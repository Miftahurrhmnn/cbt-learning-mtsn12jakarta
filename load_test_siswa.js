import http from 'k6/http';
import { check, sleep } from 'k6';
import { SharedArray } from 'k6/data';

// 1. Membaca data 600 siswa dari CSV
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

// 2. Skenario: 600 Siswa masuk bertahap lalu serentak berada di aplikasi
export const options = {
  stages: [
    { duration: '30s', target: 200 }, // 200 siswa login di 30 detik pertama
    { duration: '30s', target: 500 }, // Bertambah hingga 500 siswa
    { duration: '30s', target: 600 }, // Puncak 600 siswa masuk semua
    { duration: '2m',  target: 600 }, // Tahan 600 siswa serentak aktif di sistem selama 2 menit
    { duration: '30s', target: 0 },   // Sesi selesai bertahap
  ],
  thresholds: {
    http_req_failed: ['rate<0.05'],    // Tingkat kegagalan di bawah 5%
    http_req_duration: ['p(95)<3000'], // 95% request di bawah 3 detik
  },
};

const BASE_URL = 'http://cbt-simpel.test';

export default function () {
  const userIndex = (__VU - 1) % users.length;
  const user = users[userIndex];

  // 1. Ambil halaman login dan CSRF Token
  const loginPageRes = http.get(`${BASE_URL}/login`);
  const isGetSuccess = check(loginPageRes, {
    'GET /login berhasil (200)': (r) => r.status === 200,
  });

  let csrfToken = null;
  if (isGetSuccess && loginPageRes.body) {
    const csrfMatch = loginPageRes.body.match(/name="_token"\s+value="([^"]+)"/);
    csrfToken = csrfMatch ? csrfMatch[1] : null;
  }

  // 2. Siswa Login ke Akun masing-masing
  if (csrfToken && user) {
    const payload = {
      _token: csrfToken,
      email: user.email,
      password: user.password,
    };

    const loginRes = http.post(`${BASE_URL}/login`, payload, {
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      redirects: 2,
    });

    check(loginRes, {
      'Siswa berhasil login & masuk dashboard': (r) => r.status === 200 || r.status === 302,
    });

    // 3. Siswa berada di sistem: simulasi refresh / melihat jadwal ujian
    sleep(5);

    const dashboardRes = http.get(`${BASE_URL}/siswa/dashboard`);
    check(dashboardRes, {
      'Akses dashboard ujian lancar (200)': (r) => r.status === 200,
    });
  }

  // Jeda waktu siswa aktif
  sleep(10);
}