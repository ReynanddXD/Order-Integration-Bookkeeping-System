<?php
$conn = new mysqli("localhost", "root", "", "integrasi");
$result = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");

while($row = $result->fetch_assoc()) {
  echo '<div class="order-card">';
  echo '<div class="product-photo">Foto Produk</div>';
  echo '<div class="order-detail"><strong>' . $row["customer_name"] . '</strong><br>' . $row["product_title"] . '<br>' . $row["variation"] . '</div>';
  echo '<div class="order-info">' . $row["order_id"] . '<br>Rp ' . number_format($row["price"]) . '<br>' . $row["payment_method"] . '</div>';
  echo '<div class="status">' . $row["status"] . '</div>';
  echo '<div class="platform"><img src="' . strtolower($row["platform"]) . '.png" /></div>';
  echo '</div>';
}
?>