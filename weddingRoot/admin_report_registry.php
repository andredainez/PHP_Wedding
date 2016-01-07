<?php
  //NOTE: It appears I never implemented this functionality, and at the time I was just using phpMyAdmin to run these SELECT statements.
  // What can I say? Weddings are a busy time...

  $isSuperUserPage = true;

  require_once("resources/includePath.inc");
  require_once('dbdata.inc');
  $page_title = "Registry Data";
  include("header.inc");
?>

<section class="content">
<?php

// select * from transactions right join transactionItems on transactions.transactionID = transactionItems.transactionID left join users on users.userID = transactions.userID left join inventoryItems on inventoryItems.inventoryID = transactionItems.inventoryID left join storeItems on inventoryItems.storeItemID = storeItems.storeItemID;

// select transactions.transactionID, transactions.timeCheckedOut, transactionItems.qty, users.userID, users.username, inventoryItems.inventoryID, storeItems.storeItemID, storeItems.title, storeItems.basePrice from transactions right join transactionItems on transactions.transactionID = transactionItems.transactionID left join users on users.userID = transactions.userID left join inventoryItems on inventoryItems.inventoryID = transactionItems.inventoryID left join storeItems on inventoryItems.storeItemID = storeItems.storeItemID

$dbc = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT); //from dbdata.inc
if ($dbc->connect_errno)
{
  echo '<p>DB Connection Error.</p>';
}
else
{
  
} //end dbc is connected
?>

</section>


<?php include("footer.inc"); ?>