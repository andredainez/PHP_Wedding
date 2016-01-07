<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] < 1)
{
  header('Content-Type: application/json');
  echo json_encode(array('error' => 'A user is not logged in!'));
  exit();
}

  
  
// require_once("../resources/includePath.inc");
if (isset($_REQUEST['userID']))
{
  require_once('../resources/includePath.inc');
  require_once('dbdata.inc');
  $dbc = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT); //from dbdata.inc
  if ($dbc->connect_errno)
  {
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'DB Connection Error.'));
    exit();
  }
  else
  {
    // $query = 'select inventoryItems.inventoryID as inventoryID, inventoryItems.qty as qty, storeItems.title as title, storeItems.description as description, storeItems.basePrice as price, storeItems.imageFilename as imageFilename , storeItems.caption as caption from inventoryItems left join storeItems on inventoryItems.storeItemID = storeItems.storeItemID order by inventoryItems.orderByNum;';
    // $query = 'select cartID from carts where userID = "';
    $query = 'SELECT cartItems.inventoryID as inventoryID, cartItems.qty as qty FROM carts right join cartItems on carts.cartID = cartItems.cartID where carts.userID = "';
    $query .= $dbc->real_escape_string($_REQUEST['userID']) . '";';
    
    if ($results = $dbc->query($query))
    {
      //find this users current saved cart
      $totalItems = 0;
      $cartArray = array();
      while($row = $results->fetch_assoc()) //found a saved cart
      {
        $totalItems++;
        $cartArray[$row['inventoryID']] = $row['qty'];
      }
      if ($totalItems == 0) //result set is empty, no cart has been saved
      {
        //no cart, return empty array
        header('Content-Type: application/json');
        echo 'false';
        exit();
      }
      else
      {
        header('Content-Type: application/json');
        echo json_encode($cartArray);
        exit();
      }
    }
    else
    {
      header('Content-Type: application/json');
      echo json_encode(array('error' => 'Find user cart query error.'));
      exit();
    }
  }
}
else
{
  header('Content-Type: application/json');
  echo json_encode(array('error' => 'User ID not set in request.'));
  exit();
}
?>