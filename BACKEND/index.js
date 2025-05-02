// backend/index.js
const express = require('express');
const cors = require('cors');
const app = express();
const PORT = 5000;

app.use(cors());
app.use(express.json());

// Simulasi data pesanan dari Shopee dan TikTok
const orders = [
  { id: 'SP001', title: 'Baju Kaos', price: 70000, platform: 'Shopee' },
  { id: 'TT001', title: 'Jaket Hoodie', price: 120000, platform: 'TikTok' },
  { id: 'SP002', title: 'Topi Polos', price: 45000, platform: 'Shopee' },
];

// Endpoint: semua pesanan
app.get('/api/orders', (req, res) => {
  res.json(orders);
});

// Endpoint: laporan keuangan
app.get('/api/finance', (req, res) => {
  const total = orders.reduce((sum, item) => sum + item.price, 0);
  res.json({ totalIncome: total });
});

app.listen(PORT, () => {
  console.log(API berjalan di http://localhost:${PORT});
});
