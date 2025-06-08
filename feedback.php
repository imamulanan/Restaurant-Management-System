<?php
// Start session if needed
session_start();

// Database connection settings
$servername = "localhost";
$db_username = "root";        // Change if needed
$db_password = "";            // Change if needed
$dbname = "restaurant_management";  // Your database name

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
//($_SERVER["REQUEST_METHOD"] === "POST") এই লাইনটি PHP-তে চেক করে যে, ফর্মটি POST method দিয়ে সাবমিট করা হয়েছে কিনা।
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ইউজারের ইনপুটগুলো স্যানিটাইজ করা হচ্ছে (অপ্রয়োজনীয় স্পেস বাদ দিয়ে ও SQL ইনজেকশন থেকে রক্ষা করার জন্য)
    $name = $conn->real_escape_string(trim($_POST['customer_name']));
    $email = $conn->real_escape_string(trim($_POST['customer_email']));
    $rating = (int) $_POST['rating']; // রেটিং ইন্টিজার টাইপে কনভার্ট করা হচ্ছে
    $feedback = $conn->real_escape_string(trim($_POST['feedback']));

    // সাধারণ ভ্যালিডেশন – যদি সব ফিল্ড খালি না হয়
    if (!empty($name) && !empty($email) && !empty($rating) && !empty($feedback)) {
        
        // SQL Injection থেকে রক্ষা করার জন্য Prepared Statement ব্যবহার করা হচ্ছে
        $stmt = $conn->prepare("INSERT INTO feedback (customer_name, customer_email, rating, feedback) VALUES (?, ?, ?, ?)");
        
        // ইনপুট ভ্যালুগুলো Bind করা হচ্ছে স্টেটমেন্টের সাথে — "s" = string, "i" = integer
        $stmt->bind_param("ssis", $name, $email, $rating, $feedback);

        // স্টেটমেন্ট এক্সিকিউট করে দেখা হচ্ছে ইনসার্ট সফল হয়েছে কিনা
        if ($stmt->execute()) {
            $message = "Thank you for your feedback!"; // সফল হলে ধন্যবাদ বার্তা
        } else {
            $message = "Error saving feedback: " . $stmt->error; // না হলে এরর বার্তা
        }

        // স্টেটমেন্ট ক্লোজ করে দেওয়া হচ্ছে
        $stmt->close();
    } else {
        // যদি কোনো ফিল্ড খালি থাকে তাহলে ইউজারকে জানানো হচ্ছে
        $message = "Please fill in all required fields.";
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Feedback - Restaurant Management System</title>
  <link rel="stylesheet" href="feedback.css" />
</head>
<body>

  <!-- Header -->
  <header>
    <h1>Restaurant Management System</h1>
  </header>

  <!-- Navigation -->
  <nav>
    <a href="dashboard.html">Dashboard</a>
    <a href="order.html">Place Order</a>
    <a href="view_orders.php">Orders</a>
    <a href="menu_list.php">Menu Management</a>
    <a href="sales_report.html">Sales Report</a>
    <a href="manage_users.php">User Management</a>
    <a href="feedback.php">Feedback</a>
    <a href="logout.php">Logout</a>
  </nav>

  <!-- Feedback Form -->
  <div class="container">
    <h2>Customer Feedback</h2>

    <?php if (!empty($message)): ?>
   <!-- যদি $message ভেরিয়েবলটি খালি না হয়, তাহলে নিচের ডিভটি দেখানো হবে -->
   <div class="message"><?php echo htmlspecialchars($message); ?></div>
   <?php endif; ?>


    <form action="" method="post">
      <label for="customer_name">Your Name:</label>
      <input type="text" id="customer_name" name="customer_name" placeholder="Enter your name" required />

      <label for="customer_email">Email Address:</label>
      <input type="email" id="customer_email" name="customer_email" placeholder="Enter your email" required />

      <label for="rating">Rating:</label>
      <select id="rating" name="rating" required>
        <option value="">-- Select Rating --</option>
        <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
        <option value="4">⭐⭐⭐⭐ Very Good</option>
        <option value="3">⭐⭐⭐ Good</option>
        <option value="2">⭐⭐ Fair</option>
        <option value="1">⭐ Poor</option>
      </select>

      <label for="feedback">Your Feedback:</label>
      <textarea id="feedback" name="feedback" placeholder="Write your feedback here..." required></textarea>

      <input type="submit" value="Submit Feedback">
    </form>
  </div>

  <footer>
    &copy; 2024 Restaurant Management System. All rights reserved.
  </footer>

</body>
</html>
