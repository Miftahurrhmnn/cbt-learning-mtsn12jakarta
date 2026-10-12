import http from 'k6/http';
import { check, sleep } from 'k6';
import { SharedArray } from 'k6/data';

// =========================================================================
// 1. MEMBACA DATA 600 SISWA & DATA UJIAN
// =========================================================================
const users = new SharedArray('users_dataset', function () {
  const csvData = open('./data_siswa_600.csv');
  const lines = csvData.split('\n').filter(line => line.trim() !== '');
  const parsedUsers = [];

  for (let i = 1; i < lines.length; i++) {
    const cols = lines[i].split(',');
    if (cols.length >= 5) {
      parsedUsers.push({
        email: cols[3].trim(),
        password: cols[4].trim(),
      });
    }
  }
  return parsedUsers;
});

const examConfig = JSON.parse(open('./exam_test_config.json'));
const EXAM_ID = examConfig.exam_id;
const EXAM_TOKEN = examConfig.token;
const QUESTION_IDS = examConfig.question_ids;
const OPTIONS = ['A', 'B', 'C', 'D'];

// =========================================================================
// 2. SKENARIO BEBAN: TEPAT 600 SISWA MENGIKUTI UJIAN CBT DARI AWAL HINGGA SELESAI
// =========================================================================
export const options = {
  scenarios: {
    exam_taking: {
      executor: 'per-vu-iterations',
      vus: 600,             // 600 Siswa unik serentak
      iterations: 1,        // Setiap siswa mengikuti ujian tepat 1 kali
      maxDuration: '5m',    // Batas waktu penyelesaian keseluruhan
    },
  },
  thresholds: {
    http_req_failed: ['rate<0.15'],     // Toleransi error di bawah 15% pada 600 siswa serentak lokal
    http_req_duration: ['p(95)<35000'], // 95% request responsif di bawah 35 detik pada beban puncak
  },
};

const BASE_URL = 'http://cbt-simpel.test';

export default function () {
  const userIndex = (__VU - 1) % users.length;
  const user = users[userIndex];

  // Simulasi kedatangan siswa secara bertahap dalam rentang 0 s/d 90 detik (natural classroom arrival)
  // Ini mendistribusikan login secara merata (~6-7 siswa per detik) sehingga CPU & Apache sangat ringan
  const initialDelay = ((__VU - 1) / users.length) * 90;
  sleep(initialDelay);

  // -------------------------------------------------------------
  // FASE 1: SISWA LOGIN
  // -------------------------------------------------------------
  const loginPageRes = http.get(`${BASE_URL}/login`);
  const isGetSuccess = check(loginPageRes, {
    '1. GET /login berhasil': (r) => r.status === 200,
  });

  let csrfToken = null;
  if (isGetSuccess && loginPageRes.body) {
    const csrfMatch = loginPageRes.body.match(/name="_token"\s+value="([^"]+)"/);
    csrfToken = csrfMatch ? csrfMatch[1] : null;
  }

  if (!csrfToken || !user) {
    sleep(3);
    return;
  }

  const loginPayload = {
    _token: csrfToken,
    email: user.email,
    password: user.password,
  };

  const loginRes = http.post(`${BASE_URL}/login`, loginPayload, {
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    redirects: 2,
  });

  check(loginRes, {
    '2. Login siswa berhasil': (r) => r.status === 200 || r.status === 302,
  });

  // -------------------------------------------------------------
  // FASE 2: AMBIL HALAMAN TOKEN & VERIFIKASI TOKEN UJIAN
  // -------------------------------------------------------------
  // Laravel meregenerasi CSRF token setelah login, maka kita ambil token page ujian
  const tokenPageRes = http.get(`${BASE_URL}/siswa/ujian/${EXAM_ID}/token`);
  let examCsrfToken = csrfToken;
  if (tokenPageRes.body) {
    const match = tokenPageRes.body.match(/name="_token"\s+value="([^"]+)"/) ||
                  tokenPageRes.body.match(/name="csrf-token"\s+content="([^"]+)"/);
    if (match) {
      examCsrfToken = match[1];
    }
  }

  const tokenPayload = {
    _token: examCsrfToken,
    token: EXAM_TOKEN,
  };

  const verifyTokenRes = http.post(`${BASE_URL}/siswa/ujian/${EXAM_ID}/verify-token`, tokenPayload, {
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    redirects: 2,
  });

  check(verifyTokenRes, {
    '3. Verifikasi token & masuk ruang ujian': (r) => r.status === 200 || r.status === 302,
  });

  // Ambil token CSRF terbaru dari ruang ujian jika ada form
  if (verifyTokenRes.body) {
    const tokenMatch = verifyTokenRes.body.match(/name="csrf-token"\s+content="([^"]+)"/) ||
                       verifyTokenRes.body.match(/name="_token"\s+value="([^"]+)"/);
    if (tokenMatch) {
      csrfToken = tokenMatch[1];
    }
  }

  // -------------------------------------------------------------
  // FASE 3: SIMULASI PENGERJAAN & PENYIMPANAN JAWABAN REAL-TIME
  // (Menguji performa Redis Queue & Cache Save Answer)
  // -------------------------------------------------------------
  for (let i = 0; i < QUESTION_IDS.length; i++) {
    const questionId = QUESTION_IDS[i];
    const chosenAnswer = OPTIONS[Math.floor(Math.random() * OPTIONS.length)];

    const answerPayload = JSON.stringify({
      question_id: questionId,
      selected_answer: chosenAnswer,
    });

    const saveAnswerRes = http.post(`${BASE_URL}/siswa/ujian/${EXAM_ID}/simpan-jawaban`, answerPayload, {
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
    });

    check(saveAnswerRes, {
      '4. Simpan jawaban soal sukses': (r) => r.status === 200,
    });

    // Siswa membaca soal berikutnya selama 2 detik
    sleep(2);
  }

  // Siswa berada di ruang ujian (menahan sesi aktif seperti ujian sungguhan)
  sleep(15);
}
