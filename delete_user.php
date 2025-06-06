<?php
// সেশন শুরু করা হয়েছে, যাতে ইউজারের লগইন অবস্থা ট্র্যাক করা যায়
session_start();

// যদি ইউজার লগইন না করে থাকে অথবা ইউজারের রোল 'admin' না হয়, তাহলে তাকে হোমপেজে পাঠিয়ে দাও
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html"); // index.html পেজে রিডাইরেক্ট করা
    exit(); // স্ক্রিপ্ট বন্ধ করা
}

$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "restaurant_management";

$conn = new mysqli($servername, $db_username, $db_password, $db_name);
// যদি কানেকশনে কোনো ত্রুটি থাকে তাহলে স্ক্রিপ্ট বন্ধ করে ত্রুটি দেখাও
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);// কানেকশন ব্যর্থ হলে মেসেজ দেখাও
}

// ইউআরএল থেকে আইডি রিড করা, যদি না থাকে তাহলে 0 ধরে নিও
$id = $_GET['id'] ?? 0;// Null Coalescing Operator (??) PHP এর একটি শর্টহ্যান্ড অপারেটর, যা একটি ভেরিয়েবল যদি null হয় বা সেট না করা থাকে, তাহলে একটি ডিফল্ট ভ্যালু রিটার্ন করে।


// যদি আইডি ০ থেকে বড় হয়, অর্থাৎ বৈধ হয়
if ($id > 0) {
    $sql = "DELETE FROM users WHERE id = $id";
// SQL কোয়েরি সফল হলে ইউজার ম্যানেজমেন্ট পেজে রিডাইরেক্ট করে সফল মেসেজ দেখাও
    if ($conn->query($sql) === TRUE) {
        header("Location: manage_users.php?message=User+deleted+successfully");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
} else {
    echo "Invalid user ID.";
}

$conn->close();
?>
