<?php
session_start();

if(!isset($_SESSION["logged"])){
    header("location: ..");
    exit;
}

include_once "../core/config.php";

$error = "";
$success = "<div style=\"position:fixed;bottom:0;z-index:10;color:#fff;background-color:#4caf50;padding: 0.5%;width:100%;cursor:pointer;\" title=\"Hide\" onclick=\"this.style.display='none'\">Updated Successfully!</div>";
$profile_data = array(
  "uid"=>"",
  "name"=>"",
  "bio"=>"",
  "profile_pic"=>""
);
if($_SESSION['logged']===true && isset($_GET['profile']) && !empty($_GET['profile'])) {
  $profile = trim($_GET['profile']);
  $sql = "SELECT uid, name, bio, profile_pic, friends FROM userprofiledata WHERE uid=?";
  $stmt = mysqli_prepare($con, $sql);
  mysqli_stmt_bind_param($stmt,"s", $profile);

  if(mysqli_stmt_execute($stmt)) {
    if(mysqli_stmt_store_result($stmt)) {
      if(mysqli_stmt_num_rows($stmt) === 1) {
        mysqli_stmt_bind_result($stmt, $profile_data["uid"], $profile_data["name"], $profile_data["bio"], $profile_data["profile_pic"], $profile_data["friend"]);
        mysqli_stmt_fetch($stmt);
      }
      else
        header('location:../NOT_FOUND.php');
    }
    else
      $error .= "Some error has occurred!";
  }
}
else
    header("location: ..");
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <title>SeeYou | Profile @<?=$profile_data["uid"]?></title>
    <link rel="shortcut icon" href="../assets/images/icons/favicon.ico">

    <link rel="stylesheet" href="../assets/style/view_profile.css">
    <link rel="stylesheet" href="../assets/style/home.css">

    <script src="../assets/scripts/index.js"></script>
  </head>

  <body id="body">
    
    <?php include_once './header.php';?>

    <main>
      <?php if(!empty($error)) { ?>
        <div id="server_side_error" title="Hide" onclick="this.style.display='none';" style="cursor:pointer">
          <?= $error ?>
        </div>
      <?php } ?>
      <section class="profile_card">
        <div class="profile_pic">
          <img src="../usr_data/profile_pic/100x100/<?=$profile_data["profile_pic"]?>" alt="profile_pic">
        </div>
        <div class="profile_info">
          <?php if($profile_data['uid']!=$_SESSION['username']) {
            $sql = "SELECT user_id,friend_id FROM followerconnectionsdata WHERE user_id=? AND friend_id=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt,"ss",$_SESSION['username'], $profile_data['uid']);
            if(mysqli_stmt_execute($stmt)) {
              if(mysqli_stmt_store_result($stmt) && mysqli_stmt_num_rows($stmt) === 0) {
           
          ?>
            <button onclick="followToggle('<?=$profile_data['uid']?>')" value="false" class="follow" id="follow_btn">Follow</button>
          <?php
              }
              else {
          ?>
            <button onclick="followToggle('<?=$profile_data['uid']?>')" value="true" class="follow" style="background-color: #eee;" id="follow_btn">Following</button>
          <?php
              }
            }
            mysqli_stmt_close($stmt);
          }
          ?>

          <span class="user_tag">@<?=$profile_data["uid"]?></span>
          <span class="user_follow"><?=$profile_data["friend"]?> following </span>
          <span class="user_name"><?=$profile_data["name"]?></span>
          <span class="user_about">
            <?=$profile_data["bio"]?>
          </span>
        </div>
      </section>
      <section class="gallary">
        <?php
        $username = $_SESSION['username'];
        $temp_uid = $profile_data['uid'];
        $sql = "SELECT post_id, post_content FROM userpostdata WHERE userpostdata.uid='$temp_uid' ORDER BY time DESC;";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
         
          while($profile_data = mysqli_fetch_assoc($result)) {
            if(empty($profile_data['post_content'])) continue;
          ?>
          <article class="image">
            <a href="./full_post.php?view=<?=$profile_data['post_id']?>">
              <img src="../usr_data/uploads/<?=$profile_data['post_content']?>" alt="post">
            </a>
          </article>
          <?php
            }
          }
          else
              echo "<div style='text-align:center;padding:1vw; background-color:#eee;'>No posts here!</div>";
          ?>
      </section>
    </main>

   
    <?php include_once './footer.php';?>
  </body>

</html>
