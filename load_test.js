import http from 'k6/http';
import { sleep, check } from 'k6';

export const options = {
  stages: [
    { duration: '10s', target: 100 }, // Naik perlahan ke 100 user dalam 10 detik
    { duration: '20s', target: 500 }, // Naik drastis ke 500 user dalam 20 detik
    { duration: '30s', target: 500 }, // Tahan beban 500 user penuh selama 30 detik
    { duration: '10s', target: 0 },   // Turunkan beban kembali ke 0
  ],
};

export default function () {
  const res = http.get('http://cbt-simpel.test'); 
  
  check(res, {
    'status is 200': (r) => r.status === 200,
  });
  
  sleep(1);
}
