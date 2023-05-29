<?php
session_start();

if(!isset($_SESSION["logged"])){
    header("location: ..");
    exit;
}

include_once "../core/config.php";

$error = "";
$success = "<div style=\"position:fixed;bottom:0;z-index:10;color:#fff;background-color:#4caf50;padding: 0.5%;width:100%;cursor:pointer;\" title=\"Hide\" onclick=\"this.style.display='none'\">Updated Successfully!</div>";

if(!isset($_GET['view']) || empty($_GET['view']))
  header('location: ../NOT_FOUND.php');

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <title>SeeYou | Post</title>
    <link rel="shortcut icon" href="../assets/images/icons/favicon.ico">

    <link rel="stylesheet" href="../assets/style/content.css">
    <link rel="stylesheet" href="../assets/style/home.css">
    <link rel="stylesheet" href="../assets/style/full_post.css">

    <script src="../assets/scripts/index.js"></script>
  </head>

  <body id="body" class="scrollbar">

    <!--Overlay-for-edit-->
    <div id="popOverlay"></div>

   
    <?php include_once './header.php';?>

    <main>
      <section id="left_pane">

        <!--Post-UI-->
        <?php
        $profile_id = "";
        $name = "";
        $post_id = htmlspecialchars(trim($_GET['view']));
        $sql = "SELECT userprofiledata.uid, name, post_id, post_content, caption, likes, userprofiledata.profile_pic FROM userpostdata, userprofiledata WHERE userpostdata.uid=userprofiledata.uid AND post_id='$post_id' ORDER BY time DESC;";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) === 1) {
         
          while($profile_data = mysqli_fetch_assoc($result)) {
            $profile_id = $profile_data['uid'];
            $name = $profile_data['name'];
        ?>
        <article class="profile_post">
          <div class="top">
            <img src="../usr_data/profile_pic/100x100/<?=$profile_data['profile_pic']?>" alt="user_dp" class="user_dp">
            <span id="user_name" class="user_name"style="cursor:pointer;" onclick="window.location.href='./view_profile.php?profile=<?=$profile_data['uid']?>'"><?=$profile_data['uid']?></span>

            <?php
            if($profile_id == $_SESSION['username']) {
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
          <div class="image">
          
            <img src="../usr_data/uploads/<?=$profile_data['post_content']?>" alt="user_dp" class="user_dp">
          </div>
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
                $sql = "SELECT post_id, from_id, comment FROM postcommentdata WHERE post_id='$post_id' ORDER BY time";
                $result = mysqli_query($con, $sql);

                if (mysqli_num_rows($result) > 0) {
                
                  while($comment_data = mysqli_fetch_assoc($result)) {
                ?>
                    <div><b class="user"><?=$comment_data['from_id']?></b><?=$comment_data['comment']?></div>
                <?php
                }
              }
            ?>
          </div>
          <form action="" method="post" onsubmit="return postComment(this)">
            <input type="text" value="<?=$post_id?>" name="postId" hidden/>
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
      </section>
      <section class="gallary">
        <h2><?=$name?>'s Collections</h2>
        <?php
        $sql = "SELECT post_id, post_content FROM userpostdata WHERE userpostdata.uid='$profile_id' ORDER BY time DESC;";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
         
          while($gallary_data = mysqli_fetch_assoc($result)) {
            if(empty($gallary_data['post_content']) || $gallary_data['post_id']==$post_id) continue;
        ?>
        <article class="image">
          <a href="./full_post.php?view=<?=$gallary_data['post_id']?>">
            <img src="../usr_data/uploads/<?=$gallary_data['post_content']?>" alt="user_dp">
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
