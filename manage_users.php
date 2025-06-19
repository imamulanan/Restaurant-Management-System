<?php
$host = "localhost";
$dbname = "restaurant_management";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// যদি URL এর মাধ্যমে delete রিকুয়েস্ট আসা থাকে (যেমন: manage_users.php?delete=5)
if (isset($_GET['delete'])) {
    // delete প্যারামিটারটি থেকে ID নিয়ে সেটাকে পূর্ণসংখ্যায় রূপান্তর করা হচ্ছে
    $id = intval($_GET['delete']);

    // users টেবিল থেকে ওই ID অনুযায়ী ইউজার রেকর্ড মুছে ফেলা হচ্ছে
    $conn->query("DELETE FROM users WHERE id = $id");

    // ডিলিট করার পর আবার manage_users.php পেজে রিডাইরেক্ট করা হচ্ছে
    header("Location: manage_users.php");
    exit(); // স্ক্রিপ্ট execution বন্ধ করে দেয়
}

// সব ইউজারদের ডেটাবেজ থেকে নিয়ে আসা হচ্ছে এবং ID অনুযায়ী ক্রমানুসারে সাজানো হচ্ছে
$result = $conn->query("SELECT * FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Management - Restaurant Management System</title>
  <link rel="stylesheet" href="manage_users.css">
</head>
<body>

  <!-- Header -->
  <header>
    <h1>Restaurant Management System - Admin Panel</h1>
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

  <!-- User Management Container -->
  <div class="container">
    <h2>Employee Management</h2>

    <!-- Add New User -->
    <a href="add_user.php" class="add-btn">+ Add New User</a>

    <!-- User Table -->
    <table>
      <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Username</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Gender</th>
        <th>Role</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>

      <?php if ($result->num_rows > 0): ?>
    <!-- যদি ডাটাবেজ থেকে ইউজার পাওয়া যায় তাহলে লুপ চালানো হবে -->
    <?php while($row = $result->fetch_assoc()): ?> <!-- fetch_assoc() result set থেকে একটার পর একটা row নিয়ে আসে। -->
    <tr>
        <!-- প্রতিটি ইউজারের জন্য টেবিলের একটি সারি তৈরি করা হচ্ছে -->

        <!-- ইউজারের ID দেখানো হচ্ছে -->
        <td><?php echo $row['id']; ?></td>

        <!-- ইউজারের Full Name দেখানো হচ্ছে, htmlspecialchars ব্যবহার করা হয়েছে -->
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>

        <!-- ইউজারের Username -->
        <td><?php echo htmlspecialchars($row['username']); ?></td>

        <!-- ইউজারের ফোন নম্বর -->
        <td><?php echo htmlspecialchars($row['phone']); ?></td>

        <!-- ইউজারের ঠিকানা -->
        <td><?php echo htmlspecialchars($row['address']); ?></td>

        <!-- ইউজারের লিঙ্গ -->
        <td><?php echo htmlspecialchars($row['gender']); ?></td>

        <!-- ইউজারের ভূমিকা (Role), যেমন: Admin / Staff -->
        <td><?php echo htmlspecialchars($row['role']); ?></td>

        <!-- ইউজার অ্যাকাউন্ট তৈরির সময় -->
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>

        <!-- একশন বাটন: Edit ও Delete -->
        <td>
            <!-- Edit বাটনে ক্লিক করলে edit_user.php পেজে যাবে ওই ID সহ -->
            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>

            <!-- Delete বাটনে ক্লিক করলে manage_users.php পেজে যাবে delete parameter সহ, এবং confirm() দিয়ে সতর্কতা দেওয়া হচ্ছে -->
            <a href="manage_users.php?delete=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <!-- যদি কোনো ইউজার না থাকে তাহলে নিচের মেসেজ দেখাবে -->

        <tr>
          <td colspan="9">No users found.</td> <!--colspan="9" এর মানে হলো:একটি টেবিল সেল (cell) পুরো ৯টি কলাম জুড়ে থাকবে।-->
        </tr>
      <?php endif; ?>
    </table>
  </div>

  <!-- Footer -->
  <footer>
    &copy; 2025 Restaurant Management System. All rights reserved.
  </footer>

</body>
</html>
