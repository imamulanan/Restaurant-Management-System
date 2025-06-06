<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant_management";

$conn = new mysqli($servername, $username, $password, $dbname);
// যদি কানেকশনে কোনো ত্রুটি থাকে তাহলে স্ক্রিপ্ট বন্ধ করে ত্রুটি দেখাও
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// ইউআরএল থেকে আইডি রিড করা, যদি না থাকে তাহলে 0 ধরে নিও
$id = $_GET['id'] ?? 0;// Null Coalescing Operator (??) PHP এর একটি শর্টহ্যান্ড অপারেটর, যা একটি ভেরিয়েবল যদি null হয় বা সেট না করা থাকে, তাহলে একটি ডিফল্ট ভ্যালু রিটার্ন করে।

// যদি ফর্ম সাবমিট করা হয় (POST রিকুয়েস্ট আসে)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ইউজার ইনপুট থেকে ডেটা নেওয়া এবং SQL ইনজেকশন থেকে বাঁচার জন্য real_escape_string ব্যবহার করা হয়েছে
    $dish_name = $conn->real_escape_string($_POST['dish_name']); // ডিশের নাম
    $category = $conn->real_escape_string($_POST['category']);   // ডিশ ক্যাটাগরি
    $price = (float)$_POST['price'];                             // ডিশের দাম, float-এ কনভার্ট করা
    $availability = $conn->real_escape_string($_POST['availability']); // (Available/Unavailable)

    // ডেটাবেসে আপডেট করার SQL কোয়েরি (সংশ্লিষ্ট আইডির রেকর্ড আপডেট করবে)
    $sql = "UPDATE menu SET 
                dish_name = '$dish_name', 
                category = '$category', 
                price = $price, 
                availability = '$availability' 
            WHERE id = $id"; // নির্দিষ্ট আইডির মেনু আইটেম আপডেট করবে

    // কোয়েরি সফলভাবে এক্সিকিউট হলে
    if ($conn->query($sql) === TRUE) {
        // মেনু তালিকার পেজে রিডাইরেক্ট করে সফল মেসেজ দেখানো
        header("Location: menu_list.php?message=Item+updated+successfully");
        exit(); // স্ক্রিপ্ট বন্ধ
    } else {
        // যদি আপডেট ব্যর্থ হয় তাহলে ত্রুটি মেসেজ দেখানো
        echo "Error: " . $conn->error;
    }
}

// উপরের কোডের নিচে, এখনকার মেনু আইটেমের তথ্য ডেটাবেস থেকে বের করা হচ্ছে যাতে ফর্মে প্রি-ফিল করা যায়
$sql = "SELECT * FROM menu WHERE id = $id"; // নির্দিষ্ট আইডির মেনু আইটেম সিলেক্ট করা
$result = $conn->query($sql); // কোয়েরি চালানো
$item = $result->fetch_assoc(); // রেজাল্ট থেকে একটি অ্যাসোসিয়েটিভ অ্যারে আকারে ডেটা তোলা

// যদি ওই আইডির কোনো মেনু আইটেম না পাওয়া যায় তাহলে মেসেজ দেখাও এবং স্ক্রিপ্ট বন্ধ করো
if (!$item) {
    echo "Menu item not found."; // মেনু আইটেম পাওয়া যায়নি
    exit(); // স্ক্রিপ্ট বন্ধ
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Edit Menu Item</title>
    <link rel="stylesheet" href="edit_menu_item.css" />
</head>
<body>

    <!-- ফর্মটি ঘেরাও করার জন্য একটি কনটেইনার ডিভ -->
    <div class="form-container">
        <h2>Edit Menu Item</h2> <!-- ফর্মের উপরের শিরোনাম -->

        <!-- ফর্ম শুরু, method="post" মানে ডেটা সার্ভারে পাঠাবে POST মেথডে -->
        <form method="post" action="">
            <!-- ডিশের নাম ইনপুট -->
            <label for="dish_name">Dish Name:</label>
            <input type="text" id="dish_name" name="dish_name"
                   value="<?php echo htmlspecialchars($item['dish_name']); ?>" required>
            <!-- PHP দিয়ে ফর্মে পূর্বের dish_name ভ্যালু দেখানো হয়েছে -->

            <!-- ক্যাটাগরি ইনপুট -->
            <label for="category">Category:</label>
            <input type="text" id="category" name="category"
                   value="<?php echo htmlspecialchars($item['category']); ?>" required>
            <!-- PHP দিয়ে ফর্মে পূর্বের category ভ্যালু দেখানো হয়েছে . htmlspecialchars() ফাংশনটি HTML স্পেশাল ক্যারেক্টারগুলোকে HTML entity-তে রূপান্তর করে দেয় যেন সেগুলো ব্রাউজারে HTML হিসেবে কাজ না করে শুধুই টেক্সট হিসেবে দেখায়। -->

            <!-- প্রাইস ইনপুট -->
            <label for="price">Price ($):</label>
            <input type="number" id="price" name="price" step="0.01"
                   value="<?php echo htmlspecialchars($item['price']); ?>" required>
            <!-- PHP দিয়ে ফর্মে পূর্বের price ভ্যালু দেখানো হয়েছে এবং decimal value নেওয়ার জন্য step="0.01" -->

            <!-- এভেইল্যাবিলিটি সিলেকশন -->
            <label for="availability">Availability:</label>
            <select id="availability" name="availability" required>
                <!-- যদি item এর availability "Available" হয়, তাহলে সেটি প্রি-সিলেক্ট হবে -->
                <option value="Available" <?php if($item['availability'] == 'Available') echo 'selected'; ?>>Available</option>
                <!-- যদি item এর availability "Unavailable" হয়, তাহলে সেটি প্রি-সিলেক্ট হবে -->
                <option value="Unavailable" <?php if($item['availability'] == 'Unavailable') echo 'selected'; ?>>Unavailable</option>
            </select>

            <!-- সাবমিট বাটন -->
            <input type="submit" value="Update Item">
        </form>

        <!-- ব্যাক বাটন: মেনু তালিকায় ফিরে যাওয়ার জন্য লিংক -->
        <a href="menu_list_staff.php">Back to Menu List</a>
    </div>

</body>

</html>
