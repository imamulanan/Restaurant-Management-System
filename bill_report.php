<?php
// Database configuration
$host = "localhost";
$dbname = "restaurant_management";
$username = "root";
$password = "";

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Filtering with prepared statements
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $where = "";
    $params = [];

    switch ($filter) {
        case 'daily':
            $where = "WHERE DATE(order_time) = CURDATE()";
            break;
        case 'weekly':
            $where = "WHERE YEARWEEK(order_time, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'monthly':
            $where = "WHERE MONTH(order_time) = MONTH(CURDATE()) AND YEAR(order_time) = YEAR(CURDATE())";
            break;
    }

    $sql = "SELECT * FROM orders $where ORDER BY order_time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Restaurant Bill Report</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --primary: #6c5ce7;
      --primary-light: #a29bfe;
      --secondary: #00cec9;
      --success: #00b894;
      --warning: #fdcb6e;
      --danger: #d63031;
      --light: #f8f9fa;
      --dark: #2d3436;
      --gray: #dfe6e9;
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
      --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
      --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #dfe6e9 100%);
      min-height: 100vh;
      padding: 2rem;
      color: var(--dark);
      line-height: 1.6;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
    }

    .header {
      text-align: center;
      margin-bottom: 2.5rem;
      position: relative;
    }

    h1 {
      font-size: 2.5rem;
      color: var(--primary);
      margin-bottom: 0.5rem;
    }

    .subtitle {
      color: var(--dark);
      font-weight: 400;
      opacity: 0.8;
    }

    .divider {
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      border-radius: 2px;
      width: 100px;
      margin: 1rem auto;
    }

    .filter-container {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 2rem;
      box-shadow: var(--shadow-md);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .filter-form {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .filter-label {
      font-weight: 500;
      color: var(--dark);
    }

    .filter-select {
      padding: 0.6rem 1rem;
      border: 2px solid var(--gray);
      border-radius: 8px;
      font-family: 'Poppins', sans-serif;
      background-color: white;
      transition: var(--transition);
      cursor: pointer;
    }

    .filter-select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2);
    }

    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: var(--shadow-md);
      transition: var(--transition);
      text-align: center;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-lg);
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: var(--dark);
      opacity: 0.7;
      font-size: 0.9rem;
    }

    .table-container {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: var(--shadow-md);
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 800px;
    }

    th {
      background-color: var(--primary);
      color: white;
      padding: 1rem;
      text-align: left;
      font-weight: 500;
    }

    td {
      padding: 1rem;
      border-bottom: 1px solid var(--gray);
    }

    tr:last-child td {
      border-bottom: none;
    }

    tr:hover {
      background-color: rgba(108, 92, 231, 0.05);
    }

    .status {
      display: inline-block;
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 500;
    }

    .status-pending {
      background-color: rgba(253, 203, 110, 0.2);
      color: var(--warning);
    }

    .status-completed {
      background-color: rgba(0, 184, 148, 0.2);
      color: var(--success);
    }

    .action-btn {
      padding: 0.5rem 1rem;
      border-radius: 8px;
      font-family: 'Poppins', sans-serif;
      font-weight: 500;
      cursor: pointer;
      transition: var(--transition);
      border: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .print-btn {
      background-color: var(--primary);
      color: white;
    }

    .print-btn:hover {
      background-color: var(--primary-light);
      transform: translateY(-2px);
    }

    .no-data {
      text-align: center;
      padding: 2rem;
      color: var(--dark);
      opacity: 0.7;
    }

    @media (max-width: 768px) {
      .filter-container {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .filter-form {
        width: 100%;
      }
      
      .filter-select {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Restaurant Bill Report</h1>
      <p class="subtitle">View and manage customer bills</p>
      <div class="divider"></div>
    </div>

    <div class="filter-container">
      <form method="get" class="filter-form">
        <span class="filter-label">Filter by:</span>
        <select name="filter" class="filter-select" onchange="this.form.submit()">
          <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Orders</option>
          <option value="daily" <?= $filter === 'daily' ? 'selected' : '' ?>>Today</option>
          <option value="weekly" <?= $filter === 'weekly' ? 'selected' : '' ?>>This Week</option>
          <option value="monthly" <?= $filter === 'monthly' ? 'selected' : '' ?>>This Month</option>
        </select>
      </form>
      
      <div>
        <?php 
        $totalOrders = count($orders);
        $totalAmount = array_reduce($orders, function($carry, $order) {
          return $carry + ($order['total_amount'] ?? 0);
        }, 0);
        ?>
        <span style="font-weight: 500;">Showing <?= $totalOrders ?> orders (৳<?= number_format($totalAmount, 2) ?>)</span>
      </div>
    </div>

    <div class="stats-cards">
      <div class="stat-card">
        <div class="stat-value"><?= $totalOrders ?></div>
        <div class="stat-label">Total Orders</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">৳<?= number_format($totalAmount, 2) ?></div>
        <div class="stat-label">Total Revenue</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">৳<?= $totalOrders > 0 ? number_format($totalAmount/$totalOrders, 2) : '0.00' ?></div>
        <div class="stat-label">Average Order</div>
      </div>
    </div>

    <div class="table-container">
      <?php if (count($orders) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Table</th>
              <th>Items</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Order Time</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td>#<?= $order['id'] ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= $order['table_number'] ?? 'N/A' ?></td>
                <td><?= $order['dish_name'] ?></td>
                <td>৳<?= number_format($order['total_amount'] ?? 0, 2) ?></td>
                <td>
                  <span class="status status-<?= strtolower($order['status'] ?? 'pending') ?>">
                    <?= $order['status'] ?? 'Pending' ?>
                  </span>
                </td>
                <td><?= date('M j, Y g:i A', strtotime($order['order_time'])) ?></td>
                <td>
                  <button class="action-btn print-btn" onclick="printBill(<?= $order['id'] ?>)">
                    <i class="fas fa-print"></i> Print
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="no-data">
          <i class="fas fa-clipboard-list" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
          <p>No orders found for the selected filter</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function printBill(orderId) {
      const url = `print_bill.php?id=${orderId}`;
      const win = window.open(url, '_blank', 'width=800,height=600');
      win.focus();
    }

    // Auto-refresh every 60 seconds for real-time updates
    setTimeout(() => {
      window.location.reload();
    }, 60000);
  </script>
</body>
</html>