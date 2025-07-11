<?php
// ডেটাবেজ সার্ভারের লোকালহোস্ট নাম সেট করা
$servername = "localhost";
// ডেটাবেজ ইউজারনেম সেট করা (ডিফল্টভাবে root)
$username = "root";
// ডেটাবেজ পাসওয়ার্ড সেট করা (এখানে খালি)
$password = "";
// ব্যবহৃত ডেটাবেজের নাম
$dbname = "restaurant_management";

// MySQL সার্ভারে কানেকশন তৈরি করা
$conn = new mysqli($servername, $username, $password, $dbname);

// যদি কানেকশন কাজ না করে তাহলে এরর দেখিয়ে স্ক্রিপ্ট বন্ধ করে দেওয়া
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// ইউজার সাবমিট করা ইনপুটগুলো POST মেথড থেকে নেওয়া
$fullname = $_POST['fullname']; // পুরো নাম
$user = $_POST['username']; // ইউজারনেম
$email = $_POST['email']; // ইমেইল ঠিকানা
$pass = $_POST['password']; // পাসওয়ার্ড
$confirm_pass = $_POST['confirm_password']; // নিশ্চিত পাসওয়ার্ড
$role = $_POST['role']; // ইউজারের রোল (admin/staff)

// যদি পাসওয়ার্ড এবং কনফার্ম পাসওয়ার্ড মিলে না, তাহলে মেসেজ দেখিয়ে স্ক্রিপ্ট বন্ধ করা
if ($pass !== $confirm_pass) {
  echo "Password and Confirm Password do not match!";
  exit;
}

// পাসওয়ার্ডকে সিকিউরভাবে হ্যাশ করে সংরক্ষণের জন্য রূপান্তর করা
$hashed_password = password_hash($pass, PASSWORD_DEFAULT);

// রোল অনুযায়ী সঠিক টেবিল নির্ধারণ করা
if ($role === "admin") {
  $table = "admin"; // অ্যাডমিন টেবিলে ডেটা যাবে
} elseif ($role === "staff") {
  $table = "staff"; // স্টাফ টেবিলে ডেটা যাবে
} else {
  // যদি রোল ভুল হয়, তাহলে এরর মেসেজ দেখানো
  echo "Invalid role selected.";
  exit;
}

// ইনসার্ট করার জন্য SQL স্টেটমেন্ট তৈরি করা, যাতে ইউজার ইনপুট ইনজেকশন থেকে সুরক্ষিত থাকে .এটি Prepared Statement (প্রিপেয়ার্ড স্টেটমেন্ট)
$sql = "INSERT INTO $table (fullname, username, email, password) VALUES (?, ?, ?, ?)";

// প্রিপেয়ার করা স্টেটমেন্ট
$stmt = $conn->prepare($sql);

// ইউজার ডেটাগুলো স্টেটমেন্টে বাইন্ড করা (ssss মানে ৪টি string টাইপ ভ্যারিয়েবল)
$stmt->bind_param("ssss", $fullname, $user, $email, $hashed_password);

// যদি স্টেটমেন্ট সফলভাবে এক্সিকিউট হয়, তাহলে সফল মেসেজ দেখানো
if ($stmt->execute()) {
  echo "Registration successful. <a href='index.html'>Click here to login</a>";
} else {
  // না হলে এরর মেসেজ দেখানো
  echo "Error: " . $stmt->error;
}

// স্টেটমেন্ট এবং ডেটাবেজ কানেকশন বন্ধ করা
$stmt->close();
$conn->close();
?>
