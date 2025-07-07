<?php
$conn = new mysqli("localhost", "root", "", "restaurant_management");
$id = (int) $_GET['id'];
$bill = $conn->query("SELECT * FROM bills WHERE id = $id")->fetch_assoc();
$orders = $conn->query("SELECT * FROM orders WHERE customer_name = '{$bill['customer_name']}' AND DATE(order_time) = CURDATE()");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Bill #<?= $id ?></title>
  <style>
    body { font-family: Arial; background: #fff; padding: 20px; }
    .bill { max-width: 500px; margin: auto; border: 1px solid #ccc; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
    h2, h4 { text-align: center; }
    button { margin-top: 20px; padding: 10px 20px; width: 100%; }
  </style>
</head>
<body>
<div class="bill">
  <h2>Restaurant Management</h2>
  <h4>Bill #<?= $id ?></h4>
  <p><strong>Customer:</strong> <?= $bill['customer_name'] ?></p>
  <p><strong>Date:</strong> <?= $bill['bill_time'] ?></p>

  <table>
    <tr><th>Item</th><th>Qty</th><th>Price</th></tr>
    <?php while($item = $orders->fetch_assoc()): ?>
      <tr>
        <td><?= $item['dish_name'] ?></td>
        <td><?= $item['quantity'] ?></td>
        <td>
          <?php
            $price = $conn->query("SELECT price FROM menu WHERE dish_name = '{$item['dish_name']}'")->fetch_assoc()['price'];
            echo '৳' . number_format($price * $item['quantity'], 2);
          ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <h3 style="text-align:right;">Total: ৳<?= number_format($bill['total_amount'], 2) ?></h3>
  <button onclick="window.print()">🖨️ Print Bill</button>
</div>
</body>
</html>
