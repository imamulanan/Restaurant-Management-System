<?php
// ডেটাবেজ কানেকশনের জন্য প্রয়োজনীয় তথ্য সেট করা
$servername = "localhost";  // সার্ভার নাম বা আইপি (এখানে লোকালহোস্ট)
$username = "root";         // ডেটাবেজ ইউজারনেম
$password = "";             // ডেটাবেজ পাসওয়ার্ড (এখানে খালি)
$dbname = "restaurant_management";  // ডেটাবেজের নাম

// MySQLi ক্লাস ব্যবহার করে কানেকশন তৈরি
$conn = new mysqli($servername, $username, $password, $dbname);

// যদি কানেকশন ব্যর্থ হয়, তাহলে এরর মেসেজ দেখিয়ে প্রোগ্রাম বন্ধ করা
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ফর্ম সাবমিট হলে POST মেথড দিয়ে ডেটা পেলে নিচের কোড চলবে
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ইউজার ইনপুট ডেটা সুরক্ষিত করার জন্য রিয়েল এসকেপ ফাংশন ব্যবহার করা
    
    $dish_name = $conn->real_escape_string($_POST['dish_name']);    // ডিশের নাম 

    
    // $conn->real_escape_string হল PHP-এর MySQLi এক্সটেনশনের একটি মেথড, যা ডাটাবেজে ইনসার্ট বা কোয়েরি করার সময় ইউজার ইনপুটের স্পেশাল ক্যারেক্টারগুলোকে নিরাপদ করে তোলে।এটি এমন একটি ফাংশন যা ইনপুটের বিশেষ চিহ্নগুলো (যেমন ', ", \, ইত্যাদি) এমনভাবে পরিবর্তন করে দেয়, যাতে সেগুলো SQL Injection আক্রমণ থেকে রক্ষা পায়।


    $category = $conn->real_escape_string($_POST['category']);      // ক্যাটেগরি
    $price = (float)$_POST['price'];                                // দাম (ফ্লোট টাইপে কাস্ট)
    $availability = $conn->real_escape_string($_POST['availability']); // অ্যাভেলেবিলিটি

    // SQL ইনসার্ট স্টেটমেন্ট তৈরি করা
    $sql = "INSERT INTO menu (dish_name, category, price, availability)
            VALUES ('$dish_name', '$category', $price, '$availability')";

    // SQL কোয়েরি এক্সিকিউট করা এবং সফল হলে মেনু লিস্ট পেজে রিডাইরেক্ট করা
    if ($conn->query($sql) === TRUE) {
        header("Location: menu_list.php?message=Item+added+successfully");
        exit();  // রিডাইরেক্টের পরে স্ক্রিপ্ট বন্ধ করা
    } else {
        // এরর হলে এরর মেসেজ দেখানো
        echo "Error: " . $conn->error;
    }
}

// কানেকশন বন্ধ করা
$conn->close();
?>
