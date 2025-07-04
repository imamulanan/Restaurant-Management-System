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

// Fetch tables
$tables = $conn->query("SELECT * FROM tables ORDER BY table_number ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place Order</title>
    <link rel="stylesheet" href="order.css">
</head>
<body>
    <div class="container">
        <h2>Place Your Order</h2>
        <form action="place_order.php" method="post" id="orderForm">
            <label for="customer_name">Customer Name:</label>
            <input type="text" name="customer_name" required>

            <label for="table_id">Select Table:</label>
            <select name="table_id" required>
                <option value="">-- Select Table --</option>
                <?php while ($row = $tables->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>">Table <?= $row['table_number'] ?> (<?= $row['status'] ?>)</option>
                <?php endwhile; ?>
            </select>

            <div id="order-items">
                <div class="order-item">
                    <label>Dish:</label>
                    <select name="dish_name[]" class="dish-select" required>
                        <option value="" data-price="0">-- Select Dish --</option>
                        <?php while ($dish = $menu_items->fetch_assoc()): ?>
                            <option value="<?= $dish['dish_name'] ?>" data-price="<?= $dish['price'] ?>">
                                <?= $dish['dish_name'] ?> - ৳<?= $dish['price'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <label>Quantity:</label>
                    <input type="number" name="quantity[]" min="1" value="1" required>
                </div>
            </div>

            <button type="button" onclick="addItem()">+ Add Another Dish</button>
            <input type="submit" value="Place Order">
        </form>

        <a href="dashboard.html" class="back-button">Back to Dashboard</a>
    </div>

    <script>
        function addItem() {
            const item = document.querySelector('.order-item').cloneNode(true);
            item.querySelector('select').selectedIndex = 0;
            item.querySelector('input').value = 1;
            document.getElementById('order-items').appendChild(item);
        }
    </script>
</body>
</html>
