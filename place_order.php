<?php
// Database connection settings
$servername = "localhost";
$username = "root";// default username for localhost, change if needed
$password = ""; // default password for localhost, change if needed
$dbname = "restaurant_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    // $conn->real_escape_string হল PHP-এর MySQLi এক্সটেনশনের একটি মেথড, যা ডাটাবেজে ইনসার্ট বা কোয়েরি করার সময় ইউজার ইনপুটের স্পেশাল ক্যারেক্টারগুলোকে নিরাপদ করে তোলে।এটি এমন একটি ফাংশন যা ইনপুটের বিশেষ চিহ্নগুলো (যেমন ', ", \, ইত্যাদি) এমনভাবে পরিবর্তন করে দেয়, যাতে সেগুলো SQL Injection আক্রমণ থেকে রক্ষা পায়।
    $dish_name = $conn->real_escape_string($_POST['dish_name']);
    $quantity = (int)$_POST['quantity'];

    // Insert into the database ডেটা সরাসরি SQL স্টেটমেন্টে বসানো হয়।
    //যদি ইউজার ইনপুটে ম্যালিশাস কোড থাকে, তাহলে এটি SQL Injection ঝুঁকিপূর্ণ।
    $sql = "INSERT INTO orders (customer_name, dish_name, quantity)
            VALUES ('$customer_name', '$dish_name', $quantity)";

    if ($conn->query($sql) === TRUE) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Order Confirmation</title>
            <link rel="stylesheet" href="place_order.css">
        </head>
        <body>
            <div class="confirmation-container">
                <h2>Order Placed Successfully!</h2>
                <p><strong>Customer:</strong> <?= htmlspecialchars($customer_name) ?></p>
                <p><strong>Dish:</strong> <?= htmlspecialchars($dish_name) ?></p>
                <p><strong>Quantity:</strong> <?= $quantity ?></p>
                <a href="order.html">Place Another Order</a><br>
                <a href="view_orders.php">View All Orders</a>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
