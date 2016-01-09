<?php
$isUserPage = true;
$usePrettyPhoto = true;
$useJQuery = true;

require_once("resources/includePath.inc");
$page_title = "Check Out";
include("header.inc");
require_once('dbdata.inc');

$textGift = '';
$textGiver = $_SESSION['name'];

if (isset($_REQUEST['submitted']))
{
$dbc_transaction = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT); //from dbdata.inc
if ($dbc_transaction->connect_errno)
{
  $errors['connection'] = 'Database connection failed.';
}
else
{
  $query = 'SELECT cartItems.inventoryID as inventoryID, cartItems.qty as qty, ';
  $query .= 'carts.cartID as cartID, ';
  $query .= 'storeItems.title as title ';
  $query .= 'FROM carts right join cartItems on carts.cartID = cartItems.cartID ';
  $query .= 'right join inventoryItems on cartItems.inventoryID = inventoryItems.inventoryID ';
  $query .= 'right join storeItems on inventoryItems.storeItemID = storeItems.storeItemID ';
  $query .= 'where carts.userID = "';
  $query .= $dbc_transaction->real_escape_string($_SESSION['userID']) . '";';

  if ($results = $dbc_transaction->query($query))
  {
    if ($results->num_rows < 1)
    {
      $errors['emptyCart'] = 'You cart has no items; transaction cannot be completed.';
    }
    else
    {
      //create new transaction
      $query = 'INSERT into transactions (userID, timeCheckedOut) VALUES (';
      $query .= $dbc_transaction->real_escape_string($_SESSION['userID']) . ', ';
      $query .= 'NOW());';
      if ($dbc_transaction->query($query))
      {
        if ($result_get_auto = $dbc_transaction->query('select LAST_INSERT_ID();'))
        {
          if ($row_auto = $result_get_auto->fetch_row())
          {
            $transactionID = $row_auto[0]; //auto_increment column
            
            $count = 0;
            $cartID = 0;
            $countRows = $results->num_rows;
            $query = 'INSERT into transactionItems (transactionID, inventoryID, qty) VALUES ';
            $queries_reduce_inventory = array();
            while ($row = $results->fetch_assoc())
            {
              //build string to display in PDF certificate
              if ($results->num_rows == 1)
              {
                ;
              }
              elseif ($countRows == 1)
              {
                if ($results->num_rows == 2)
                  $textGift .= ' and ';
                else
                  $textGift .= ', and ';
              }
              elseif ($countRows < $results->num_rows)
              {
                $textGift .= ', ';
              }
              if ($row['qty'] > 1)
                $textGift .= $row['qty'] . ' ';
              
              $textGift .= $row['title'];
              --$countRows;
              
              //build query to insert into transactionItems
              if ($count++ > 0)
                $query .= ', ';
              $query .= '(' . $transactionID . ',';
              $query .= $row['inventoryID'] . ',';
              $query .= $row['qty'] . ')';
              
              $queries_reduce_inventory[] = 'UPDATE inventoryItems SET qty = qty - ' . $row['qty'] . ' WHERE inventoryID = ' . $row['inventoryID'] . ';';
              
              $cartID = $row['cartID'];
            }
            $query .= ';';
            
            if ($dbc_transaction->query($query))
            {
              $query1 = 'DELETE from cartItems WHERE cartID = ' . $cartID . ';';
              $query2 = 'DELETE from carts WHERE cartID = ' . $cartID . ';';
              if ($dbc_transaction->query($query1) && $dbc_transaction->query($query2))
              {
                foreach ($queries_reduce_inventory as $key => $query_reduce_inv)
                {
                  // echo $query_reduce_inv . '<br />';
                  if (!$dbc_transaction->query($query_reduce_inv))
                    $errors['reduceInventory' . $key] = 'Removing item from store inventory (ID: ' . $key . ') failed.';
                }
                ; //everything successful. yay!
                //will display the confirmation message and certificate below
              }
              else
              {
                $errors['deleteOldCarts'] = 'Delete cart items after transaction failed.';
              }
            }
            else
            {
              $errors['insertTransactionItemsFail'] = 'Insert transaction items failed.';
            }
          }
          else
          {
            $errors['fetchID'] = 'Fetch_row for auto_insert_ID failed.';
          }
        }
        else
        {
          $errors['queryID'] = 'Query for getting auto_insert_ID failed.';
        }
      }
      else
      {
        $errors['newTransaction'] = 'Create new transaction query failed.';
        // $errors['specifics'] = $dbc_transaction->error;
      }
    }
  }
  else
  {
    $errors['query'] = 'Inventory query failed to return results.';
  }
}

  if (!isset($errors))
  {
?>

  <section class="content">
    <h2>Transaction Completed</h2>
    <p>The items you are giving us have been saved to your wedding login. You may print out this customized certificate to include in a card at any time.</p>
    <p><a href="WeddingCertificate.php?gift=<?php echo $textGift; ?>&giver=<?php echo $textGiver; ?>&display=preview" target="_none" rel="external">Preview Certificate</a></p>
    <p><a href="WeddingCertificate.php?gift=<?php echo $textGift; ?>&giver=<?php echo $textGiver; ?>&display=download">Download Certificate</a></p>
    <p>If you come back to the registry page later, you will be able to print your PDF certificate again.</p>
  </section>

<?php
  }
} // end if(submitted)

if (isset($errors))
{
  echo '<div class="error" style="border:1px red dotted; padding-right:15px; padding-left:15px;">';
  foreach($errors as $key => $value)
  {
    echo '<p class="error">Error: ' . $value . "</p>\n";
  }
  echo '<p>Even though there was a web site error, no worries! You can still print out a PDF certificate using the "Preview" link below. Thanks for using the registry check-out system!</p>';
  echo '</div>';
}

if (isset($errors) || !isset($_REQUEST['submitted'])) //if is-NOT-submitted
{
?>

<script type="text/javascript" charset="utf-8">

var sessionUserID = <?php echo $_SESSION['userID']; ?>;

function showDetails(identifier)
{
  if (identifier == 'Chase')
  {
    if ($("#detailsPaypal").css('display') != 'none')
     $("#detailsPaypal").fadeOut(400, function() {$("#detailsChase").fadeIn(400)});
    else if ($("#detailsCheck").css('display') != 'none')
     $("#detailsCheck").fadeOut(400, function() {$("#detailsChase").fadeIn(400)});
   else
    $("#detailsChase").fadeIn(600);
  }
  else if (identifier == 'Paypal')
  {
    if ($("#detailsChase").css('display') != 'none')
     $("#detailsChase").fadeOut(400, function() {$("#detailsPaypal").fadeIn(400)});
    else if ($("#detailsCheck").css('display') != 'none')
     $("#detailsCheck").fadeOut(400, function() {$("#detailsPaypal").fadeIn(400)});
   else
    $("#detailsPaypal").fadeIn(600);
  }
  else if (identifier == 'Check')
  {
    if ($("#detailsPaypal").css('display') != 'none')
     $("#detailsPaypal").fadeOut(400, function() {$("#detailsCheck").fadeIn(400)});
    else if ($("#detailsChase").css('display') != 'none')
     $("#detailsChase").fadeOut(400, function() {$("#detailsCheck").fadeIn(400)});
   else
    $("#detailsCheck").fadeIn(600);
  }
}

</script>

<section class="content">

  <div class="quest">
    <h3 class="fancy" style="text-align:center;">Honeymoon Registry Cart</h3>
    <?php
    //<table id="tableCart" style="min-width:217px;">
    ?>
    <table id="tableCart" style="width:100%">
      <thead>
        <tr>
          <td style="border-bottom:1px dotted #555555;"></td>
          <td style="border-bottom:1px dotted #555555;">Item</td>
          <td style="border-bottom:1px dotted #555555;">Qty</td>
          <td style="border-bottom:1px dotted #555555;">Value</td>
        <tr>
      </thead>
      <tbody id="tbodyCart">

<?php
$dbc = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT); //from dbdata.inc
if ($dbc->connect_errno)
{
  echo '<p>DB Connection Error.</p>';
}
else
{
  $query = 'SELECT cartItems.inventoryID as inventoryID, cartItems.qty as qty, ';
  $query .= 'storeItems.title as title, storeItems.basePrice as basePrice, storeItems.imageFilename as imageFilename, storeItems.caption as caption ';
  $query .= 'FROM carts right join cartItems on carts.cartID = cartItems.cartID ';
  $query .= 'right join inventoryItems on cartItems.inventoryID = inventoryItems.inventoryID ';
  $query .= 'right join storeItems on inventoryItems.storeItemID = storeItems.storeItemID ';
  $query .= 'where carts.userID = "';
  $query .= $dbc->real_escape_string($_SESSION['userID']) . '";';

  if ($results = $dbc->query($query))
  {
    if ($results->num_rows < 1)
    {
      $isCartEmpty = true;
      ?>
        <tr>
          <td colspan="4"><em>Cart empty.</em></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="4" style="border-top:1px dotted #555555; text-align:center;"><a href="registry.php" class="noUnderline"><em>Click here to return to previous page to add or delete items.</em></a></td>
      </tfoot>
      <?php
    }
    else
    {
      $isCartEmpty = false;
      $total = 0;
      $countRows = $results->num_rows;
      while ($row = $results->fetch_assoc())
      {
      
        if ($results->num_rows == 1)
        {
          ;
        }
        elseif ($countRows == 1)
        {
          if ($results->num_rows == 2)
            $textGift .= ' and ';
          else
            $textGift .= ', and ';
        }
        elseif ($countRows < $results->num_rows)
        {
          $textGift .= ', ';
        }
        
        if ($row['qty'] > 1)
          $textGift .= $row['qty'] . ' ';
        
        $textGift .= $row['title'];
        
        --$countRows;
        ?>
        <tr>
          <input type="hidden" id="inventoryID[<?php echo $row['inventoryID']; ?>]" value="<?php echo $row['inventoryID']; ?>" class="inputInvID" />
          <td><a title="<?php echo $row['caption']; ?>" href="images/storeItems/<?php echo $row['imageFilename']; ?>" rel="prettyPhoto" class="noUnderline"><img src="images/storeItems/thumb_<?php echo $row['imageFilename']; ?>" width="75" height="50" /><br /></a></td>
          <td><?php echo $row['title']; ?></td>
          <td><?php echo $row['qty']; ?></td>
          <td>$<?php echo (intval($row['basePrice']) / 100) ?></td><?php
          $total += intval($row['basePrice']) * intval($row['qty']);
          ?>
        </tr>
        <?php
      }
      ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" style="border-top:1px dotted #555555;"><a href="registry.php" class="noUnderline"><em>Click here to return to previous page to edit.</em></a></td>
          <td style="border-top:1px dotted #555555;">Total: </td>
          <td id="tdCartTotal" style="border-top:1px dotted #555555;">$<?php echo $total / 100; ?></td>
        </tr>
      </tfoot>
      <?php
    }
  }
  else
  {
    echo '<p>Error: Inventory query failed to return results.</p>';
  }
}
?>
    </table>
  </div>

<p>There are several ways to gift into our honeymoon fund, two electronic and one traditional.</p>
<table width="100%" style="text-align:center;">
  <tr>
    <td><a href="https://www.chase.com/online-banking/quickpay" title="Link to Chase Quick Pay" rel="external" target="_blank" class="noUnderline"><img src="resources/registry_store/chase.png" width="150" height="33" /></a></td>
    <td><a href="https://www.paypal.com/webapps/mpp/send-money-online" title="Link to PayPal money transfer" rel="external" target="_blank" class="noUnderline"><img src="resources/registry_store/paypal.png" width="150" height="39" /></a></td>
    <td><img src="resources/registry_store/check.jpg" width="150" height="69" /></td>
  </tr>
  <tr>
    <td><a href="javascript:void(0)" onclick="showDetails('Chase');"><img src="resources/icons/magnify_plus.png" width="24px" height="24px" /> Details</a></td>
    <td><a href="javascript:void(0)" onclick="showDetails('Paypal');"><img src="resources/icons/magnify_plus.png" width="24px" height="24px" /> Details</a></td>
    <td><a href="javascript:void(0)" onclick="showDetails('Check');"><img src="resources/icons/magnify_plus.png" width="24px" height="24px" /> Details</a></td> 
  </tr>
</table>
<div style="display:none; border:1px dotted; margin:5px; padding-right:15px; padding-left:15px;" id="detailsChase">
  <h3>Chase Instructions</h3>
  <p>Chase Quick-Pay is an electronic bank-to-bank transfer that will be deposited directly into our savings account set up for our honeymoon. No matter what bank you have an account with, you can use this service free of charge. To utilize this option, you will need to enroll in the (free) Quick-Pay service. Follow the instructions on the page to fill out your information. After you are enrolled and the recipient information is filled out, select the "Send Money" option and fill in the amount that your gifts selected from our Honeymoon Registry totalled, add a note if you desire, and Send! You're done!</p>
  <p>Chase also provides an easy <a href="https://chaseonline.chase.com/content/secure/pt/document/qp_shared_userguide_p.pdf" rel="external" target="_blank">Using QuickPay FAQ</a> for more details.</p>
</div>
<div style="display:none; border:1px dotted; margin:5px; padding-right:15px; padding-left:15px;" id="detailsPaypal">
  <h3>PayPal Instructions</h3>
  <p>PayPal is an electronic third-party transfer service. You can utilize PayPal transfers if you have a PayPal account. PayPal transfers are free if you have a verified bank account (to do this, go to "My Account" and select the "Add Bank Account" option. Verification by the bank can take up to [ ] business days). You are also able to make payments with a credit or debit card, but please keep in mind that PayPal will add on a 3% + $0.30 fee for this method. If you wish to use this option, log in or create an account, and select the "Send Money", using the "Send money to family or friends" option in the "Send Money Online" tab. Use (name:) BLANK and (email:) BLANK for the recipient information, enter the amount of your Honeymoon Registry gift total, select "Continue". The next screen will give you an overview of your payment, allowing you to make changes and add an email message. Confirm that everything about your payment is correct, then select the "Send Money" button.</p>
</div>
<div style="display:none; border:1px dotted; margin:5px; padding-right:15px; padding-left:15px;" id="detailsCheck">
  <h3>Check Instructions</h3>
  <p>Tried and true (and very non-digital), just fill one out from your check book, tuck it into a card (along with your customized PDF certificate!) and drop it into our card box at the wedding!</p>
</div>

<?php
  if ($isCartEmpty)
  {
    echo '<p>Your cart is empty, but you could still print out a <a href="WeddingCertificate.php?gift=&giver=&display=preview" target="_none" rel="external">blank certificate</a> to include in a card, if you\'d like!</p>';
  }
  else
  {
?>
    <p>After you finish, please click the button below to claim these gifts for yourself, and finalize your registry check-out. You will be able to print out a card-sized certificate of the awesome honeymoon activity you've given us! (<a href="WeddingCertificate.php?gift=<?php echo $textGift; ?>&giver=<?php echo $textGiver; ?>&display=preview" target="_none" rel="external">Preview Certificate</a>)</p>
    <form action="registry_check_out.php" method="post">
      <input type="hidden" name="userID" id="userID" value="<?php echo $_SESSION['userID']; ?>" />
      <p style="text-align:center;"><input type="submit" value="Complete Transaction" id="submitted" name="submitted" /></p>
    </form>
<?php
  }
?>
</section>

<?php
} //end is-not-submitted block
?>

<?php include("footer.inc"); ?>