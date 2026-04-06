 <?php
 session_start();
 if(isset($_SESSION['user_id'])){
  header("Location: dashboard.php");
  exit();
 }
 ?>
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        
  .toggle{
      position: relative;
      right: 10px;
      top: 2px;
      cursor: pointer;
  }
    </style>

</head>
<body>
    <div class="container" id="signUp" style="display:none;">
      <h1 class="form-title">Register</h1>
      <form method="POST" action="register.php">
        <div class="input-group">
           <i class="fas fa-user"></i>
           <input type="text" name="fName" id="fName" placeholder="First Name" required>
           <label for="fname">First Name</label>
        </div>
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="lName" id="lName" placeholder="Last Name" required>
            <label for="lName">Last Name</label>
        </div>
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <label for="email">Email</label>
        </div>
       <div class="input-group">
    <i class="fas fa-lock"></i>
    <input type="password" name="password" id="signUpPassword" placeholder="Password" required>
    <i class="fa fa-eye toggle" id="toggleSignUp"></i>
    <label for="signUpPassword">Password</label>
</div> 
     <!-- Warning message -->
<p id="passError" class="password-requirements">
     Password must be at least 8 characters long and include a capital letter, a number, and a special character
       <input type="submit" class="btn" value="Sign Up" name="signUp">
      </form>
      <p class="or">
        ----------or--------
      </p>
      <div class="icons">
        <i class="fab fa-google"></i>
        <i class="fab fa-facebook"></i>
      </div>
      <div class="links">
        <p>Already Have Account ?</p>
        <button id="signInButton">Sign In</button>
      </div>
    </div>

    <div class="container" id="signIn">
        <h1 class="form-title">Sign In</h1>
        <form method="post" action="login.php">
          <div class="input-group">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" id="email" placeholder="Email" required>
              <label for="email">Email</label>
          </div>
         <div class="input-group">
    <i class="fas fa-lock"></i>
    <input type="password" name="password" id="signInPassword" placeholder="Password" required>
    <i class="fa fa-eye toggle" id="toggleSignIn"></i>
    <label for="signInPassword">Password</label>
</div>
          <p class="recover">
            <a href="#">Recover Password</a>
          </p>
         <input type="submit" class="btn" value="Sign In" name="signIn">
        </form>
<script>
// SHOW / HIDE PASSWORD
  function setupPasswordToggle(toggleId, passwordId) {
    const toggleIcon = document.getElementById(toggleId);
    const passwordInput = document.getElementById(passwordId);

    toggleIcon.addEventListener("click", function () {
        // Change input type (text or password)
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);
        // Change eye icon  
        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
    });
}
const passwordInput = document.getElementById("signUpPassword");
const errorMsg = document.getElementById("passError");

passwordInput.addEventListener("input", function() {
    const value = passwordInput.value;
    
    // Formula: Letters 8+, Capital letter 1+, Number 1+,special character 1+
    const regex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;

    if (value.length > 0 && !regex.test(value)) {
        errorMsg.style.display = "block";
        passwordInput.style.borderColor = "red";
    } else {
        errorMsg.style.display = "none";
        passwordInput.style.borderColor = "#ccc";
    }
});

// Switch on operations for both two forms
setupPasswordToggle("toggleSignUp", "signUpPassword");
setupPasswordToggle("toggleSignIn", "signInPassword");

// SWITCH FORMS 
function showLogin(){
    document.getElementById("signUp").style.display = "none";
    document.getElementById("signIn").style.display = "block";
}
</script>
        <p class="or">
          ----------or--------
        </p>
        <div class="icons">
          <i class="fab fa-google"></i>
          <i class="fab fa-facebook"></i>
        </div>
        <div class="links">
          <p>Don't have account yet?</p>
          <button id="signUpButton">Sign Up</button>
        </div>
      </div>
 
   <script src="script.js"></script>       
</body>
</html>
 