<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] < 2)
{
  echo "Administrator not logged in!";
  exit();
}

  
  
// require_once("../resources/includePath.inc");
if (isset($_REQUEST['userID']))
{
  require_once('../resources/includePath.inc');
  require_once('classWeddingUser.inc');
  $userManager = new weddingUser();
  $attArray = $userManager->getAttendeesArrayByID($_REQUEST['userID']);

  header('Content-Type: application/json');
  echo json_encode($attArray);
}
else
{
  header('Content-Type: application/json');
  echo 'false';
}
?>