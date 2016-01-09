<?php
$isUserPage = true;
$usePrettyPhoto = true;
$useJQuery = true;

require_once("resources/includePath.inc");
$page_title = "Honeymoon Registry";
include("header.inc");
require_once('dbdata.inc');
$textGiver = $_SESSION['name'];

//** Check to See if a previous transaction already exists **
$dbc = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT); //from dbdata.inc
if ($dbc->connect_errno)
{
  echo '<p>DB Connection Error.</p>';
}
else
{
$query = 'select transactionID, userID from transactions where userID = "';
$query .= $dbc->real_escape_string($_SESSION['userID']) . '";';
if (!$results = $dbc->query($query))
{
  echo '<p>Error: Query to check if transactions exist failed.</p>';
}
else
{
  if ($results->num_rows > 0) //records do exist
  {
    //user has already checked out at least once
    
    
    $query = 'SELECT transactionItems.inventoryID as inventoryID, transactionItems.qty as qty, ';
    $query .= 'storeItems.title as title, storeItems.description as description, storeItems.basePrice as basePrice, storeItems.imageFilename as imageFilename, storeItems.caption as caption ';
    $query .= 'FROM transactions right join transactionItems on transactions.transactionID = transactionItems.transactionID ';
    $query .= 'right join inventoryItems on transactionItems.inventoryID = inventoryItems.inventoryID ';
    $query .= 'right join storeItems on inventoryItems.storeItemID = storeItems.storeItemID ';
    $query .= 'where transactions.userID = "';
    $query .= $dbc->real_escape_string($_SESSION['userID']) . '";';

    if ($results_transaction_items = $dbc->query($query))
    {
      if ($results_transaction_items->num_rows < 1)
      {
        //transaction doesn't contain any valid items; display link to blank certificate
        ?>
        <p>Error: Your login has a previously saved honeymoon registry transaction, but we can't find any items for you! Oops.</p>
        <p>No worries, though. You can still print out a <a href="WeddingCertificate.php?gift=&giver=&display=preview" target="_none" rel="external">Blank Certificate</a> to give a honeymoon gift. Or, you can contact us and we can fix the problem for you.</p>
        <?php
      }
      else
      {
        ?>
          <p>Welcome back to our registry! Here are the items that you have selected as your wedding gift:</p>
          <div class="quest" id="boxTransactions">
            <h3 class="fancy" style="text-align:center; margin-top:5px; margin-bottom:5px;">Previously Gifted Items</h3>
            <table id="tableTransactions" style="width:100%">
              <thead>
                <tr>
                  <td style="border-bottom:1px dotted #555555;"></td>
                  <td style="border-bottom:1px dotted #555555;">Item</td>
                  <td style="border-bottom:1px dotted #555555;">Qty</td>
                  <td style="border-bottom:1px dotted #555555;">Value</td>
                </tr>
              </thead>
              <tbody id="tbodyTransactions">
        <?php
        $textGift = '';
        
        $isCartEmpty = false;
        $total = 0;
        $countRows = $results_transaction_items->num_rows;
        while ($row = $results_transaction_items->fetch_assoc())
        {
        
          if ($results_transaction_items->num_rows == 1)
          {
            ;
          }
          elseif ($countRows == 1)
          {
            if ($results_transaction_items->num_rows == 2)
              $textGift .= ' and ';
            else
              $textGift .= ', and ';
          }
          elseif ($countRows < $results_transaction_items->num_rows)
          {
            $textGift .= ', ';
          }
          
          if ($row['qty'] > 1)
            $textGift .= $row['qty'] . ' ';
          
          $textGift .= $row['title'];
          
          --$countRows;
          ?>
    
              <tr>
                <td><a title="<?php echo $row['caption']; ?>" href="images/storeItems/<?php echo $row['imageFilename']; ?>" rel="prettyPhoto" class="noUnderline"><img src="images/storeItems/thumb_<?php echo $row['imageFilename']; ?>" width="75" height="50" /><br /></a></td>
                <td><strong><?php echo $row['title']; ?></strong><br /><small><?php echo $row['description']; ?></small></td>
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
                <td colspan="2" style="border-top:1px dotted #555555;"></td>
                <td style="border-top:1px dotted #555555;">Total: </td>
                <td id="tdCartTotal" style="border-top:1px dotted #555555;">$<?php echo $total / 100; ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
        
        <p>Here is your customized <a href="WeddingCertificate.php?gift=<?php echo $textGift; ?>&giver=<?php echo $textGiver; ?>&display=preview" target="_none" rel="external">Honeymoon Registry Certificate</a> for you to print or <a href="WeddingCertificate.php?gift=<?php echo $textGift; ?>&giver=<?php echo $textGiver; ?>&display=download">download</a>.</p>
        <p><small>(You can also print a <a href="WeddingCertificate.php?gift=&giver=&display=preview" target="_none" rel="external">Blank Certificate</a> if you prefer to write in different information.)</small></p>
        <?php
      }
    }
    else
    {
      echo '<p>Error: Transaction query failed.</p>';
    }
  }
  else //user has no saved transactions, therefore they have never checked out before
  {



?>

<script type="text/javascript" charset="utf-8">

var cartItems = new Array();
var cartCount = 0;
var sessionUserID = <?php echo $_SESSION['userID']; ?>;
var cartModified = false;

$(document).ready(function()
{
  loadCartFromDB(sessionUserID);
  
  $("#submitted").click(function()
  {
    console.log("beginning save.");
    if (saveCartToDB(sessionUserID))
    {
      console.log("save has returned.");
      // alert("save has returned.");
      window.location.href = 'registry_check_out.php';
      return true;
    }
    else
    {
      console.log("ajax error.");
      // alert("ajax error.");
      return false;
    }
  });
  
  <?php //from: https://developer.mozilla.org/en-US/docs/Web/API/window.setInterval?redirectlocale=en-US&redirectslug=DOM%2Fwindow.setInterval#Dangerous_usage ?>
  (function cartSaveLoop()
  {
    setTimeout(function()
    {
      if (cartModified)
      {
        saveCartToDB(sessionUserID);
        $("#divSaved").fadeIn(600).delay(1000).fadeOut(600);
      }
      cartSaveLoop();
    }, 15000);
  })();
});

function addToCart(inventoryID)
{
  if($("#qty\\[" + inventoryID + "\\]").attr("value") > 0)
  {
    //alert($("#qty\\[" + inventoryID + "\\]").attr("value"));
    $("#qty\\[" + inventoryID + "\\]").attr("value", $("#qty\\[" + inventoryID + "\\]").attr("value") - 1);
    $("#spanQty\\[" + inventoryID + "\\]").html($("#qty\\[" + inventoryID + "\\]").attr("value"));
    if (cartItems[inventoryID] >= 0)
      cartItems[inventoryID]++;
    else
      cartItems[inventoryID] = 1;
  }
  else
  {
    //error message?? no items available
  }
  
  if (window.console)
    console.log("cartItems: " + cartItems);
  
  cartModified = true;
  updateCart();
}

function removeFromCart(inventoryID)
{
  if(cartItems[inventoryID] > 0)
  {
    cartItems[inventoryID]--;
    $("#qty\\[" + inventoryID + "\\]").attr("value", parseInt($("#qty\\[" + inventoryID + "\\]").attr("value")) + 1);
    $("#spanQty\\[" + inventoryID + "\\]").html($("#qty\\[" + inventoryID + "\\]").attr("value"));
  }
  else
    cartItems[inventoryID] = 0;
  
  if (window.console)
    console.log("cartItems: " + cartItems);
  
  cartModified = true;
  updateCart();
}

function updateCart()
{
  // if (window.console)
    // console.log(cartItems.length);
  var cartTotal = 0;
  var countItems = 0;
  $("#tbodyCart tr").remove();
  for (var i = 0; i <= cartItems.length; ++i)
  {
    // console.log("i = " + i);
    // console.log("length = " + cartItems.length);
    if (cartItems[i] > 0)
    {
      // if (window.console)
      // console.log("InventoryID[" + i + "] = " + cartItems[i]);
      // console.log($("#title\\[" + i + "\\]").attr("value"));
      var trNew = $("<tr/>");
      trNew.append($("<td/>").append(cartItems[i]));
      trNew.append($("<td/>").append($("#title\\[" + i + "\\]").attr("value")));
      trNew.append($("<td/>").append("$" + $("#price\\[" + i + "\\]").attr("value") / 100));
      var aDeleteItem = $("<a/>", {"title": "Remove all from Cart",
                            "href": "javascript:void(0)",
                            "class": "noUnderline",
                            click: function (invID) {
                              return function () {
                                removeFromCart(invID);
                              };
                            } (i)
                            }).append("X");
      trNew.append($("<td/>").append(aDeleteItem));
      
      $("#tbodyCart").append(trNew);
      
      cartTotal += cartItems[i] * $("#price\\[" + i + "\\]").attr("value");
      countItems++;
    }
  }
  
  if (countItems > 0)
  {
    $("#tdCartTotal").html("$" + cartTotal / 100);
    
    $("#boxText").hide(600);
    $("#boxCart").show(600);
  }
  else
  {
    $("#tdCartTotal").html("");
    $("#tbodyCart").append("<tr><td colspan=\"4\"><em>Cart empty</em></td></tr>");
    
    $("#boxCart").hide(600);
    $("#boxText").show(600);
  }
  
  $(".inputInvID").each(function()
  {
    // alert($(this).val());
    // alert(parseInt($("#qty\\[" + $(this).val() + "\\]").attr("value")));
    if (parseInt($("#qty\\[" + $(this).val() + "\\]").attr("value")) <= 0)
    {
      $("#buttonAdd\\[" + $(this).val() + "\\]").attr("disabled", "disabled");
    }
    else
    {
      $("#buttonAdd\\[" + $(this).val() + "\\]").removeAttr("disabled");
    }
  });
  
  console.log("-------------------");
}

function saveCartToDB(userID)
{
  var success = false;
  $.ajax(
  {
    type: "POST",
    url: "js/registry_save_cart.php",
    async: false,
    data: {"arrayCart": cartItems, "userID": userID}
  }
  ).done(function(results)
  {
    if (results)
    {
      console.log("Error has been returned: " + results['error']);
      // return false;
      success = false;
    }
    else //no error
    {
      console.log("No error returned from AJAX.");
      // return true;
      success = true;
    }
  }
  ).fail(function(jqXHR, textStatus)
  {
    console.log('AJAX Request Failed: ' + textStatus);
    // return false;
    success = false;
  }
  );
  console.log("Reached end of function after AJAX.");
  if (success)
  {
    console.log("success == true");
    return true;
  }
  else
  {
    console.log("success == false");
    return false;
  }
}

function loadCartFromDB(userID)
{
  $.ajax(
  {
    type: "POST",
    url: "js/registry_load_cart.php",
    data: {"userID": userID}
  }
  ).done(function(results)
  {
    if (results['error'] !== null && typeof results['error'] != 'undefined')
    {
      alert("Error has been returned: " + results['error']);
    }
    else
    {
      jQuery.each(results, function(currInvID, currQty)
      {
        cartItems[currInvID] = currQty;
        $("#qty\\[" + currInvID + "\\]").attr("value", (((currMath = (parseInt($("#qty\\[" + currInvID + "\\]").attr("value")) - currQty)) > 0) ? currMath : 0));
        $("#spanQty\\[" + currInvID + "\\]").html($("#qty\\[" + currInvID + "\\]").attr("value"));
      });
      updateCart();
    }
    // else //no error
    // {
      // alert('no error\r\n' + results);
    // }
  }
  ).fail(function(jqXHR, textStatus)
  {
    alert('AJAX Request Failed: ' + textStatus);
  }
  );
}

</script>

<section class="content">
<div id="boxText">
  <h3>Welcome to our honeymoon registry!</h3>
  <p>When we first started to contemplate our future together, both of us agreed that we wanted to keep our lives exciting. Going on adventures and exploring new places has helped us grow together and made for a relationship rich in memories and great experiences, and we want to see that continue to strengthen our marriage. As you contribute to our registry, you can help us fly to our destination (and boy, will our arms be tired!), fund individual or multiple nights at our hotels, take us out to a fancy dinner, or help keep our adrenaline pumping with plenty of day trips and excursions. The choice is yours! We can't wait to see what's waiting for us!</p>
  <?php //<p><em><a href="registry_check_out.php" title="Complete gift with empty cart?">(Skip past checkout)</a></em></p> ?>
</div>

<?php //<!--<form id="formCart" name="formCart" action="registry_check_out.php" method="post">--> ?>

  <p> </p>
  <div class="quest" id="boxCart" style="display:none;">
    <h3 class="fancy" style="text-align:center; margin-top:5px; margin-bottom:5px;">Honeyfund Cart</h3>
    <?php
    //<table id="tableCart" style="min-width:217px;">
    ?>
    <table id="tableCart" style="width:100%">
      <thead>
        <tr>
          <td style="border-bottom:1px dotted #555555;">Qty</td>
          <td style="border-bottom:1px dotted #555555;">Item</td>
          <td style="border-bottom:1px dotted #555555;">Value</td>
          <td style="border-bottom:1px dotted #555555;">Remove</td>
        <tr>
      </thead>
      <tbody id="tbodyCart">
        <tr>
          <td colspan="4"><em>Cart empty</em></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" style="text-align: center; border-top:1px dotted #555555;"><div id="divSaved" style="display:none;"><em>Cart Saved</em></div></td>
          <td id="tdCartTotal" style="border-top:1px dotted #555555;"></td>
          <td style="text-align:right; border-top:1px dotted #555555;"><input id="submitted" name="submitted" type="button" value="Check Out" /></td>
        </tr>
      </tfoot>
    </table>
  </div>
<!--</form>-->
  
<?php
//set up database connection to display store inventory


$query = 'select inventoryItems.inventoryID as inventoryID, inventoryItems.qty as qty, inventoryItems.qtyMax as qtyMax, storeItems.title as title, storeItems.description as description, storeItems.basePrice as price, storeItems.imageFilename as imageFilename , storeItems.caption as caption from inventoryItems left join storeItems on inventoryItems.storeItemID = storeItems.storeItemID order by inventoryItems.orderByNum;';
/*
inventoryItems.inventoryID as inventoryID,
inventoryItems.qty as qty, 
inventoryItems.qtyMax as qtyMax,
storeItems.title as title, 
storeItems.description as description, 
storeItems.basePrice as price, 
storeItems.imageFilename as imageFilename , 
storeItems.caption as caption from inventoryItems
*/
if ($results = $dbc->query($query))
{
  while ($row = $results->fetch_assoc())
  {
    ?>
    <hr />
    <input type="hidden" id="inventoryID[<?php echo $row['inventoryID']; ?>]" value="<?php echo $row['inventoryID']; ?>" class="inputInvID" />
    <input type="hidden" id="qty[<?php echo $row['inventoryID']; ?>]" value="<?php echo $row['qty']; ?>" />
    <input type="hidden" id="title[<?php echo $row['inventoryID']; ?>]" value="<?php echo $row['title']; ?>" />
    <input type="hidden" id="price[<?php echo $row['inventoryID']; ?>]" value="<?php echo $row['price']; ?>" />
    <figure class="imageBox left caption" style="margin-right:10px;"><a title="<?php echo $row['caption']; ?>" href="images/storeItems/<?php echo $row['imageFilename']; ?>" rel="prettyPhoto"><img src="images/storeItems/thumb_<?php echo $row['imageFilename']; ?>" width="168" height="115" /><br /></a></figure>
    <h2><?php echo $row['title']; ?></h2>
    <p>
      $<?php echo (intval($row['price']) / 100) ?> - <span id="spanQty[<?php echo $row['inventoryID']; ?>]"><?php echo $row['qty']; ?> of <?php echo $row['qtyMax']; ?></span> Available - 
      <input onClick="addToCart(<?php echo $row['inventoryID']; ?>)" <?php //echo (($row['qty'] > 0) ? '' : 'disabled'); ?> id="buttonAdd[<?php echo $row['inventoryID']; ?>]" type="button" value="Add to Cart" />
    </p>
    <p><?php echo $row['description']; ?></p>
    
    <?php
  }
}
else
{
  echo '<p>Error: Inventory query failed to return results.</p>';
}
?>
</section>
<?php
} //end check if already checked out once
} //end transaction check query fail
} //end $dbc connect errno
?>

<?php include("footer.inc"); ?>