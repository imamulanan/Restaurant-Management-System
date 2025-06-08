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

// যদি URL এ 'id' প্যারামিটার না থাকে, তাহলে manage_users.php পেজে রিডাইরেক্ট করাও
if (!isset($_GET['id'])) {
    header('Location: manage_users.php'); // রিডাইরেক্ট
    exit(); // স্ক্রিপ্ট বন্ধ করে দেওয়া
}

// 'id' প্যারামিটারটি ইন্টিজার হিসেবে নাও (সিকিউরিটির জন্য)
$id = intval($_GET['id']);

// ইউজারদের টেবিল থেকে ওই ID-র তথ্য বের করো
$result = $conn->query("SELECT * FROM users WHERE id = $id");

// যদি ইউজার না মেলে বা একাধিক রেকর্ড মেলে (যা ভুল), তাহলে বার্তা দেখাও
if ($result->num_rows !== 1) {
    echo "User not found!"; // ইউজার পাওয়া যায়নি
    exit(); // স্ক্রিপ্ট বন্ধ করে দেওয়া
}


$user = $result->fetch_assoc();

// চেক করা হচ্ছে ফর্ম সাবমিট হয়েছে কিনা POST মেথড দিয়ে
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $role = $conn->real_escape_string($_POST['role']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);
    $gender = $conn->real_escape_string($_POST['gender']);

    $sql = "UPDATE users SET 
                full_name = '$full_name',
                username = '$username',
                role = '$role',
                phone = '$phone',
                address = '$address',
                gender = '$gender'
            WHERE id = $id";

    if ($conn->query($sql)) {
        header("Location: manage_users.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link rel="stylesheet" href="edit_users.css">
</head>
<body>

  <div class="container">
    <h2>Edit User</h2>

    <form method="POST">
      <label for="full_name">Full Name:</label>
      <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

      <label for="username">Username:</label>
      <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

      <label for="role">Role:</label>
      <select name="role" required>
        <option value="Admin" <?php if($user['role'] === 'Admin') echo 'selected'; ?>>Admin</option>
        <option value="Staff" <?php if($user['role'] === 'Staff') echo 'selected'; ?>>Staff</option>
      </select>

      <label for="phone">Phone Number:</label>
      <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

      <label for="address">Address:</label>
      <textarea name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>

      <label for="gender">Gender:</label>
      <select name="gender" required>
        <option value="Male" <?php if($user['gender'] === 'Male') echo 'selected'; ?>>Male</option>
        <option value="Female" <?php if($user['gender'] === 'Female') echo 'selected'; ?>>Female</option>
        <option value="Other" <?php if($user['gender'] === 'Other') echo 'selected'; ?>>Other</option>
      </select>

      <button type="submit">Update User</button>
    </form>

    <a href="manage_users.php">← Back to User Management</a>
  </div>

</body>
</html>
