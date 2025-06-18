<?php
session_start(); 
// সেশন শুরু করা হচ্ছে, লগইন সফল হলে ব্যবহারকারীর তথ্য সংরক্ষণের জন্য

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_management";
// ডাটাবেস কানেকশনের জন্য তথ্য নির্ধারণ

$conn = new mysqli($servername, $username, $password, $dbname);
// ডাটাবেস কানেকশন তৈরি

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// কানেকশন যদি ব্যর্থ হয় তাহলে এরর দেখিয়ে প্রোগ্রাম বন্ধ করে দিবে

$user = $_POST['username'];
$pass = $_POST['password'];
$role = $_POST['role'];
// ফর্ম থেকে ইউজারনেম, পাসওয়ার্ড এবং রোল গ্রহণ করা

if ($role === "admin") {
  $table = "admin";
} elseif ($role === "staff") {
  $table = "staff";
} else {
  die("Invalid role selected.");
}
// রোল অনুযায়ী টেবিল নির্ধারণ (admin বা staff)। অন্য কিছু হলে প্রোগ্রাম বন্ধ

$sql = "SELECT * FROM $table WHERE username = ?";
$stmt = $conn->prepare($sql);
// প্রিপেয়ার্ড স্টেটমেন্ট তৈরি (SQL Injection থেকে রক্ষা পেতে)

$stmt->bind_param("s", $user);
// ইউজারনেম প্যারামিটার বেঁধে দেয়া হচ্ছে

$stmt->execute();
// স্টেটমেন্ট এক্সিকিউট করা হচ্ছে

$result = $stmt->get_result();
// রেজাল্ট পাওয়া

if ($result->num_rows === 1) {
  // যদি ১টি ইউজার পাওয়া যায় (অর্থাৎ ইউজারনেম সঠিক)
  $row = $result->fetch_assoc();
  // ডেটা অ্যাসোসিয়েটিভ অ্যারে আকারে নেয়া

  if (password_verify($pass, $row['password'])) {
    // পাসওয়ার্ড যাচাই করা হচ্ছে (hashed পাসওয়ার্ড মিলেছে কিনা)
    
    $_SESSION['username'] = $row['username'];
    $_SESSION['fullname'] = $row['fullname'];
    $_SESSION['role'] = $role;
    $_SESSION['user_id'] = $row['id'];
    // সেশন ভ্যারিয়েবলে ব্যবহারকারীর তথ্য সংরক্ষণ

    if ($role === "admin") {
      header("Location: dashboard.html");
      // অ্যাডমিন হলে অ্যাডমিন ড্যাশবোর্ডে রিডাইরেক্ট
    } else {
      header("Location: dashboard_staff.html");
      // স্টাফ হলে স্টাফ ড্যাশবোর্ডে রিডাইরেক্ট
    }
    exit;
    // রিডাইরেক্টের পর স্ক্রিপ্ট বন্ধ করা
  } else {
    echo "Incorrect password.";
    // পাসওয়ার্ড ভুল হলে মেসেজ দেখানো
  }
} else {
  echo "User not found.";
  // ইউজারনেম না পাওয়া গেলে মেসেজ দেখানো
}

$stmt->close();
$conn->close();
// স্টেটমেন্ট এবং কানেকশন বন্ধ করা
?>
