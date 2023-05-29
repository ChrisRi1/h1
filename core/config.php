<?php

define('DB_SERVER','localhost');
define('DB_USERNAME','root');
define('DB_PASSWORD','');
define('DB_NAME','seeyouuserdb');

$con = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($con === false)
  echo ("<div style='color:#fff;background-color:#B71C1C;padding: 0.5%;position:fixed;width:100%;'>Error: DbCnnctErr, Contact Administrator!</div>");
