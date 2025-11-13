<?php
session_start();

// Optional: kung naka-login na, diretso sa home
if (isset($_SESSION['email'])) {
  header("Location: home.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<style>
  input[name="signIn"] {
    background-color: #800000;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s ease;
  }

  input[name="signIn"]:hover {
    background-color: #a00000; /* lighter maroon */
    transform: scale(1.05);    /* subtle grow */
    box-shadow: 0 0 10px rgba(128, 0, 0, 0.4); /* soft glow */
  }

    input[name="signUp"] {
    background-color: #800000;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.3s ease;
  }

  input[name="signUp"]:hover {
    background-color: #a00000; /* lighter maroon */
    transform: scale(1.05);    /* subtle grow */
    box-shadow: 0 0 10px rgba(128, 0, 0, 0.4); /* soft glow */
  }

  
</style>


<body>
    <div class="container" id="signup" style="display:none;">
      <div class="bsulogo" style="display:flex; justify-content:center; align-items:center; margin-bottom:20px;">
  <img src="img/bsu logo.png" alt="BulSU Logo" style="width:120px; height:auto;">
</div>
      <h1 class="form-title">Sign Up</h1>
      <form method="post" action="register.php">
        <div class="input-group">
           <i class="fas fa-user"></i>
          <input type="text" name="firstName" id="firstName" placeholder="First Name" required>
           <label for="fname">First Name</label>
        </div>
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="lastName" id="lastName" placeholder="Last Name" required>
            <label for="lName">Last Name</label>
        </div>
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <label for="email">Email</label>
        </div>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="password" placeholder="Password" required>
            <label for="password">Password</label>
        </div>
       <input type="submit" class="btn" value="Sign Up" name="signUp" style="background-color:#800000;">
      </form>

      
      <div class="links">
        <p>Already Have Account ?</p>
        <button id="signInButton" style="color: #e4b809ff;">Sign In</button>
      </div>
    </div>

    <div class="container" id="signIn">
      <div class="bsulogo" style="display:flex; justify-content:center; align-items:center; margin-bottom:20px;">
  <img src="img/bsu logo.png" alt="BulSU Logo" style="width:120px; height:auto;">
</div>
        <h1 class="form-title">Sign In</h1>
        <form method="post" action="register.php">
          <div class="input-group">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" id="email" placeholder="Email" required>
              <label for="email">Email</label>
          </div>
          <div class="input-group">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" id="password" placeholder="Password" required>
              <label for="password">Password</label>
          </div>
         <input type="submit" class="btn" value="Sign In" name="signIn" style="background-color:#800000;">
        </form>
        
        
        <div class="links">
          <p>Don't have account yet?</p>
          <button id="signUpButton" style = "color: #e4b809ff;">Sign Up</button>
        </div>
      </div>
      <script src="script.js"></script>
      <script src="index1.js"></script>
</body>
</html>
