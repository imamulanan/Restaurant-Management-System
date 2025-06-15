<?php
// ফর্ম সাবমিশন হ্যান্ডেল করার জন্য কোড শুরু

$message = "";  
// মেসেজ দেখানোর জন্য একটি ভ্যারিয়েবল খালি রাখা হয়েছে

if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    // চেক করা হচ্ছে, ফর্ম সাবমিট হয়েছে কি না (POST রিকোয়েস্ট হলে)

    $email = htmlspecialchars($_POST['email']);  
    // ইউজার থেকে পাওয়া ইমেইলকে স্যানিটাইজ করে নেয়া (HTML স্পেশাল ক্যারেক্টার গুলোকে নিরাপদ করে)

    
    $registeredEmails = ['user1@example.com', 'user2@example.com'];  
    // এখানে ডামি ইমেইল লিস্ট দেওয়া হয়েছে (আসলে ডাটাবেস থেকে চেক করবে)

    if (in_array($email, $registeredEmails)) {  
        // যদি ইউজার ইমেইল ডামি লিস্টে থাকে

        $message = "<p style='color: green;'>A password reset link has been sent to $email</p>";  
        // সফলতার মেসেজ দেখাবে (গ্রিন কালারে)

        // sendResetLink($email); // Example for future use
        // ভবিষ্যতে ইমেইল পাঠানোর জন্য ফাংশন কল করা যাবে এখানে
    } else {
        $message = "<p style='color: red;'>Email address not found!</p>";  
        // যদি ইমেইল না পাওয়া যায়, তাহলে এরর মেসেজ দেখাবে (রেড কালারে)
    }
}
?>


<!DOCTYPE html>
<html lang="en">  
<!-- ডকুমেন্টের ভাষা ইংরেজি সেট -->

<head>
  <meta charset="UTF-8" />  
  <!-- ক্যারেক্টার এনকোডিং -->

  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>  
  <!-- মোবাইল ও অন্যান্য ডিভাইসের জন্য রেস্পন্সিভ ভিউপোর্ট -->

  <title>Forgot Password</title> 
  <link rel="stylesheet" href="forgot_password.css">  
  

</head>

<body>

  <div class="container">  
    <h2>Forgot Password</h2>  
    <p>Enter your registered email to reset your password.</p>  
    <div class="message">
      <?php echo $message; ?>  
      <!-- PHP থেকে প্রেরিত মেসেজ এখানে দেখানো হবে -->
    </div>

    <!-- Forgot password form -->
    <form action="" method="post">  
      <!-- ফর্ম, ফর্ম সাবমিট করলে এই পেজেই POST রিকোয়েস্ট যায় -->

      <label for="email">Email Address</label>  
      <!-- লেবেল -->

      <input type="email" id="email" name="email" placeholder="Enter your email" required>  
      <!-- ইমেইল ইনপুট ফিল্ড, অবশ্যই পূরণ করতে হবে -->

      <button type="submit" class="btn">Send Reset Link</button>  
      <!-- সাবমিট বাটন -->

    </form>

    <a href="index.html" class="back-link">Back to Login</a>  
    <!-- লগইন পেজে ফেরার লিঙ্ক -->

  </div>

</body>
</html>
