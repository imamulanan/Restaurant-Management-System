<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_management";
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch menu items
$menu_items = $conn->query("SELECT * FROM menu WHERE availability = 'Available'");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $customer_name = $_POST['customer_name'];
  $items = $_POST['dish'];
  $quantities = $_POST['quantity'];
  $total = 0;

  foreach ($items as $index => $dish_id) {
    $dish_id = (int)$dish_id;
    $quantity = (int)$quantities[$index];

    $dish = $conn->query("SELECT * FROM menu WHERE id = $dish_id")->fetch_assoc();
    $price = $dish['price'];
    $total += $price * $quantity;

    // Save to orders table
    $stmt = $conn->prepare("INSERT INTO orders (customer_name, dish_name, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $customer_name, $dish['dish_name'], $quantity);
    $stmt->execute();
  }

  // Save bill
  $stmt = $conn->prepare("INSERT INTO bills (customer_name, total_amount) VALUES (?, ?)");
  $stmt->bind_param("sd", $customer_name, $total);
  $stmt->execute();

  $bill_id = $conn->insert_id;
  header("Location: print_bill.php?id=$bill_id");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Generate Bill</title>
  <style>
  :root {
  --primary-color: #4CAF50;
  --primary-light: #81C784;
  --primary-dark: #388E3C;
  --primary-hover: #45a049;
  --secondary-color: #FF9800;
  --background-color: #f8f9fa;
  --card-color: #ffffff;
  --text-color: #333333;
  --text-light: #666;
  --border-color: #e0e0e0;
  --border-radius: 12px;
  --border-radius-sm: 8px;
  --box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
  --box-shadow-hover: 0 15px 30px rgba(0, 0, 0, 0.12);
  --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  --transition-fast: all 0.15s ease-out;
}

body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  padding: 20px;
  background-color: var(--background-color);
  color: var(--text-color);
  line-height: 1.6;
  background-image: radial-gradient(circle at 10% 20%, rgba(129, 199, 132, 0.05) 0%, rgba(129, 199, 132, 0.05) 90%);
}

.container {
  background: var(--card-color);
  padding: 30px;
  border-radius: var(--border-radius);
  max-width: 600px;
  margin: 40px auto;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
  border: 1px solid var(--border-color);
  position: relative;
  overflow: hidden;
}

.container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: linear-gradient(to bottom, var(--primary-color), var(--primary-light));
  transition: var(--transition);
}

.container:hover {
  box-shadow: var(--box-shadow-hover);
  transform: translateY(-3px);
}

.container:hover::before {
  width: 6px;
}

h2 {
  text-align: center;
  color: var(--primary-dark);
  margin-bottom: 30px;
  position: relative;
  padding-bottom: 15px;
  font-weight: 700;
  letter-spacing: -0.5px;
}

h2::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 4px;
  background: linear-gradient(to right, var(--primary-light), var(--primary-color), var(--primary-light));
  border-radius: 2px;
}

label {
  display: block;
  margin-top: 20px;
  font-weight: 500;
  color: var(--text-light);
  font-size: 14px;
  transition: var(--transition-fast);
}

.input-group {
  position: relative;
  margin-top: 8px;
}

.input-group::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background: var(--primary-color);
  transition: var(--transition);
}

.input-group:focus-within::after {
  width: 100%;
}

input, select {
  width: 100%;
  padding: 14px 12px;
  border: 1px solid var(--border-color);
  border-radius: var(--border-radius-sm);
  font-size: 15px;
  transition: var(--transition);
  box-sizing: border-box;
  background-color: rgba(255, 255, 255, 0.8);
  color: var(--text-color);
}

input:focus, select:focus {
  outline: none;
  border-color: var(--primary-light);
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.15);
  background-color: #fff;
}

.dish-row {
  display: flex;
  gap: 15px;
  margin-top: 15px;
  align-items: center;
}

.dish-row select, .dish-row input {
  flex: 1;
}

button {
  margin-top: 30px;
  padding: 15px;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
  border: none;
  border-radius: var(--border-radius-sm);
  cursor: pointer;
  width: 100%;
  font-size: 16px;
  font-weight: 600;
  letter-spacing: 0.5px;
  transition: var(--transition);
  text-transform: uppercase;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 6px rgba(76, 175, 80, 0.2);
}

button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: 0.5s;
}

button:hover {
  transform: translateY(-3px);
  box-shadow: 0 7px 14px rgba(76, 175, 80, 0.3);
}

button:hover::before {
  left: 100%;
}

button:active {
  transform: translateY(1px);
  box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
}

/* Floating label effect */
.input-group.focused label {
  transform: translateY(-25px);
  font-size: 12px;
  color: var(--primary-color);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .container {
    padding: 25px;
    margin: 20px 15px;
  }
  
  .dish-row {
    flex-direction: column;
    gap: 10px;
  }
  
  .dish-row select, .dish-row input {
    width: 100%;
  }
}

@media (max-width: 480px) {
  body {
    padding: 10px;
  }
  
  .container {
    padding: 20px;
    border-radius: var(--border-radius-sm);
  }
  
  h2 {
    font-size: 1.5rem;
  }
  
  input, select, button {
    padding: 12px;
  }
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-5px);
  }
}

.container > * {
  animation: fadeInUp 0.6s cubic-bezier(0.22, 0.61, 0.36, 1) forwards;
  opacity: 0;
}

/* Sequential animations with increasing delay */
.container > *:nth-child(1) { animation-delay: 0.1s; }
.container > *:nth-child(2) { animation-delay: 0.2s; }
.container > *:nth-child(3) { animation-delay: 0.3s; }
.container > *:nth-child(4) { animation-delay: 0.4s; }
.container > *:nth-child(5) { animation-delay: 0.5s; }

/* Hover effects for interactive elements */
input:hover, select:hover {
  border-color: #bbb;
}

/* Special styling for select dropdown */
select {
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 15px;
}

/* Loading state for button */
button.loading {
  position: relative;
  color: transparent;
}

button.loading::after {
  content: '';
  position: absolute;
  width: 20px;
  height: 20px;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  margin: auto;
  border: 3px solid transparent;
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>

<script>
  function addDishRow() {
    const container = document.getElementById('dish-container');
    const newRow = container.firstElementChild.cloneNode(true);
    newRow.querySelector('select').selectedIndex = 0;
    newRow.querySelector('input').value = '';
    newRow.style.opacity = 0;
    container.appendChild(newRow);
    
    // Add animation
    setTimeout(() => {
      newRow.style.opacity = 1;
    }, 10);
    
    // Add remove functionality
    const removeBtn = newRow.querySelector('.remove-dish');
    removeBtn.style.display = 'flex';
    removeBtn.onclick = function() {
      newRow.style.opacity = 0;
      newRow.style.transform = 'translateX(20px)';
      setTimeout(() => {
        newRow.remove();
      }, 300);
    };
  }
  
  // Initialize first remove button
  document.addEventListener('DOMContentLoaded', function() {
    const firstRemoveBtn = document.querySelector('.remove-dish');
    if (firstRemoveBtn) {
      firstRemoveBtn.style.display = 'none';
    }
  });
</script>
</head>
<body>
<div class="container">
  <h2>Restaurant Billing</h2>
  <form method="POST">
    <label>Customer Name:</label>
    <input type="text" name="customer_name" required>

    <label>Order Items:</label>
    <div id="dish-container">
      <div class="dish-row">
        <select name="dish[]">
          <?php while($row = $menu_items->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= $row['dish_name'] ?> (৳<?= $row['price'] ?>)</option>
          <?php endwhile; ?>
        </select>
        <input type="number" name="quantity[]" placeholder="Qty" min="1" value="1" required>
      </div>
    </div>

    <button type="button" onclick="addDishRow()">+ Add More</button>
    <button type="submit">Generate Bill</button>
  </form>
</div>
</body>
</html>
