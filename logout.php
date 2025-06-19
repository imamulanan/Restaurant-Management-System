<?php

session_start();

// session_unset() ফাংশনটি সব সেশন ভ্যারিয়েবল গুলোকে মুছে ফেলে
session_unset();

// session_destroy() ফাংশনটি সম্পূর্ণ সেশনটাই ধ্বংস করে দেয় (অর্থাৎ সেশন আইডি সহ সবকিছু মুছে ফেলে)
session_destroy();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Logout - Restaurant Management System</title>
  <link rel="stylesheet" href="logout.css">
</head>
<body>

  <div class="logout-container">
    <h2>You have been logged out</h2>
    <p>Thank you for using the Restaurant Management System.</p>

    <!-- Redirect back to login page -->
    <a href="index.html" class="button">Back to Login</a>
  </div>

  <footer>
    &copy; 2025 Restaurant Management System
  </footer>

</body>
</html>