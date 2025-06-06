<?php
// ডাটাবেস সার্ভার ইনফরমেশন সেট করা
$servername = "localhost"; // সার্ভারের নাম (লোকাল সার্ভার)
$username = "root";        // ডাটাবেস ইউজারনেম
$password = "";            // ডাটাবেস পাসওয়ার্ড (লোকালহোস্টে সাধারণত ফাঁকা থাকে)
$dbname = "restaurant_management"; // ডাটাবেসের নাম

// ডাটাবেসের সাথে কানেকশন তৈরি করা
$conn = new mysqli($servername, $username, $password, $dbname);

// যদি কানেকশনে কোনো ত্রুটি থাকে তাহলে স্ক্রিপ্ট বন্ধ করে ত্রুটি দেখাও
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // কানেকশন ব্যর্থ হলে মেসেজ দেখাও
}

// ইউআরএল থেকে আইডি রিড করা, যদি না থাকে তাহলে 0 ধরে নিও
$id = $_GET['id'] ?? 0; // Null Coalescing Operator (??) PHP এর একটি শর্টহ্যান্ড অপারেটর, যা একটি ভেরিয়েবল যদি null হয় বা সেট না করা থাকে, তাহলে একটি ডিফল্ট ভ্যালু রিটার্ন করে।

// যদি আইডি ০ থেকে বড় হয়, অর্থাৎ বৈধ হয়
if ($id > 0) {
    // নির্দিষ্ট আইডির মেনু আইটেম ডিলিট করার SQL কমান্ড
    $sql = "DELETE FROM menu WHERE id = $id";

    // SQL কোয়েরি সফল হলে মেনু তালিকায় রিডাইরেক্ট করো এবং মেসেজ দেখাও
    if ($conn->query($sql) === TRUE) {
        header("Location: menu_list.php?message=Item+deleted+successfully"); // সফলভাবে ডিলিটের পর রিডাইরেক্ট
        exit(); // স্ক্রিপ্ট বন্ধ করে দাও
    } else {
        // যদি ডিলিট সফল না হয় তাহলে ত্রুটি দেখাও
        echo "Error deleting item: " . $conn->error;
    }
} else {
    // যদি আইডি বৈধ না হয় তাহলে ত্রুটি মেসেজ দেখাও
    echo "Invalid item ID.";
}

// ডাটাবেস কানেকশন বন্ধ করা
$conn->close();
?>
