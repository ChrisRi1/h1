<?php


$error = "";



function isNameValid() {
  global $error;
  // Fullname validation
  if(empty(trim($_POST['fullname']))) {
    $error .= "Name can't be empty!<br/>";
  }
  else {
    if(strlen(trim($_POST['fullname']))>30)
      $error .= "Name should be atmost 30 characters long.";
    else {
      if(!preg_match("/^[a-zA-Z ]+$/",trim($_POST['fullname']) ))
        $error .= "Name can only have alphabets and space.";
      else
        return true;
    }//End-StringLen
  }//End-NotEmpty
}



function isUsernameValid($cmd = "shouldnotexist") {
  global $error;
  global $con;
  // Username validation
  if(empty(trim($_POST['username']))) {
    $error .= "Username should not be empty!<br/>";
  }
  else {
    if(strlen(trim($_POST['username']))<6 || strlen(trim($_POST['username']))>20)
      $error .= "Username should be 6-20 characters long.";
    else {
      if(!preg_match("/^[a-zA-Z0-9]+$/",trim($_POST['username'])))
        $error .= "Username can only be alphanumeric!";
      else {
        if($cmd === "shouldnotexist") {
          $sql = "SELECT uid FROM userlogindata where uid=?";

          if($stmt = mysqli_prepare($con, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST['username']);

            if(mysqli_stmt_execute($stmt)) {
              mysqli_stmt_store_result($stmt);

              if(mysqli_stmt_num_rows($stmt) == 1)
                $error .= "Username is already taken!<br/>";
              else
                return true;
            }
          }//End-mysql prepare
          else
            $error .= "Oops! Someting went wrong. Try again.";
          mysqli_stmt_close($stmt);
        }
        else
        if($cmd === "validate") {
          return true; //valid username
        }
      }
    }//End-preg_match
  }//End-NotEmpty
}

function isEmailValid($sameEmailAllowed = false) {
  global $error;
  global $con;
  // Email validation
  if(empty(trim($_POST['email']))) {
    $error .= "Email should not be empty!<br/>";
  }
  else {
    if(strlen(trim($_POST['email']))<7 || strlen(trim($_POST['email']))>100)
      $error .= "Email should be 7-100 characters long.";
    else {
      $regex_email = "/^(([^<>()\[\]\\.,;:\s@\"]+(\.[^<>()\[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/";
      if(!preg_match($regex_email,trim($_POST['email'])))
        $error .= "Email is not valid!";
      else {
        if($sameEmailAllowed)
          $sql = "SELECT email FROM userlogindata where email=? and uid<>?";
        else
          $sql = "SELECT email FROM userlogindata where email=?";

        if($stmt = mysqli_prepare($con, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $param_email);
          $param_email = trim($_POST['email']);

          if(mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);

            if(mysqli_stmt_num_rows($stmt) == 1)
              $error .= "Email already in use!<br/>";
            else
              return true;

          }//stmt execute
          else
            $error .= "Oops! Someting went wrong. Try again.";
          mysqli_stmt_close($stmt);
        }//End-mysql prepare
      }//End-preg_match
    }//End-StringLen
  }//End-NotEmpty
}




function isPasswordValid($cmd = "repass", $password = "") {
  global $error;
  // Password validation

  if($cmd == "repass") {
    if(empty($_POST['password']) || empty($_POST['rpassword'])) {
      $error .= "Password or Repeat Password can't be empty!<br/>";
    }
    else {
      if((strlen($_POST['password'])<6 || strlen($_POST['password'])>20) ||
         (strlen($_POST['rpassword'])<6 || strlen($_POST['rpassword'])>20))
        $error .= "Password or Repeat Password should be 6-15 characters long.";
      else {
        $regex_password = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/";

        if(!preg_match($regex_password,$_POST['password']) ||
           !preg_match($regex_password,$_POST['rpassword']) )
          $error .= "Password or Repeat Password should contain atleast an uppercase, a lowercase, a special character, and a number!";
        else
          if($_POST['password'] !== $_POST['rpassword'])
            $error .= "Repeat Password should match Password!";
          else
            return true;
      }//End-StringLen
    }//End-NotEmpty
  }
  else
  if($cmd === "pass") {
    if(empty($password)) {
      $error .= "Password can't be empty!<br/>";
    }
    else {
      if(strlen($password)<6 || strlen($password)>20)
        $error .= "Password should be 6-15 characters long.";
      else {
        $regex_password = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/";

        if(!preg_match($regex_password,$password))
          $error .= "Password should contain atleast an uppercase, a lowercase, a special character, and a number!";
        else
          return true;
      }//End-StringLen
    }
  }
}


function isDobValid() {
  global $error;
  // DOB validation
  if(empty(trim($_POST['dob']))) {
    $error .= "Birthday can't be empty!<br/>";
  }
  else {
    if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",trim($_POST['dob'])))
      $error = "Birthday format is wrong (YYYY-MM-DD)!";
    else {
      $date = explode('-',$_POST['dob']);
      if(!checkdate($date[1],$date[2],$date[0]))
        $error .= "Invalid date of birth!";
      else {
        $age = DateTime::createFromFormat("Y-m-d",$_POST['dob'])
        ->diff(new DateTime('now'))
        ->y;
        if($age<13)
          $error .= "Your age is less than 13 year!";
        else
          return true;
      }
    }//End-StringLen
  }//End-NotEmpty
}