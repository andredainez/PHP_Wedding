<?php
// guests_delete_user.php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] < 2)
{
  echo "Administrator not logged in!";
  exit();
}

  
// require_once("../resources/includePath.inc");
if (isset($_REQUEST['userID'])) // && isset($_REQUEST['hash']))
{
  require_once('../resources/includePath.inc');
  require_once('classWeddingUser.inc');
  $userManager = new weddingUser();
  if ($userManager->deleteUser($_REQUEST['userID']))
    echo 'true';
  else
    echo 'false';

  //header('Content-Type: application/json');
  //echo json_encode($attArray);
}
else
{
  echo 'false';
}
?>