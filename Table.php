<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_management";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Update table status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_table"])) {
    $table_id = intval($_POST["table_id"]);
    $new_status = $conn->real_escape_string($_POST["status"]);
    $conn->query("UPDATE tables SET status = '$new_status' WHERE id = $table_id");
}

// Get all tables
$result = $conn->query("SELECT * FROM tables ORDER BY table_number ASC");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Manage Tables</title>
  <style>
  :root {
    --primary: #6c5ce7;
    --secondary: #a29bfe;
    --success: #00b894;
    --warning: #fdcb6e;
    --danger: #d63031;
    --light: #f8f9fa;
    --dark: #2d3436;
    --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  }

  body {
    font-family: 'Poppins', Arial, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 2rem;
    margin: 0;
    color: var(--dark);
  }

  h2 {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.5rem;
    color: var(--primary);
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
    position: relative;
    display: inline-block;
    left: 50%;
    transform: translateX(-50%);
  }

  h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    border-radius: 2px;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.5s ease;
  }

  body.loaded h2::after {
    transform: scaleX(1);
  }

  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
    perspective: 1000px;
  }

  .table-card {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    transition: var(--transition);
    transform-style: preserve-3d;
    position: relative;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px) rotateX(10deg);
    animation: cardEntrance 0.6s ease-out forwards;
  }

  @keyframes cardEntrance {
    to {
      opacity: 1;
      transform: translateY(0) rotateX(0);
    }
  }

  .table-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
  }

  .table-card:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
  }

  .table-card h3 {
    margin-top: 0;
    margin-bottom: 1rem;
    font-size: 1.5rem;
    color: var(--dark);
    position: relative;
    display: inline-block;
  }

  .table-card h3::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 50%;
    height: 2px;
    background: var(--secondary);
    transition: width 0.3s ease;
  }

  .table-card:hover h3::after {
    width: 100%;
  }

  .table-details {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    font-size: 0.9rem;
  }

  .table-details span {
    display: flex;
    align-items: center;
    gap: 0.3rem;
  }

  .status {
    display: inline-block;
    padding: 0.3rem 0.6rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .status-Available {
    background-color: rgba(0, 184, 148, 0.2);
    color: var(--success);
  }

  .status-Reserved {
    background-color: rgba(253, 203, 110, 0.2);
    color: var(--warning);
  }

  .status-Occupied {
    background-color: rgba(214, 48, 49, 0.2);
    color: var(--danger);
  }

  form select, form button {
    width: 100%;
    padding: 0.8rem;
    margin-top: 1rem;
    border: none;
    border-radius: 8px;
    font-family: 'Poppins', Arial, sans-serif;
    transition: var(--transition);
  }

  form select {
    background-color: white;
    border: 1px solid #e0e0e0;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    cursor: pointer;
  }

  form select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.2);
  }

  form button {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(108, 92, 231, 0.2);
    position: relative;
    overflow: hidden;
  }

  form button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: 0.5s;
  }

  form button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(108, 92, 231, 0.3);
  }

  form button:hover::before {
    left: 100%;
  }

  .table-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--primary);
    transition: transform 0.3s ease;
  }

  .table-card:hover .table-icon {
    transform: scale(1.1) rotate(5deg);
  }

  /* Responsive design */
  @media (max-width: 768px) {
    .grid {
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
    
    h2 {
      font-size: 2rem;
    }
  }

  /* Animation delays for cards */
  .table-card:nth-child(1) { animation-delay: 0.1s; }
  .table-card:nth-child(2) { animation-delay: 0.2s; }
  .table-card:nth-child(3) { animation-delay: 0.3s; }
  .table-card:nth-child(4) { animation-delay: 0.4s; }
  .table-card:nth-child(5) { animation-delay: 0.5s; }
  .table-card:nth-child(6) { animation-delay: 0.6s; }
  .table-card:nth-child(7) { animation-delay: 0.7s; }
  .table-card:nth-child(8) { animation-delay: 0.8s; }

  /* Add this script to your HTML to trigger the loaded class */
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.body.classList.add('loaded');
    });
  </script>
</style>
</head>
<body>

<h2>Table Management</h2>

<div class="grid">
  <?php while($row = $result->fetch_assoc()): ?>
    <div class="table-card">
      <h3>Table <?= $row['table_number']; ?></h3>
      <p><strong>Capacity:</strong> <?= $row['capacity']; ?> people</p>
      <p><strong>Status:</strong>
         <span class="status-<?= $row['status']; ?>"><?= $row['status']; ?></span></p>
      <form method="POST">
        <input type="hidden" name="table_id" value="<?= $row['id']; ?>">
        <select name="status">
          <option value="Available" <?= $row['status'] === 'Available' ? 'selected' : ''; ?>>Available</option>
          <option value="Reserved" <?= $row['status'] === 'Reserved' ? 'selected' : ''; ?>>Reserved</option>
          <option value="Occupied" <?= $row['status'] === 'Occupied' ? 'selected' : ''; ?>>Occupied</option>
        </select>
        <button type="submit" name="update_table">Update</button>
      </form>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>
