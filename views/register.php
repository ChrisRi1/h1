<?php

include_once "../core/config.php";
include_once "../core/helper.php";

$fullname = $username = $email = $password = $dob = "";
$error = "";
// Main function call
if($_SERVER['REQUEST_METHOD'] == 'POST') {

  if( isset($_POST['fullname']) && isset($_POST['username']) &&
    isset($_POST['email']) && isset($_POST['password']) &&
    isset($_POST['rpassword']) && isset($_POST['dob'])) {

    if((isNameValid() === true) && (isUsernameValid() === true) &&
       (isEmailValid() === true) && (isPasswordValid()) && (isDobValid())) {
      $username = trim($_POST["username"]);
      $fullname = trim($_POST["fullname"]);
      $email = trim($_POST["email"]);
      $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
      $dob = trim($_POST["dob"]);

      $sql_login = "INSERT INTO userlogindata(uid, email, pass) VALUES(?,?,?)";
      $sql_profile = "INSERT INTO userprofiledata(uid, name, dob) VALUES(?,?,?)";

      if($stmt_login = mysqli_prepare($con, $sql_login)) {
        mysqli_stmt_bind_param($stmt_login,"sss", $username, $email, $password);

        if(mysqli_stmt_execute($stmt_login)) {
          //Add data to profile
          if($stmt_profile = mysqli_prepare($con, $sql_profile)) {
            mysqli_stmt_bind_param($stmt_profile,"sss", $username, $fullname, $dob);

            if(mysqli_stmt_execute($stmt_profile)) {
              header('location: ../?action=registerSuccess');
            }
          }
          else
            $error .= "Someting went wrong. Try again.";

          mysqli_stmt_close($stmt_profile);
        }
        else
          $error .= "Someting went wrong. Try again.";

        mysqli_stmt_close($stmt_login);
      }
      else
        $error .= "Oops! Someting went wrong. Try again.";
      mysqli_close($con);
    }
  }
  else
    $error .= "Invalid request! Please reload the page and try agin.";
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="description" content="SeeYou - A new social networking experiance.">
    <meta name="keywords" content="SeeYou,social networking,chatting,follow,profile,share,register">
    <meta name="author" content="christian rizzo">

    <title>SeeYou | Home</title>
    <link rel="shortcut icon" href="../assets/images/icons/favicon.ico">
    <link rel="stylesheet" href="../assets/style/master.css">
    <link rel="stylesheet" href="../assets/style/form.css">
    <link rel="stylesheet" href="../assets/style/register.css">

    <script src="../assets/scripts/validation.js"></script>
  </head>

  <body>
    <header>
        <a href="../">
         
        </a>
    </header>

    <main>

      <section id="left_pane" class="register_left_pane">
        <h1>Welcome to the family!</h1>
        <span class="blue bold">Register to be a part of this journey.</span>
        
      </section>

      <section id="right_pane" class="register_right_pane">
        <div class="alert">
          <span>!</span><span>Create your SeeYou account to continue.</span>
        </div>
        <?php if(!empty($error)) { ?>
        <div id="server_side_error">
          <?= $error ?>
        </div>
        <?php } ?>
        <div class="register_form">
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="registerForm" onsubmit="return validateRegister()">
            <!---Form-Start-Here--->

            <!-------------->
            <div class="inputFormError" id="nameInputFormError"></div>

            <div class="input_container">
              <div class="input_icon">
                  <span></span>
                <img src="../assets/images/icons/face-id-50.png" alt="name_img">
              </div>
              <div class="input_area">
                <label for="fullname">Name</label>
                <input type="text" name="fullname" onblur="validName()" placeholder="Enter your full name." value="<?php if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fullname'])) echo trim($_POST['fullname']);?>">
              </div>

            </div>
            <!-------------->
            <div class="inputFormError" id="usernameInputFormError"></div>

            <div class="input_container">
              <div class="input_icon">
                  <span></span>
                  <img src="../assets/images/icons/email-sign-50.png" alt="user_img">
              </div>
              <div class="input_area">
                <label for="username">Username</label>
                <input type="text" name="username" onblur="validUserName()" placeholder="Choose a username." value="<?php if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) echo trim($_POST['username']);?>">
              </div>
            </div>
            <!-------------->
            <div class="inputFormError" id="emailInputFormError"></div>

            <div class="input_container">
              <div class="input_icon">
                  <span></span>
                <img src="../assets/images/icons/secured-letter-50.png" alt="email_img">
              </div>
              <div class="input_area">
                <label for="email">Email</label>
                <input type="email" name="email" onblur="validEmail()" placeholder="Enter your email." value="<?php if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) echo trim($_POST['email']);?>">
              </div>

            </div>
            <!-------------->
            <div class="inputFormError" id="passwordInputFormError"></div>

            <div class="input_container">
              <div class="input_icon">
                  <span></span>
                <img src="../assets/images/icons/password-50.png" alt="pass_img">
              </div>
              <div class="input_area">
                <label for="password">Password</label>
                <input type="password" name="password" onblur="validPassword()" placeholder="Enter your password." value="<?php if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'])) echo $_POST['password'];?>">
              </div>

            </div>
            <!-------------->
            <div class="inputFormError" id="rpasswordInputFormError"></div>

            <div class="input_container">
              <div class="input_icon">
                  <span></span>
                <img src="../assets/images/icons/password-50.png" alt="pass_img">
              </div>
              <div class="input_area">
                <label for="password">Repeat Password</label>
                <input type="password" name="rpassword" onblur="validRePassword()" placeholder="Repeat your password." value="">
              </div>

            </div>
            <!-------------->
            <div class="inputFormError" id="birthdayInputFormError"></div>

            <div class="input_container">
              <div class="input_icon">
                  <span></span>
                <img src="../assets/images/icons/birthday-50.png" alt="birthday_img">
              </div>
              <div class="input_area">
                <label for="dob">Birthday</label>
                <input type="date" name="dob" onblur="validBirthday()"  value="<?php if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dob'])) echo $_POST['dob'];?>"/>
              </div>

            </div>
            <!-------------->

            <div class="register_form_button bottom_buttons">
              <input type="submit" name="" id="submit" value="Sign Up Now">
              <button type="button" onclick="window.location.href='../';" name="register">Get Login</button>
            </div>
            <!-------------->
          </form>
        </div>
      </section>

    </main>
    <?php include_once './footer.php';?>
  </body>

</html>
