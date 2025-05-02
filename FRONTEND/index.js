import React, { useEffect, useState } from 'react';
import './App.css';

function App() {
  const [orders, setOrders] = useState([]);
  const [income, setIncome] = useState(0);
  const [form, setForm] = useState({ id: '', title: '', price: '', platform: '' });

  useEffect(() => {
    fetchOrders();
    fetchFinance();
  }, []);

  const fetchOrders = () => {
    fetch('http://localhost:5000/api/orders')
      .then(res => res.json())
      .then(data => setOrders(data));
  };

  const fetchFinance = () => {
    fetch('http://localhost:5000/api/finance')
      .then(res => res.json())
      .then(data => setIncome(data.totalIncome));
  };

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    fetch('http://localhost:5000/api/orders', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ ...form, price: Number(form.price) }),
    })
      .then(() => {
        fetchOrders();
        fetchFinance();
        setForm({ id: '', title: '', price: '', platform: '' });
      });
  };

  return (
    <div className="App">
      <h1>Integrasi Shopee & TikTok Shop</h1>

      <form onSubmit={handleSubmit} style={{ marginBottom: '30px' }}>
        <input name="id" placeholder="ID" value={form.id} onChange={handleChange} required />
        <input name="title" placeholder="Judul Barang" value={form.title} onChange={handleChange} required />
        <input name="price" placeholder="Harga" type="number" value={form.price} onChange={handleChange} required />
        <select name="platform" value={form.platform} onChange={handleChange} required>
          <option value="">Pilih Platform</option>
          <option value="Shopee">Shopee</option>
          <option value="TikTok">TikTok</option>
        </select>
        <button type="submit">Tambah Pesanan</button>
      </form>

      <table border="1" cellPadding="10" style={{ margin: 'auto' }}>
        <thead>
          <tr>
            <th>ID</th>
            <th>Judul Barang</th>
            <th>Harga</th>
            <th>Platform</th>
          </tr>
        </thead>
        <tbody>
          {orders.map((o) => (
            <tr key={o._id}>
              <td>{o.id}</td>
              <td>{o.title}</td>
              <td>Rp {o.price.toLocaleString()}</td>
              <td>{o.platform}</td>
            </tr>
          ))}
        </tbody>
      </table>
      <h2 style={{ textAlign: 'center' }}>
        Total Pendapatan: Rp {income.toLocaleString()}
      </h2>
    </div>
  );
}

export default App;