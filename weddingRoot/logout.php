<?php
  session_start();
  
  require_once("resources/includePath.inc");
  $page_title = "Logout";

  if (isset($_SESSION['login']))
  {
    $_SESSION = array(); //clear the whole session array
    session_destroy();
    setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0); //delete the session cookie
    
    include("header.inc");
?>

<section class="content">
  <p>You have been logged out of your wedding guest account. Can't wait to see you there!</p>
</section>

<?php
     include("footer.inc");
  }
  else //no proper session set already
  {
    include("header.inc");
?>

<section class="content">
  <p>You are not logged in right now.</p>
  <br /><br /><br />
  <p><small>How did you get here?</small></p>
  <p><small>...How did I get here? Where is here? Help!</small></p>
</section>

<?php
     include("footer.inc");
  }
?>