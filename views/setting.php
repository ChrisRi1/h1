<?php
session_start();

if(!isset($_SESSION["logged"])){
    header("location: ..");
    exit;
}

include_once "../core/config.php";
include_once "../core/helper.php";

$failed = false;
$error = "";
$success = "<div style='color:#fff;background-color:#4caf50;padding: 0.5%;width:100%;cursor:pointer;' title='Hide' onclick=\"this.style.display='none'\">Updated Successfully!</div>";

$data = array('uid' => null,
             'email' => null,
             'name' => null,
             'dob' => null,
             'gender' => null,
             'bio' => null,
             'mobile' => null,
             'profile_pic' => null);

if($_SESSION['logged']===true) {
  if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['action'])) {

    $sql = "";
    if($_POST['action'] == "save")
      $sql = "UPDATE userprofiledata SET name=?,dob=?,gender=?,bio=? WHERE uid = ?;";
    else if($_POST['action'] == "update")
      $sql = "UPDATE userlogindata,userprofiledata SET userlogindata.email=?, userprofiledata.mobile=? WHERE userlogindata.uid=userprofiledata.uid AND userlogindata.uid = ?;";
    else if($_POST['action'] == "change_password")
      $sql = "UPDATE userlogindata SET pass=? WHERE uid=?;";

    $stmt = mysqli_prepare($con, $sql);

    if($_POST['action'] == "save") {
      if(isNameValid() && isDobValid()){
        $fullname = trim($_POST['fullname']);
        $dob = trim($_POST['dob']);

        $gender = "";
        if(isset($_POST['gender']))
          $gender = trim($_POST['gender']);

        $bio = "";
        if(isset($_POST['bio']))
          $bio = trim($_POST['bio']);

        mysqli_stmt_bind_param($stmt,"sssss", $fullname, $dob, $gender, $bio, $_SESSION['username']);

        if(!mysqli_stmt_execute($stmt))
          $error .= "Some error has occurred!";
        else
          echo $success;
      }
    }
    else if($_POST['action'] == "update") {
      $email = trim($_POST['email']);
      $mobile = trim($_POST['mobile']);
      mysqli_stmt_bind_param($stmt,"sss", $email, $mobile, $_SESSION['username']);

      if(!mysqli_stmt_execute($stmt))
        $error .= "Some error has occurred!";
      else
        echo $success;
    }
    else if($_POST['action'] == "change_password") {
      if(isset($_POST['current_password']) && isset($_POST['password']) && $_POST['current_password']===$_POST['password'])
        $error .= "New password can't be same as old password!";
      else if(isPasswordValid('pass',$_POST['current_password']) && isPasswordValid('repass')) {
        $sub_sql = "SELECT pass from userlogindata WHERE uid=?";
        $stmt = mysqli_prepare($con, $sub_sql);
        mysqli_stmt_bind_param($stmt,"s",$_SESSION['username']);

        if(mysqli_stmt_execute($stmt)) {
          // Store result

          if(mysqli_stmt_store_result($stmt) && mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_bind_result($stmt, $hashed_password);
            mysqli_stmt_fetch($stmt);
            if(password_verify($_POST['current_password'], $hashed_password)) {
              $stmt = mysqli_prepare($con, $sql);
              $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
              mysqli_stmt_bind_param($stmt,"ss", $password, $_SESSION['username']);

              if(!mysqli_stmt_execute($stmt))
                $error .= "Some error has occurred!";
              else
                echo $success;
            }
            else $error .= "Current password is wrong!";
          }
        }
      }
    }
  }

  $sql = "SELECT userlogindata.uid,email,name,dob,gender,bio,mobile,profile_pic FROM userlogindata, userprofiledata WHERE userlogindata.uid=userprofiledata.uid AND userprofiledata.uid=?;";

  if($stmt = mysqli_prepare($con, $sql)) {
    mysqli_stmt_bind_param($stmt,"s", $_SESSION['username']);

    if(mysqli_stmt_execute($stmt)) {
      mysqli_stmt_store_result($stmt);
      if(mysqli_stmt_num_rows($stmt) === 1) {
        mysqli_stmt_bind_result($stmt, $data['uid'], $data['email'], $data['name'], $data['dob'], $data['gender'], $data['bio'], $data['mobile'], $data['profile_pic']);
        mysqli_stmt_fetch($stmt);
      }
      else $failed = true;
    }
    else $failed = true;
  }
  else $failed = true;

  if($failed)
    die ("<div style='color:#fff;background-color:#B71C1C;padding: 0.5%;position:fixed;width:100%;'>Error: DbCnnctErr, Contact Administrator!</div>");

mysqli_stmt_close($stmt);
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    
    <meta http-equiv="Pragma" content="no-cache" />
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="chris">

    <title>SeeYou | Setting</title>
    <link rel="shortcut icon" href="../assets/images/icons/favicon.ico">

    <link rel="stylesheet" href="../assets/style/header_action.css">
    <link rel="stylesheet" href="../assets/style/home.css">

    <script src="../assets/scripts/index.js"></script>
    <script src="../assets/scripts/setting.js"></script>
    <script src="../assets/scripts/validation.js"></script>

  </head>

  <body id="body" class="scrollbar">
  
    <?php include_once './header.php';?>

    <main>
      <span class="activity_icon">
        <img src="../assets/images/icons/settings-50.png"/>
        <b>Settings</b>
      </span>

    <?php if(!empty($error)) { ?>
      <div id="server_side_error" title="Hide" onclick="this.style.display='none';" style="cursor:pointer">
        <?= $error ?>
      </div>
    <?php } ?>

      <section class="panel">
        <table>
          <tr>
            <td>
              <span class="user_dp">
                <img src="../usr_data/profile_pic/100x100/<?=$data['profile_pic']?>" alt="user_dp">
              </span>
            </td>
            <td>
              <span class="user_name"><?=$data['uid']?></span>
              <form action="../core/upload.php?redirect=settings" method="post" enctype="multipart/form-data" id="changeProfilePic">
                <input type="file" name="profilepic" id="profilepic" accept=".png,.jpeg,.jpg" onchange="document.getElementById('changeProfilePicButton').click();" hidden>
                <a class="changeProfilePic" onclick="document.getElementById('profilepic').click();">Change profile picture</a>
                <input type="submit" name="submit" value="submit" id="changeProfilePicButton" hidden>
              </form>
            </td>
          </tr>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" name="profileForm">
            <tr>
              <td><label for="fullname">Name</label></td>
              <td><input type="text" name="fullname" value="<?=$data['name']?>" onblur="validName()" placeholder="Enter your full name." required></td>
            </tr>
            <tr>
              <td><label for="dob">Birthday</label></td>
              <td><input type="date" name="dob" onblur="validBirthday()" value="<?=$data['dob']?>" required/></td>
            </tr>
            <tr>
            <td><label for="gender">Gender</label></td>
              <td><select name="gender">
                <option name="male" <?php if($data['gender']=="Male") echo "selected";?>>Male</option>
                <option name="female" <?php if($data['gender']=="Female") echo "selected";?>>Female</option>
                <option name="nonbinary" <?php if($data['gender']=="Non-Binary") echo "selected";?>>Non-Binary</option>
              </select></td>
            </tr>

            <tr>
              <td><label for="bio">Bio</label></td>
              <td><textarea name="bio" placeholder="Someting about yourself..."><?=$data['bio']?></textarea></td>
            </tr>
            <tr>
              <td></td>
              <td><button type="submit" name="action" value="save" class="save" >Save</button></td>
            </tr>
          </form>
        </table>
      </section>

      <section class="panel">
        <table>
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="contactForm">
            <tr>
              <td>
                <span class="user_dp">
                  <img src="../usr_data/profile_pic/100x100/<?=$data['profile_pic']?>" alt="user_dp">
                </span>
              </td>
              <td>
                <span class="user_name"><?=$data['uid']?></span>
              </td>
            </tr>
            <tr>
              <td><label for="mobile">Phone Mobile</label></td>
              <td><input type="text" name="mobile" placeholder="+91-XXXXXXXXXX" value="<?=$data['mobile']?>"></td>
            </tr>
            <tr>
              <td><label for="email">Email</label></td>
              <td><input type="email" name="email" placeholder="Enter your email" value="<?=$data['email']?>" required></td>
            </tr>
            <tr>
              <td></td>
              <td><button type="submit" class="save" name="action" value="update">Update</button></td>
            </tr>
          </form>
        </table>
      </section>
      <section class="panel">
        <table>
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="changePasswordForm">
            <tr>
              <td>
                <span class="user_dp">
                  <img src="../usr_data/profile_pic/100x100/<?=$data['profile_pic']?>" alt="user_dp">
                </span>
              </td>
              <td>
                <span class="user_name"><?=$data['uid']?></span>
              </td>
            </tr>
            <tr>
              <td><label for="current_password">Current Password</label></td>
              <td><input type="password" name="current_password" placeholder="Current password" required></td>
            </tr>
            <tr>
              <td><label for="password">New Password</label></td>
              <td><input type="password" name="password" placeholder="New password" required></td>
            </tr>
            <tr>
              <td><label for="rpassword">Repeat Password</label></td>
              <td><input type="password" name="rpassword" placeholder="Repeat password" required></td>
            </tr>
            <tr>
              <td></td>
              <td><button type="submit" class="save" name="action" value="change_password">Change Password</button></td>
            </tr>
          </form>
        </table>
      </section>
      <section class="panel_nav">
        <span id="activePanel" onclick="togglePanel(this,0)">Profile</span>
        <span onclick="togglePanel(this,1)">Contact</span>
        <span onclick="togglePanel(this,2)">Change Password</span>
      </section>
    </main>

   
    <?php include_once './footer.php';?>
  </body>

</html>

<?php } ?>
