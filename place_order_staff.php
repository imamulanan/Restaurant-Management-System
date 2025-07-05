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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ইউজার ইনপুট সংগ্রহ করে তা স্যানিটাইজ করা হচ্ছে যাতে SQL Injection থেকে রক্ষা পাওয়া যায়

    // $conn->real_escape_string হল PHP-এর MySQLi এক্সটেনশনের একটি মেথড, যা ডাটাবেজে ইনসার্ট বা কোয়েরি করার সময় ইউজার ইনপুটের স্পেশাল ক্যারেক্টারগুলোকে নিরাপদ করে তোলে।এটি এমন একটি ফাংশন যা ইনপুটের বিশেষ চিহ্নগুলো (যেমন ', ", \, ইত্যাদি) এমনভাবে পরিবর্তন করে দেয়, যাতে সেগুলো SQL Injection আক্রমণ থেকে রক্ষা পায়।
    
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $dish_name = $conn->real_escape_string($_POST['dish_name']);
    $quantity = (int)$_POST['quantity'];// কুয়ান্টিটি একটি পূর্ণসংখ্যা হিসেবে নেওয়া হচ্ছে

    // Insert into the database
    $sql = "INSERT INTO orders (customer_name, dish_name, quantity)
            VALUES ('$customer_name', '$dish_name', $quantity)";
    // কোয়েরিটি সফলভাবে চললে কনফার্মেশন পেজ দেখানো হবে
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

                <!-- ইনপুটগুলো নিরাপদভাবে HTML-এ দেখানো হচ্ছে -->
                <p><strong>Customer:</strong> <?= htmlspecialchars($customer_name) ?></p>
                <p><strong>Dish:</strong> <?= htmlspecialchars($dish_name) ?></p>
                <p><strong>Quantity:</strong> <?= $quantity ?></p>
                <!-- নতুন অর্ডার বা সব অর্ডার দেখার লিংক -->
                <a href="order_staf.html">Place Another Order</a><br>
                <a href="view_orders_staff.php">View All Orders</a>
            </div>
        </body>
        </html>
        <?php
    } else {
        // যদি ইনসার্টে সমস্যা হয় তাহলে এরর মেসেজ দেখানো হবে
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
