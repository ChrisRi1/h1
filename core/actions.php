<?php



session_start();

include_once "config.php";

if(isset($_SESSION["logged"])){
    if($_SESSION['logged']===true && isset($_POST['cmd']) && !empty($_POST['cmd'])) {

      switch (htmlspecialchars(trim($_POST['cmd']))) {
        case 'follow':
          if(isset($_POST['uid']) && !empty($_POST['uid'])) {
            $connectionId = bin2hex(random_bytes(50));
            $user_id = $_SESSION['username'];
            $friend_id = htmlspecialchars(trim($_POST['uid']));
            /*
            Status code: 0- Not accepted, 1-accepted , 
            */
            $status = "0";

            $sql = "SELECT user_id,friend_id FROM followerconnectionsdata WHERE user_id=? AND friend_id=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt,"ss",$user_id, $friend_id);

            if(mysqli_stmt_execute($stmt)) {
              if(mysqli_stmt_store_result($stmt) && mysqli_stmt_num_rows($stmt) === 0) {
                $sql = "INSERT INTO followerconnectionsdata(connectionId,user_id,friend_id,status) VALUES(?,?,?,?);";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt,"ssss", $connectionId, $user_id, $friend_id, $status);

                if(mysqli_stmt_execute($stmt)) {
                  $sql = "UPDATE userprofiledata SET friends=friends+1 WHERE uid=?";
                  $stmt = mysqli_prepare($con, $sql);
                  mysqli_stmt_bind_param($stmt,"s", $user_id);
                  mysqli_stmt_execute($stmt);
                }
              }
            }
          }
          break;
          case 'unfollow':
            if(isset($_POST['uid']) && !empty($_POST['uid'])) {
              $user_id = $_SESSION['username'];
              $friend_id = htmlspecialchars(trim($_POST['uid']));

              $sql = "DELETE FROM followerconnectionsdata WHERE user_id=? AND friend_id=?;";
              $stmt = mysqli_prepare($con, $sql);
              mysqli_stmt_bind_param($stmt,"ss",$user_id, $friend_id);
              if(mysqli_stmt_execute($stmt)) {
                $sql = "UPDATE userprofiledata SET friends=friends-1 WHERE uid=?";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt,"s",$user_id);
                mysqli_stmt_execute($stmt);
              }
            }
          break;
          case 'search':
            if(isset($_POST['q']) && !empty($_POST['q'])) {
              $q = htmlspecialchars(trim($_POST['q']));

              $sql = "SELECT uid, profile_pic FROM userprofiledata WHERE uid LIKE '%$q%' ORDER BY uid LIMIT 10 ";
              $result = mysqli_query($con, $sql);

              if (mysqli_num_rows($result) > 0) {
                // output data of each row
                while($profile_data = mysqli_fetch_assoc($result)) {
                  if($profile_data['uid']==$_SESSION['username']) continue;
              ?>
              <div class="friend" onclick="window.location.href='../views/view_profile.php?profile=<?=$profile_data['uid']?>'">
                <img src="../usr_data/profile_pic/100x100/<?=$profile_data['profile_pic']?>" alt="user_dp" class="user_dp">
                <?=$profile_data['uid']?>
              </div>
              <?php
                }
              }else echo "<div class='friend' style='text-align:center;'>No user found!</div>";
            }
          break;
          case 'like':
          if(isset($_POST['postid']) && !empty($_POST['postid'])) {
            $reactionid = bin2hex(random_bytes(50));
            $post_id = htmlspecialchars(trim($_POST['postid']));
            $uid = $_SESSION['username'];

            $sql = "SELECT reactionid FROM postlikecount WHERE post_id=? AND uid=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt,"ss",$post_id, $uid);

            if(mysqli_stmt_execute($stmt)) {
              if(mysqli_stmt_store_result($stmt) && mysqli_stmt_num_rows($stmt) === 0) {
                $sql = "INSERT INTO postlikecount(reactionid,post_id,uid) VALUES(?,?,?);";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt,"sss", $reactionid, $post_id, $uid);

                if(mysqli_stmt_execute($stmt)) {
                  $sql = "UPDATE userpostdata SET likes=likes+1 WHERE post_id=?";
                  $stmt = mysqli_prepare($con, $sql);
                  mysqli_stmt_bind_param($stmt,"s", $post_id);
                  mysqli_stmt_execute($stmt);
                }
              }
            }
          }
          break;

          case 'unlike':
            if(isset($_POST['postid']) && !empty($_POST['postid'])) {
              $post_id = htmlspecialchars(trim($_POST['postid']));
              $uid = $_SESSION['username'];

              $sql = "DELETE FROM postlikecount WHERE post_id=? AND uid=?;";
              $stmt = mysqli_prepare($con, $sql);
              mysqli_stmt_bind_param($stmt,"ss", $post_id, $uid);

              if(mysqli_stmt_execute($stmt)) {
                $sql = "UPDATE userpostdata SET likes=likes-1 WHERE post_id=?";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt,"s", $post_id);
                mysqli_stmt_execute($stmt);
              }
            }
          break;
          case 'deletePost':
            if(isset($_POST['postid']) && !empty($_POST['postid'])) {
              $post_id = htmlspecialchars(trim($_POST['postid']));
              $uid = $_SESSION['username'];
              echo $post_id." ".$uid;

              $sql1 = "DELETE FROM postlikecount WHERE post_id=? AND uid=?;";
              $sql2 = "DELETE FROM userpostdata WHERE post_id=? AND uid=?;";
              $stmt1 = mysqli_prepare($con, $sql1);
              $stmt2 = mysqli_prepare($con, $sql2);
              mysqli_stmt_bind_param($stmt1,"ss", $post_id, $uid);
              mysqli_stmt_bind_param($stmt2,"ss", $post_id, $uid);

              mysqli_stmt_execute($stmt1);
              mysqli_stmt_execute($stmt2);
              header('location:../');
            }
          break;
          case 'comment':
          if(isset($_POST['text']) && isset($_POST['id']) && !empty($_POST['text']) && !empty($_POST['id'])) {
            $comment_id = bin2hex(random_bytes(50));
            $from_id = $_SESSION['username'];
            $comment = htmlspecialchars(trim($_POST['text']));
            $post_id = htmlspecialchars(trim($_POST['id']));

            $sql = "INSERT INTO postcommentdata(comment_id, from_id, comment, post_id) VALUES(?,?,?,?)";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt,"ssss", $comment_id, $from_id, $comment, $post_id);

            if(mysqli_stmt_execute($stmt)) {
              $sql = "SELECT post_id, from_id, comment FROM postcommentdata WHERE post_id='$post_id' ORDER BY time";
              $result = mysqli_query($con, $sql);

              if (mysqli_num_rows($result) > 0) {
                // output data of each row
                while($comment_data = mysqli_fetch_assoc($result)) {
                ?>
                <div><b class='user'><?=$comment_data['from_id']?></b><?=$comment_data['comment']?></div>
              <?php
                }
              }
            }
          }
          break;
      }
    }
}
