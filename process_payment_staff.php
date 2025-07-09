<?php
// সার্ভার, ইউজারনেম, পাসওয়ার্ড এবং ডাটাবেইসের নাম সেট করা
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_management";

// ডাটাবেইসের সাথে সংযোগ স্থাপন
$conn = new mysqli($servername, $username, $password, $dbname);

// সংযোগ সফল হয়েছে কিনা চেক করা
if ($conn->connect_error) {
  // সংযোগে সমস্যা হলে স্ক্রিপ্ট বন্ধ করে দেয় এবং এরর মেসেজ দেখায়
  die("Connection failed: " . $conn->connect_error);
}

// ফর্ম থেকে পাঠানো ডাটা রিসিভ করা
$user = $_POST['username'];         // ইউজারের নাম
$phone = $_POST['phone'];           // ফোন নাম্বার
$amount = $_POST['amount'];         // পরিশোধের পরিমাণ
$method = $_POST['payment_method']; // পেমেন্ট মেথড (যেমন: বিকাশ, নগদ)

// ইউনিক ট্রানজেকশন আইডি তৈরি করা (প্রতি পেমেন্টের জন্য আলাদা আইডি)
$transaction_id = uniqid('txn_', true);

// SQL স্টেটমেন্ট তৈরি করা (প্লেসহোল্ডারসহ) ইনজেকশন থেকে বাঁচার জন্য
$sql = "INSERT INTO payments (username, phone, amount, payment_method, transaction_id) VALUES (?, ?, ?, ?, ?)";

// প্রিপেয়ার স্টেটমেন্ট তৈরি করা
$stmt = $conn->prepare($sql);

// প্লেসহোল্ডারে ভ্যালুগুলো বসানো
// "ssdss" মানে: string, string, double, string, string
$stmt->bind_param("ssdss", $user, $phone, $amount, $method, $transaction_id);

// এক্সিকিউট করা এবং চেক করা সফল হয়েছে কিনা
if ($stmt->execute()) {
  // সফল হলে অ্যালার্ট দিয়ে ট্রানজেকশন আইডি দেখানো এবং ইউজারকে ড্যাশবোর্ডে রিডাইরেক্ট করা
  echo "<script>alert('Payment successful! Transaction ID: $transaction_id'); window.location.href='dashboard_staff.html';</script>";
} else {
  // কোনো এরর হলে সেটি দেখানো
  echo "Error: " . $stmt->error;
}

// স্টেটমেন্ট এবং কানেকশন বন্ধ করা
$stmt->close();
$conn->close();
?>
