<?php
$host = "localhost";  
// ডাটাবেস সার্ভারের ঠিকানা (সাধারণত লোকালহোস্ট)

$dbname = "restaurant_management";  
// ডাটাবেসের নাম যেখানে ডাটা সেভ হবে

$username = "root";  
// ডাটাবেসে লগইনের জন্য ইউজারনেম

$password = "";  
// ডাটাবেসে লগইনের জন্য পাসওয়ার্ড (এখানে খালি)

$conn = new mysqli($host, $username, $password, $dbname);  
// নতুন মাইএসকিউএল কানেকশন তৈরি করা হচ্ছে

if ($conn->connect_error) {  
    die("Connection failed: " . $conn->connect_error);  
    // যদি কানেকশন না হয়, তাহলে প্রোগ্রাম বন্ধ করে ত্রুটির বার্তা দেখাবে
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
    // চেক করা হচ্ছে ফর্ম সাবমিট হয়েছে কিনা POST মেথড দিয়ে

    $full_name = $conn->real_escape_string($_POST['full_name']);  
    // ইউজারের পূর্ণ নাম নেয়া হচ্ছে এবং সুরক্ষিত করার জন্য ডাটাবেস ইনজেকশন প্রতিরোধ করা হচ্ছে
    $username = $conn->real_escape_string($_POST['username']);  
    $role = $conn->real_escape_string($_POST['role']);  
    $phone = $conn->real_escape_string($_POST['phone']);  
    $address = $conn->real_escape_string($_POST['address']);  
    $gender = $conn->real_escape_string($_POST['gender']); 
    
    $created_at = date('Y-m-d H:i:s');  
    // বর্তমান তারিখ ও সময় ফরম্যাটে নেয়া

    // ইউজার ইনফো ডাটাবেসে যোগ করার SQL কোয়েরি
    $sql = "INSERT INTO users (full_name, username, role, phone, address, gender, created_at) 
            VALUES ('$full_name', '$username', '$role', '$phone', '$address', '$gender', '$created_at')";

    if ($conn->query($sql)) {  
        // কোয়েরি সফল হলে নিচের কাজ হবে
        header("Location: manage_users.php");  
        // ম্যানেজ ইউজার পেজে রিডাইরেক্ট হবে

        exit();  
        // স্ক্রিপ্ট এখানেই বন্ধ হয়ে যাবে
    } else {
        // যদি ইনসার্ট করার সময় কোনো সমস্যা হয়, তাহলে এরর মেসেজ দেখাবে
        echo "Error: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New User</title>
  <link rel="stylesheet" href="add_user.css"> <!-- বাইরের CSS ফাইল যুক্ত -->
</head>
<body>

  <div class="container">
    <h2>Add New User</h2> <!-- ফর্ম শিরোনাম -->

    <form method="POST"> <!-- POST মেথডে ফর্ম সাবমিট -->
      <label for="full_name">Full Name:</label>
      <input type="text" name="full_name" required> <!-- পূর্ণ নাম ইনপুট -->

      <label for="username">Username:</label>
      <input type="text" name="username" required> <!-- ইউজারনেম ইনপুট -->

      <label for="role">Role:</label>
      <select name="role" required> <!-- রোল সিলেক্ট -->
        <option value="Admin">Admin</option>
        <option value="Staff">Staff</option>
      </select>

      <label for="phone">Phone Number:</label>
      <input type="text" name="phone" required> <!-- ফোন নম্বর ইনপুট -->

      <label for="address">Address:</label>
      <textarea name="address" rows="3" required></textarea> <!-- ঠিকানা ইনপুট -->

      <label for="gender">Gender:</label>
      <select name="gender" required> <!-- লিঙ্গ সিলেক্ট -->
        <option value="">Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
      </select>

      <button type="submit">Add User</button> <!-- সাবমিট বাটন -->
    </form>

    <a href="manage_users.php">← Back to User Management</a> <!-- ম্যানেজ ইউজার পেজে ফিরে যাওয়ার লিঙ্ক -->
  </div>

</body>
</html>
