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
    $query = 'select cartID from carts where userID = "';
    $query .= $dbc->real_escape_string($_REQUEST['userID']) . '";';
    
    if ($results = $dbc->query($query))
    {
      //find this users current saved cart OR create a new one
      $cartID = 0;
      if($row = $results->fetch_row()) //found a saved cart
      {
        $cartID = $row[0];
      }
      else //result set is empty, no cart has been saved
      {
        // header('Content-Type: application/json');
        // echo json_encode(array('error' => 'No cart saved.'));
        $query = 'insert into carts (userID) values (' . $dbc->real_escape_string($_REQUEST['userID']) . ');';
        if ($dbc->query($query))
        {
          if ($results = $dbc->query('select LAST_INSERT_ID();'))
          {
            if ($row = $results->fetch_row())
            {
              $cartID = $row[0]; //auto_increment column
            }
            else
            {
              header('Content-Type: application/json');
              echo json_encode(array('error' => 'Fetch last_insert_id results failure.'));
              exit();
            }
          }
          else
          {
            header('Content-Type: application/json');
            echo json_encode(array('error' => 'Fetch last_insert_id failed.'));
            exit();
          }
        }
        else
        {
          header('Content-Type: application/json');
          echo json_encode(array('error' => 'Create new cart query failed.'));
          exit();
        }
      }
      
      //delete any previously saved cart items
      $query = 'delete from cartItems where cartID = ' . $cartID . ';';
      if (!$dbc->query($query))
      {
        header('Content-Type: application/json');
        echo json_encode(array('error' => 'Cart clearing query failed.'));
        exit();
      }
      
      $query = 'insert into cartItems (cartID, inventoryID, qty) values ';
      $firstRow = true;
      
      if (isset($_REQUEST['arrayCart'])) //if the array wasn't passed in the REQUEST, cart will be empty
      {
        foreach($_REQUEST['arrayCart'] as $currInvID => $currQty)
        {
          if ($currQty > 0)
          {
            $query .= ((!$firstRow) ? ', ' : '') . '(' . $cartID . ', ' . $currInvID . ', ' . $currQty . ')';
            $firstRow = false;
          }
        }
        $query .= ';';
        if (!$firstRow) //as least one set of cartItems will be inserted
        {
          if (!$dbc->query($query))
          {
            header('Content-Type: application/json');
            echo json_encode(array('error' => 'Insert cart items query error.'));
            exit();
          }
        }
      }
    }
    else
    {
      header('Content-Type: application/json');
      echo json_encode(array('error' => 'Find user cart query error.'));
      exit();
    }
  }
  
  // header('Content-Type: application/json');
  // echo json_encode($_REQUEST['arrayCart']);
}
else
{
  header('Content-Type: application/json');
  echo json_encode(array('error' => 'User ID not set in request.'));
  exit();
}
?>