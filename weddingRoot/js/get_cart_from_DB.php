<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] < 1)
{
  echo "User not logged in!";
  exit();
}

// require_once("../resources/includePath.inc");
if (isset($_REQUEST['userID']))
{
  if ($_REQUEST['userID'] == $_SESSION['userID'])
  {
	  $returnArray = true;

	  header('Content-Type: application/json');
	  echo json_encode($returnArray);
  }
  else
  {
    header('Content-Type: application/json');
    echo 'false';
  }
}
else
{
  header('Content-Type: application/json');
  echo 'false';
}
?>