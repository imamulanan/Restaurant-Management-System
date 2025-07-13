<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve orders from database
$sql = "SELECT customer_name, dish_name, quantity, order_time FROM orders ORDER BY order_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List</title>
    <link rel="stylesheet" href="view_orders.css">
</head>
<body>
    <div class="container">
        <h2>Order List</h2>
        <table>
            <tr>
                <th>Customer Name</th>
                <th>Dish Name</th>
                <th>Quantity</th>
                <th>Order Time</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['customer_name']) . "</td>
                            <td>" . htmlspecialchars($row['dish_name']) . "</td>
                            <td>" . (int)$row['quantity'] . "</td>
                            <td>" . $row['order_time'] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center;'>No orders found.</td></tr>";
            }

            $conn->close();
            ?>
        </table>

        <a href="order.html">Back to Order Page</a>
    </div>
</body>
</html>
