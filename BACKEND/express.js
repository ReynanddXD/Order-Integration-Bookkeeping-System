// server/index.js
const express = require('express');
const cors = require('cors');
const app = express();
const PORT = 5000;

app.use(cors());

// Endpoint Pesanan dari Shopee & Tokopedia (Dummy)
app.get('/api/orders', (req, res) => {
  res.json([
    {
      username: 'KinayShopee',
      product: 'Kaos Polos',
      price: 45000,
      payment: 'ShopeePay',
      status: 'Siap Dikirim',
      shipping: 'JNE',
      platform_logo: 'https://cdn-icons-png.flaticon.com/512/825/825454.png',
      image: 'https://via.placeholder.com/64'
    },
    {
      username: 'KinayToko',
      product: 'Topi Pria',
      price: 50000,
      payment: 'OVO',
      status: 'Dikirim',
      shipping: 'SiCepat',
      platform_logo: 'https://cdn-icons-png.flaticon.com/512/5968/5968534.png',
      image: 'https://via.placeholder.com/64'
    }
  ]);
});

// Endpoint Saldo
app.get('/api/balance', (req, res) => {
  res.json({ total: 100000000 });
});

// Endpoint Laporan
app.get('/api/report', (req, res) => {
  res.json([
    { date: '2025-05-01', total: 300000, platform: 'Shopee' },
    { date: '2025-05-02', total: 200000, platform: 'Tokopedia' }
  ]);
});

app.listen(PORT, () => {
  console.log(`Server running at http://localhost:${PORT}`);
});