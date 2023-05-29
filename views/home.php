<?php
session_start();

if(!isset($_SESSION["logged"])){
    header("location: ..");
    exit;
}

include_once "../core/config.php";

$error = "";
$failed = false;
$success = "<div style=\"position:fixed;bottom:0;z-index:10;color:#fff;background-color:#4caf50;padding: 0.5%;width:100%;cursor:pointer;\" title=\"Hide\" onclick=\"this.style.display='none'\">Updated Successfully!</div>";

$data = array('uid' => null,
             'email' => null,
             'name' => null,
             'dob' => null,
             'gender' => null,
             'bio' => null,
             'mobile' => null,
             'profile_pic' => null);

if($_SESSION['logged']===true) {

  /******************
  Upload profile post
  ******************/
  if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['submit']) && $_POST['submit']=="uploadProfilePost") {

    if( (isset($_POST['caption']) && !empty($_POST['caption'])) || (isset($_FILES['image']) && !empty($_FILES['image']['name']))) {
      $params = array(
        "post_id"=>"",
        "post_content"=>"",
        "caption"=>""
      );
      $params["post_id"] = bin2hex(random_bytes(30));

      if(isset($_POST["caption"]) && !empty($_POST["caption"])){
        $params["caption"] = htmlspecialchars($_POST["caption"]);
      }
      if(isset($_FILES["image"]) && !empty($_FILES["image"]["name"])) {
        $params["post_content"] = bin2hex(random_bytes(15)).".".pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
      }
      if(!empty($params['post_id'])) {
        $sql = "INSERT INTO userpostdata (uid, post_id, post_content, caption) VALUES(?,?,?,?);";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt,"ssss", $_SESSION['username'], $params['post_id'], $params['post_content'], $params['caption']);

        if(isset($_FILES["image"]) && !empty($_FILES["image"]["name"])) {
          $folderPath = "../usr_data/uploads/";

          if(move_uploaded_file($_FILES["image"]["tmp_name"], $folderPath.$params["post_content"])) {
            if(!mysqli_stmt_execute($stmt))
              $error .= "Some error has occurred!";
            else
              echo $success;
          }
          else
            $error .= "An internal error occurred! Try later.";
        }
        else {
          if(!mysqli_stmt_execute($stmt))
            $error .= "Some error has occurred!";
          else
            echo $success;
        }
      }
      else
        $error .= "Some error occurred!";
    }
    else
      $error .= "You can't leave everything empty!";
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

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    
    <meta http-equiv="Pragma" content="no-cache" />
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="Christian Rizzo">

    <title>SeeYou | <?=$_SESSION['username']?></title>
    <link rel="shortcut icon" href="../assets/images/icons/favicon.ico">

    <link rel="stylesheet" href="../assets/style/content.css">
    <link rel="stylesheet" href="../assets/style/home.css">

    <script src="../assets/scripts/index.js"></script>
  </head>

  <body id="body" class="scrollbar">

   
    <div id="popOverlay"></div>
    
    <?php include_once './header.php';?>

    <main>
      <section id="left_pane">
        <?php if(!empty($error)) { ?>
          <div id="server_side_error" title="Hide" onclick="this.style.display='none';" style="cursor:pointer">
            <?= $error ?>
          </div>
        <?php } ?>
        <!--New-Post-Start-->
        <article id="post">
          <span class="title">
            <img src="../assets/images/icons/post-50.png"/>
            <b>Add post to profile.</b>
          </span>

          <img id="file_upload_preview"/>

          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" name="uploadPost" onsubmit="return uploadPostAndFile();">
            <input type="file" name="image" id="file_upload" accept=".png,.jpeg,.jpg" onchange="loadPreview(this)" hidden>
            <span class="image_upload" onclick="document.getElementById('file_upload').click();">
              <span>
                <img src="../assets/images/icons/add-50.png"/>
                <b id="file_name">Add Image</b>
              </span>
            </span>
            <textarea placeholder="Write something here...". name="caption"></textarea>
            <button type="submit" name="submit" value="uploadProfilePost">Post</button>
            <button type="reset" onclick="resetFileInput()">Reset</button>
          </form>
        </article>
        <!--New-Post-End-->

        <!--Post-UI-->
        <?php
        $username = $_SESSION['username'];
        $friend_list = "'".$username."'";
        $sql_list = "SELECT friend_id FROM followerconnectionsdata WHERE user_id='$username'";
        $result = mysqli_query($con, $sql_list);
        if (mysqli_num_rows($result) > 0) {
          while($list = mysqli_fetch_assoc($result)) {
            $friend_list .= " '".$list['friend_id']."'";
          }
        }
        $friend_list = join(",",explode(" ",trim($friend_list)));
        $sql = "SELECT userprofiledata.uid, post_id, post_content, caption, likes, userprofiledata.profile_pic FROM userpostdata, userprofiledata WHERE userpostdata.uid=userprofiledata.uid AND userprofiledata.uid IN($friend_list) ORDER BY time DESC;";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
          while($profile_data = mysqli_fetch_assoc($result)) {
          ?>
          <article class="profile_post">
            <div class="top">
              <img src="../usr_data/profile_pic/100x100/<?=$profile_data['profile_pic']?>" alt="user_dp" class="user_dp">
              <span id="user_name" class="user_name" style="cursor:pointer;" onclick="window.location.href='./view_profile.php?profile=<?=$profile_data['uid']?>'"><?=$profile_data['uid']?></span>
              <?php
              if($profile_data['uid'] == $_SESSION['username']) {
              ?>
              <img src="../assets/images/icons/ellipsis-50.png" alt="menu" class="menu" onclick="showPostEditOptions('<?=$profile_data['post_id']?>')"/>
              <div class="postEditOptions" id="postEditOption<?=$profile_data['post_id']?>">
                <!--form method="post" action="editPost.php">
                  <input name="postid" value="<?=$profile_data['post_id']?>" hidden/>
                  <button type="submit">Edit</button>
                </form-->
                <form method="post" action="../core/actions.php">
                  <input type="hidden" name="cmd" value="deletePost">
                  <input name="postid" value="<?=$profile_data['post_id']?>" hidden/>
                  <button type="submit">Delete</button>
                </form>
              </div>
            <?php }?>
            </div>
            <?php if(!empty($profile_data['post_content'])) {?>
            <div class="image">
              <img src="../usr_data/uploads/<?=$profile_data['post_content']?>" alt="user_dp" class="user_dp">
            </div>
            <?php
            }
            if(empty($profile_data['post_content']) && !empty($profile_data['caption'])) {
            ?>
            <div class="textStatus">
              <?=$profile_data['caption']?>
            </div>
            <?php }?>
            <div class="bottom">

              <?php
              $sql = "SELECT reactionid FROM postlikecount WHERE post_id=? AND uid=?";
              $post_id = $profile_data['post_id'];
              $uid = $_SESSION['username'];
              $stmt = mysqli_prepare($con, $sql);
              mysqli_stmt_bind_param($stmt,"ss",$post_id, $uid);

              $like_css = "";
              $liked_css = "";
              if(mysqli_stmt_execute($stmt)) {
                if(mysqli_stmt_store_result($stmt) && mysqli_stmt_num_rows($stmt) === 0) {
                  $like_css = "style='display:inline-block;'";
                  $liked_css = "style='display:none;'";
                }
                else {
                  $like_css = "style='display:none;'";
                  $liked_css = "style='display:inline-block;'";
                }
              }
              ?>
              <img src="../assets/images/icons/love-50.png" <?=$like_css?> alt="Like" class="like" id="like<?=$profile_data['post_id']?>" onclick="likeToggle(true,'<?=$profile_data['post_id']?>')">
              <img src="../assets/images/icons/loved-50.png" <?=$liked_css?> alt="Liked"  class="liked" id="liked<?=$profile_data['post_id']?>" onclick="likeToggle(false,'<?=$profile_data['post_id']?>')">
              Liked by <span id="like_count<?=$profile_data['post_id']?>"><?=$profile_data['likes']?></span> people.
              <span id="caption"><?=$profile_data['caption']?></span>
            </div>
            <div id="comment_feed<?=$profile_data['post_id']?>" class="comment_feed">
            <?php
              $post_id = $profile_data['post_id'];
              $sql_commment = "SELECT post_id, from_id, comment FROM postcommentdata WHERE post_id='$post_id' ORDER BY time";
              $result_comment = mysqli_query($con, $sql_commment);
              if (mysqli_num_rows($result) > 0) {
                // output data of each row
                while($comment_data = mysqli_fetch_assoc($result_comment)) {
            ?>
            <div><b class="user"><?=$comment_data['from_id']?></b><?=$comment_data['comment']?></div>
            <?php
              }
            }
            ?>
            </div>
            <form action="" method="post" onsubmit="return postComment(this)">
              <input type="text" value="<?=$profile_data['post_id']?>" name="postId" hidden/>
              <input type="text" placeholder="Add a comment..." name="comment"/>
              <button class="btn" type="submit">Post</button>
            </form>
          </article>
          <?php
            }
          }
          else
              echo "<div style='text-align:center;padding:1vw; background-color:#eee;'>No posts here!</div>";
          ?>
        <!--UI-END-->

        <span class="end">You are at end. Goto <a href="#body">Top</a></span>

      </section>


      
      <!--Profile-Section-->
      <section id="right_pane">
        <div class="logo">
          <img src="../assets/images/logos/text.svg" alt="see_you!" class="logo">
        </div>
        <span id="divide"></span>
        <div class="profile_card">
          <img src="../usr_data/profile_pic/100x100/<?=$data["profile_pic"]?>" alt="user_dp" class="user_dp">
          <span id="user_name" class="user_name"><?=$data["name"]?></span>
          <span id="user_tag" class="user_tag"><?=$data["uid"]?></span>
          <a href="./view_profile.php?profile=<?=$data["uid"]?>" class="profile">View Profile</a>
        </div>
      <!--Friends-Section-->
        <span class="friends_list_h">Your Friends</span>
        <div id="friends_list" class="scrollbar">

          <?php
          $username = $_SESSION['username'];
          $sql = "SELECT uid, name, profile_pic FROM userprofiledata,followerconnectionsdata WHERE userprofiledata.uid=followerconnectionsdata.friend_id AND user_id='$username';";
          $result = mysqli_query($con, $sql);

          if (mysqli_num_rows($result) > 0) {
            
            while($follow_data = mysqli_fetch_assoc($result)) {
          ?>
          <a href="./view_profile.php?profile=<?=$follow_data['uid']?>">
            <div class="friend">
              <img src="../usr_data/profile_pic/100x100/<?=$follow_data['profile_pic']?>" alt="user_dp" class="user_dp">
              <?=$follow_data['name']?>
            </div>
          </a>
          <?php
            }
          }
          else
              echo "<div style='text-align:center;padding:1vw; background-color:#eee;'>No friend in here!</div>";
          ?>

        </div>
      </section>

    </main>

   
    <?php include_once './footer.php';?>
  </body>

</html>
