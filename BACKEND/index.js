// frontend/src/App.js
import React, { useEffect, useState } from 'react';
import './App.css';

function App() {
  const [orders, setOrders] = useState([]);
  const [income, setIncome] = useState(0);

  useEffect(() => {
    fetch('http://localhost:5000/api/orders')
      .then(res => res.json())
      .then(data => setOrders(data));

    fetch('http://localhost:5000/api/finance')
      .then(res => res.json())
      .then(data => setIncome(data.totalIncome));
  }, []);

  return (
    <div className="App">
      <h1>Integrasi Shopee & TikTok Shop</h1>
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
            <tr key={o.id}>
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

export default App;