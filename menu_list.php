<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_management";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM menu ORDER BY id DESC";
// 'menu' টেবিল থেকে সব কলাম (*) নির্বাচন করো এবং রেকর্ডগুলো id এর বিপরীত ক্রমে সাজাও (বড় id থেকে ছোট id)।

$result = $conn->query($sql);
// ডাটাবেজ কানেকশন $conn দিয়ে উপরের SQL কমান্ডটি চালাও এবং ফলাফল $result এ সংরক্ষণ করো।

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Menu Management - Restaurant Management System</title>
  <link rel="stylesheet" href="menu_list.css">
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
    <a href="manage_users.php">Employee Management</a>
    <a href="feedback.php">Feedback</a>
    <a href="logout.php">Logout</a>
  </nav>

  <div class="container">
    <h2>Menu Management</h2>
    <a href="add_menu_item.html" class="add-btn">+ Add New Dish</a>

    <table>
      <tr>
        <th>ID</th>
        <th>Dish Name</th>
        <th>Category</th>
        <th>Price ($)</th>
        <th>Availability</th>
        <th>Actions</th>
      </tr>

      <?php
      // যদি মেনু টেবিল থেকে পাওয়া রেজাল্টে অন্তত ১টি রো থাকে:
      if ($result->num_rows > 0) 
      {
        // প্রতিটি রো $row হিসেবে লুপের মাধ্যমে বের করা হচ্ছে
          while($row = $result->fetch_assoc())// fetch_assoc() result set থেকে একটার পর একটা row নিয়ে আসে। 
          {
            // প্রতিটি রো-এর জন্য একটি HTML <tr> (টেবিল রো) তৈরি করা হচ্ছে
              echo "<tr>";
              // id কলামের মান একটি <td> (টেবিল ডাটা) এর মধ্যে দেখানো হচ্ছে

              echo "<td>" . $row['id'] . "</td>";

              // dish_name কলামের মান HTML special characters থেকে সুরক্ষিত করে দেখানো হচ্ছে
              echo "<td>" . htmlspecialchars($row['dish_name']) . "</td>";
              echo "<td>" . htmlspecialchars($row['category']) . "</td>";
              echo "<td>" . number_format($row['price'], 2) . "</td>";
              echo "<td>" . $row['availability'] . "</td>";
              // প্রতিটি রো-এর জন্য একটি Edit লিংক দেওয়া হচ্ছে যাতে edit_menu_item_staff.php ফাইলের মাধ্যমে সংশ্লিষ্ট রেকর্ড এডিট করা যায়
              echo "<td>
                      <a href='edit_menu_item.php?id=" . $row['id'] . "' class='action-btn edit-btn'>Edit</a>
                      <a href='delete_menu_item.php?id=" . $row['id'] . "' class='action-btn delete-btn' onclick='return confirm(\"Are you sure?\");'>Delete</a>
                    </td>";
              echo "</tr>";
          }
      } else {
        // যদি কোন মেনু আইটেম না থাকে তাহলে একটি রো দেখানো হবে যাতে বলা হবে "No menu items found."
          echo "<tr><td colspan='6'>No menu items found.</td></tr>";
      }
      ?>
    </table>
  </div>

  <footer>
    &copy; 2025 Restaurant Management System. All rights reserved.
  </footer>

</body>
</html>

<?php $conn->close(); ?>
