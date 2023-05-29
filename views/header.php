<header>
  <a href="./">
    <section class="logo container">
      <!--img src="../assets/images/logos/icon.svg" alt="see_you!" class="logo"-->
      <span class="header_divider"></span>
      <img src="../assets/images/logos/text.svg" alt="see_you!" class="logo">
    </section>
  </a>
  <section class="options container">
    <!--img src="../assets/images/icons/notification-50.png" style="height:116%;" alt="notifications" class="notification" onclick="window.location.href='./notification.php';">
    <span class="alert"></span-->
    <!--img src="../assets/images/icons/speech-bubble-50.png" alt="messages" class="message" onclick="window.location.href='./message.php';">
    <span class="alert"></span-->
  </section>
  <section class="search container" id="search_container">
    <form action="" method="post" onsubmit="return false">
      <button type="submit"></button>
      <input type="text" name="query" id="search_query" placeholder="Search" autocomplete="off" onkeyup="liveSearch(false)">
    </form>
    <span class="header_divider"></span>
    <img src="../assets/images/icons/settings-50.png" alt="setting" class="setting" onclick="window.location.href='./setting.php';">
    <img src="../assets/images/icons/logout-rounded-up-50.png" alt="logout" class="logout" onclick="window.location.href='./logout.php';">

  </section>

  <div id="search_result" class="scrollbar">

  </div>

</header>
