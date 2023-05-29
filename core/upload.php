<?php

session_start();

if(!isset($_SESSION["logged"])){
    header("location: ..");
    exit;
}

include_once "config.php";

/******************
Change profile pic
******************/
if($_SESSION['logged']===true && $_SERVER['REQUEST_METHOD']=="POST" && isset($_GET['redirect']) && $_GET['redirect']=="settings") {

  if(isset($_POST["submit"])) {
    if(is_array($_FILES)) {

      $file = $_FILES['profilepic']['tmp_name'];
      $fileNewName = bin2hex(random_bytes(15));
      $folderPath = "../usr_data/profile_pic/100x100/";
      $ext = pathinfo($_FILES['profilepic']['name'], PATHINFO_EXTENSION);
      $fileNew = $folderPath.$fileNewName.".".$ext;

      if(move_uploaded_file($file, $fileNew)) {

          
          //move_uploaded_file($file, $folderPath. $fileNewName. ".". $ext);

          //Storing name of old profile pic
          $sql = "SELECT profile_pic FROM userprofiledata WHERE uid = ?;";
          if($stmt = mysqli_prepare($con, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $fileOldName);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
          }
          $sql = "UPDATE userprofiledata SET profile_pic = ? WHERE uid = ?;";

          $stmt = mysqli_prepare($con, $sql);
          $fileNewName = $fileNewName.'.'. $ext;
          mysqli_stmt_bind_param($stmt,"ss",$fileNewName, $_SESSION['username']);

          if(!mysqli_stmt_execute($stmt)) {
            unlink($folderPath.$fileNewName);
          }

          if($fileOldName != "default.png")
            unlink($folderPath.$fileOldName);

          mysqli_stmt_close($stmt);
          mysqli_close($con);
          cropImage($folderPath.$fileNewName, 300, $folderPath.$fileNewName);
        }
        header('location: ../views/setting.php');
    }
  }
}
else {
  header('location: ../views/home.php');
}

function imageResize($imageResourceId,$width,$height) {
  $targetWidth = 100;
  $targetHeight = 100;

  $targetLayer=imagecreatetruecolor($targetWidth,$targetHeight);
  imagecopyresampled($targetLayer,$imageResourceId,0,0,0,0,$targetWidth,$targetHeight, $width,$height);

  return $targetLayer;
}

function cropImage($sourcePath, $thumbSize, $destination) {

  $parts = explode('.', $sourcePath);
  $ext = $parts[count($parts) - 1];
  if ($ext == 'jpg' || $ext == 'jpeg') {
    $format = 'jpg';
  } else {
    $format = 'png';
  }

  if ($format == 'jpg') {
    $sourceImage = imagecreatefromjpeg($sourcePath);
  }
  if ($format == 'png') {
    $sourceImage = imagecreatefrompng($sourcePath);
  }

  list($srcWidth, $srcHeight) = getimagesize($sourcePath);

  // calculating the part of the image to use for thumbnail
  if ($srcWidth > $srcHeight) {
    $y = 0;
    $x = ($srcWidth - $srcHeight) / 2;
    $smallestSide = $srcHeight;
  } else {
    $x = 0;
    $y = ($srcHeight - $srcWidth) / 2;
    $smallestSide = $srcWidth;
  }

  $destinationImage = imagecreatetruecolor($thumbSize, $thumbSize);
  imagecopyresampled($destinationImage, $sourceImage, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);

  if ($format == 'jpg') {
    imagejpeg($destinationImage, $destination, 100);
  }
  if ($format == 'png') {
    imagepng($destinationImage, $destination);
  }
}

?>
