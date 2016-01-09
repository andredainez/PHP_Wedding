<?php

//TODO: Total duplication between guests_edit.php and guests_new.php...needs to be reduced into one file.

/*
notes:

for existing attendees, array of controls names will match db fields, like "isAttending" and "isPlusOne", and index [] will match attendeeID
  else, it will be array of "new_isAttending", "new_isPlusOne", and index [] will simply be numeric
      I know i'm mixing underscores and camel-case, but I'll sacrifice the style for readability in this case. Shrug.

*/

  $isSuperUserPage = true;

  require_once("resources/includePath.inc");
  $page_title = "New User";
  include("header.inc");
  
  //include_once("displayTree.inc"); // Displays a multi-dimensional array as a HTML unordered lists. usage: displayTree($array)

  $promptPlaceholderPlusOne = "Leave blank to be 'Guest'";
  $promptPlaceholderNotPlusOne = "Required";
?>

<?php //if this needs to be an external file: <script src="js/user_admin.js" type="text/javascript" charset="utf-8"></script> ?>
<script type="text/javascript">
  //globals:
  <?php $firstAttID = 0; ?>
  var currAttID = <?php echo $firstAttID; ?>;
  var currTabIndex = 0;
  function changeTabIndex(newTabIndex)
  {
    currTabIndex = newTabIndex;
  }
  // var plusOneIDs = new Array();
  
  //add onClick to all checkboxes on the page:
  //only works in Chrome?
  window.addEventListener("DOMContentLoaded",
  function()
    {
      var elemInputs = document.getElementsByTagName("input");
      for (var i = 0; i < elemInputs.length; ++i)
      {
        if (elemInputs[i].type == "checkbox")
        {
          document.getElementById(elemInputs[i].id).addEventListener("click", changeLabelImgStyle, false);
          if ((elemInputs[i].id).substr(0, 9) == "isPlusOne" || (elemInputs[i].id).substr(0, 13) == "new_isPlusOne")
          {
            // elemInputs[i].attID = parseInt(elemInputs[i].className, 10); //10 == decimal
            // var plusOneID[parseInt(elemInputs[i].className, 10)] = ;
            // document.getElementById(elemInputs[i].id).addEventListener("click", swapPlusOne, false);
            // var newLen = plusOneIDs.push(parseInt(elemInputs[i].className, 10));
            document.getElementById(elemInputs[i].id).addEventListener("click", swapPlusOne, false);
            // document.getElementById(elemInputs[i].id).addEventListener("click", function() {swapPlusOne(plusOneID[parseInt(elemInputs[i].className, 10)])}, false);
          }
        }
      }
    },
  false);
  
  
  function changeLabelImgStyle()
  {
    // alert("this.id = " + this.id);
    var chkbx = document.getElementById(this.id);
    var lbl = document.getElementById("img_" + this.id);
    //alert("chkbx.id = '" + chkbx.id + "', lbl.id = '" + lbl.id + "'");
    if (lbl.className == "imgGray")
      lbl.className = "imgBlack";
    else
      lbl.className = "imgGray";
  }
  
  function swapPlusOne()
  {
    var swapAttID = parseInt(this.id.substr(-2, 1), 10);
    //alert("this.id = " + this.id + "\n\nExtracted ID# = " + swapAttID);
    var chkAttPlusOne = document.getElementById(this.id);
    var idTextAtt = "displayName[" + swapAttID + "]";
    if (this.id.substr(0, 3) == "new")
      idTextAtt = "new_" + idTextAtt;
    var textAttName = document.getElementById(idTextAtt);
    if (textAttName.required == true)
    {
      textAttName.required = false;
      textAttName.placeholder = "<?php echo $promptPlaceholderPlusOne; ?>";
    }
    else
    {
      textAttName.required = true;
      textAttName.placeholder = "<?php echo $promptPlaceholderNotPlusOne; ?>";
    }
  }

  //http://viralpatel.net/blogs/dynamically-add-remove-rows-in-html-table-using-javascript/
  function addAttRow()
  {
    currAttID++;
  
    //alert("before");
    var table = document.getElementById("editUserTable");
    var numCurrRow = table.rows.length - 4;
    
    var rowDivider = table.insertRow(numCurrRow);
    var cellHR = rowDivider.insertCell(0);
    cellHR.colSpan = "2";
    var elementHR = document.createElement("hr");
    cellHR.appendChild(elementHR);
    
    numCurrRow++;
    var rowID = table.insertRow(numCurrRow);
    var cellIDLabel = rowID.insertCell(0);
    var elementIDLabel = document.createElement("label");
    elementIDLabel.htmlFor = "new_attendeeID[" + currAttID + "]";
    elementIDLabel.innerHTML = "Attendee ID: ";
    cellIDLabel.appendChild(elementIDLabel);
    var cellIDInput = rowID.insertCell(1);
    var elementIDInput = document.createElement("input")
    elementIDInput.type = "text";
    elementIDInput.name = "new_attendeeID[" + currAttID + "]";
    elementIDInput.id = "new_attendeeID[" + currAttID + "]";
    elementIDInput.value = "";
    elementIDInput.placeholder = "auto_increment"
    elementIDInput.disabled = true;
    elementIDInput.size = 10;
    elementIDInput.maxLength = 254;
    elementIDInput.tabIndex = currTabIndex++;
    cellIDInput.appendChild(elementIDInput);
    
    numCurrRow++;
    var rowName = table.insertRow(numCurrRow);
    var cellNameLabel = rowName.insertCell(0);
    var elementNameLabel = document.createElement("label");
    elementNameLabel.htmlFor = "new_displayName[" + currAttID + "]";
    elementNameLabel.innerHTML = "Attendee Name: ";
    cellNameLabel.appendChild(elementNameLabel);
    var cellNameInput = rowName.insertCell(1);
    var elementNameInput = document.createElement("input")
    elementNameInput.type = "text";
    elementNameInput.name = "new_displayName[" + currAttID + "]";
    elementNameInput.id = "new_displayName[" + currAttID + "]";
    elementNameInput.value = "";
    elementNameInput.placeholder = "<?php echo $promptPlaceholderNotPlusOne; ?>";
    elementNameInput.required = true;
    elementNameInput.size = 25;
    elementNameInput.maxLength = 254;
    elementNameInput.tabIndex = currTabIndex++;
    cellNameInput.appendChild(elementNameInput);
    
    numCurrRow++;
    var rowCheckboxes = table.insertRow(numCurrRow);
    var cellPlusOne = rowCheckboxes.insertCell(0);
    var elementIsAtt = document.createElement("input");
    elementIsAtt.type = "hidden";
    elementIsAtt.name = "new_isAttending[" + currAttID + "]";
    elementIsAtt.id = "new_isAttending[" + currAttID + "]";
    elementIsAtt.value = "";
    elementIsAtt.size = 1;
    elementIsAtt.maxLength = 1;
    elementIsAtt.tabIndex = currTabIndex++;
    cellPlusOne.appendChild(elementIsAtt);
    
    var elementPlusOneLabel = document.createElement("label");
    elementPlusOneLabel.htmlFor = "new_isPlusOne[" + currAttID + "]";
    var elementPlusOneImg = document.createElement("img");
    elementPlusOneImg.src = "resources/icons/plusOne.svg";
    elementPlusOneImg.width = "15";
    elementPlusOneImg.height = "15";
    elementPlusOneImg.id = "img_new_isPlusOne[" + currAttID + "]";
    elementPlusOneImg.name = "img_new_isPlusOne[" + currAttID + "]";
    elementPlusOneImg.className = "imgGray";
    elementPlusOneLabel.appendChild(elementPlusOneImg);
    cellPlusOne.appendChild(document.createTextNode(" "));
    cellPlusOne.appendChild(elementPlusOneLabel);
    cellPlusOne.appendChild(document.createTextNode(" "));
    var elementPlusOneInput = document.createElement("input");
    elementPlusOneInput.type = "checkbox";
    elementPlusOneInput.name = "new_isPlusOne[" + currAttID + "]";
    elementPlusOneInput.id = "new_isPlusOne[" + currAttID + "]";
    elementPlusOneInput.attID = currAttID;
    elementPlusOneInput.class = currAttID;
    elementPlusOneInput.tabIndex = currTabIndex++;
    cellPlusOne.appendChild(document.createTextNode(" "));
    cellPlusOne.appendChild(elementPlusOneInput);
    cellPlusOne.appendChild(document.createTextNode(" "));
    document.getElementById(elementPlusOneInput.id).addEventListener("click", changeLabelImgStyle, false);
    // var newLen = plusOneIDs.push(currAttID);
    document.getElementById(elementPlusOneInput.id).addEventListener("click", swapPlusOne, false);

    
    var cellChildren = rowCheckboxes.insertCell(1);
    
    var elementChildLabel = document.createElement("label");
    elementChildLabel.htmlFor = "new_isChild[" + currAttID + "]";
    var elementChildImg = document.createElement("img");
    elementChildImg.src = "resources/icons/rsvp_children.png";
    elementChildImg.width = "15";
    elementChildImg.height = "15";
    elementChildImg.id = "img_new_isChild[" + currAttID + "]";
    elementChildImg.name = "img_new_isChild[" + currAttID + "]";
    elementChildImg.className = "imgGray";
    elementChildLabel.appendChild(document.createTextNode(" "));
    elementChildLabel.appendChild(elementChildImg);
    elementChildLabel.appendChild(document.createTextNode(" "));
    cellChildren.appendChild(elementChildLabel);
    var elementChildInput = document.createElement("input");
    elementChildInput.type = "checkbox";
    elementChildInput.name = "new_isChild[" + currAttID + "]";
    elementChildInput.id = "new_isChild[" + currAttID + "]";
    elementChildInput.tabIndex = currTabIndex++;
    cellChildren.appendChild(document.createTextNode(" "));
    cellChildren.appendChild(elementChildInput);
    cellChildren.appendChild(document.createTextNode(" "));
    document.getElementById(elementChildInput.id).addEventListener("click", changeLabelImgStyle, false);
    
    var elementInfantLabel = document.createElement("label");
    elementInfantLabel.htmlFor = "new_isInfant[" + currAttID + "]";
    var elementInfantImg = document.createElement("img");
    elementInfantImg.src = "resources/icons/rsvp_pacifier.png";
    elementInfantImg.width = "15";
    elementInfantImg.height = "15";
    elementInfantImg.id = "img_new_isInfant[" + currAttID + "]";
    elementInfantImg.name = "img_new_isInfant[" + currAttID + "]";
    elementInfantImg.className = "imgGray";
    elementInfantLabel.appendChild(document.createTextNode(" "));
    elementInfantLabel.appendChild(elementInfantImg);
    elementInfantLabel.appendChild(document.createTextNode(" "));
    cellChildren.appendChild(elementInfantLabel);
    var elementInfantInput = document.createElement("input");
    elementInfantInput.type = "checkbox";
    elementInfantInput.name =  "new_isInfant[" + currAttID + "]";
    elementInfantInput.id =  "new_isInfant[" + currAttID + "]";
    elementInfantInput.tabIndex = currTabIndex++;
    cellChildren.appendChild(document.createTextNode(" "));
    cellChildren.appendChild(elementInfantInput);
    cellChildren.appendChild(document.createTextNode(" "));
    document.getElementById(elementInfantInput.id).addEventListener("click", changeLabelImgStyle, false);


    //alert("inserted");
  }
</script>




<section class="content">
<?php
if (isset($_REQUEST['submitted'])) 
{
  //echo displayTree($_REQUEST);
  //print_r($_REQUEST);
  
  require_once("classWeddingUser.inc");
  $userCreator = new weddingUser();
  
  if(isset($_REQUEST['username']) && $_REQUEST['username'] != '')
  {
    if ($userCreator->usernameExists($_REQUEST['username']))
      $errors['username'] = 'Username already exists';
    else
      $username = $_REQUEST['username'];
  }
  else
    $errors['username'] = 'Invalid Username';
  if(isset($_REQUEST['passwordPlaintext']) && $_REQUEST['passwordPlaintext'] != '')
    $pw = $_REQUEST['passwordPlaintext'];
  else
    $errors['passwordPlaintext'] = 'Invalid Password';
  if(isset($_REQUEST['name']) && $_REQUEST['name'] != '')
    $fullName = $_REQUEST['name'];
  else
    $errors['name'] = 'Invalid Main Display Name';
  if(isset($_REQUEST['address']) && $_REQUEST['address'] != '')
    $address = $_REQUEST['address'];
  else
    $address = null;
  //else
  //  $errors['address'] = 'Invalid Address';
  
  
  if (!isset($errors))
  {
    if($newUserID = $userCreator->newUser($username, $pw, $fullName, $address))
    {
      //display success message:
      ?>
      <h2>Created "<?php echo $fullName; ?>"</h2>
      <p>
        Username: <?php echo $username; ?>, Password: <?php echo $pw; ?>, SQL ID: <?php echo $newUserID; ?><br />
        Address: <?php echo $address; ?>
      </p>
      <h3>Attendees:</h3>
      <p>
      <?php
    
      //for update.php: second foreach for non-"new_"
      
      foreach($_REQUEST['new_displayName'] as $attID => $new_displayName)
      {
        if (isset($_REQUEST['new_isPlusOne'][$attID]) && $_REQUEST['new_isPlusOne'][$attID] == 'on')
          $isPlusOne = true;
        else
          $isPlusOne = false;
        if (isset($_REQUEST['new_isInfant'][$attID]) && $_REQUEST['new_isInfant'][$attID] == 'on')
          $isInfant = true;
        else
          $isInfant = false;
        if (isset($_REQUEST['new_isChild'][$attID]) && $_REQUEST['new_isChild'][$attID] == 'on')
          $isChild = true;
        else
          $isChild = false;
        if (isset($new_displayName) && $new_displayName != '')
        {
          $name = $new_displayName;
        }
        else //displayName is blank
        {
          if ($isPlusOne)
            $name = null;
          else
            $errors['new_displayName[' . $attID . ']'] = 'Invalid Attendee Display Name';
        }
      
        if ($currNewAttID = $userCreator->newAttendee($newUserID, $isPlusOne, $isChild, $isInfant, $name))
        {
          //display success for each attendee:
          ?>
            <?php echo (($isPlusOne) ? '<img src="resources/icons/plusOne.svg" width="15" height="15" />' : ' '); ?>
            <?php echo (($isChild) ? '<img src="resources/icons/rsvp_children.png" width="15" height="15" />' : ' '); ?>
            <?php echo (($isInfant) ? '<img src="resources/icons/rsvp_pacifier.png" width="15" height="15" />' : ' '); ?>
            Name: <?php echo ((isset($name)) ? $name : '<em>not given</em>'); ?> 
            (SQL ID: <?php echo $currNewAttID; ?>)
            <br />
          <?php
          //echo 'DEBUG: New attendee [' . $attID . '] created! (SQL ID: ' . $currNewAttID. ')<br /><br />';
        }
        else
        {
          $errors['create_new_attendee[' . $attID . ']'] = 'Error during new attendee [' . $attID . '] create';
        }
      }
      echo '</p>';
    }
    else
    {
      $errors['create_new_user'] = 'Error during new user create';
    }
  }
  
  //***********************************  DEBUG
  if (isset($errors))
  {
    echo '<h2>Errors during creation</h2>';
    foreach($errors as $key => $value)
    {
      echo 'Error[' . $key . '] => ' . $value . '<br />';
    }
  }
}
else
{

}
?>
  <form action="guests_new.php" method="post" id="newGuest">
    <table class="formTable" id="editUserTable" name="editUserTable">
    <?php $tabIndex = 1; ?>
    <tr>
      <td><label for="userID">userID: </label></td>
      <td><input type="text" name="userID" id="userID" value="" placeholder="auto_increment" size="10" maxlength="254" tabindex="<?php echo $tabIndex++; ?>" disabled /></td>
    </tr>
    <tr>
      <td><label for="username">Username: </label></td>
      <td><input type="text" name="username" id="username" value="" placeholder="Login Name" size="25" maxlength="254" required tabindex="<?php echo $tabIndex++; ?>" /></td>
    </tr>
    <tr>
      <td><label for="passwordPlaintext">Password: </label></td>
      <td><input type="text" name="passwordPlaintext" id="passwordPlaintext" value="" placeholder="Fun Password" size="25" required maxlength="254" tabindex="<?php echo $tabIndex++; ?>" /></td>
    </tr>
    <tr>
      <td><label for="name">Display Name: </label></td>
      <td><input type="text" name="name" id="name" value="" placeholder="Name Shown to User" size="25" maxlength="254" required tabindex="<?php echo $tabIndex++; ?>" /></td>
    </tr>
    <tr>
      <td><label for="email">Email: </label></td>
      <td><input type="text" name="email" id="email" value="" size="25" maxlength="254" tabindex="<?php echo $tabIndex++; ?>" placeholder="User Inputted" disabled /></td>
    </tr>
    <tr>
      <td><label for="address">Mailing Address: </label></td>
      <td><input type="text" name="address" id="address" value="" placeholder="Address" size="25" maxlength="254" tabindex="<?php echo $tabIndex++; ?>" /></td>
    </tr>
    
    <tr>
      <td colspan="2"><hr /></td>
    </tr>
    <tr>
      <td><label for="new_attendeeID[<?php echo $firstAttID; ?>]">Attendee ID: </label></td>
      <td><input type="text" name="new_attendeeID[<?php echo $firstAttID; ?>]" id="new_attendeeID[<?php echo $firstAttID; ?>]" value="" placeholder="auto_increment" disabled size="10" maxlength="254" tabindex="<?php echo $tabIndex++; ?>" /></td>
    </tr>
    <tr>
      <td><label for="new_displayName[<?php echo $firstAttID; ?>]">Attendee Name: </label></td>
      <td><input type="text" name="new_displayName[<?php echo $firstAttID; ?>]" id="new_displayName[<?php echo $firstAttID; ?>]" required placeholder="<?php echo $promptPlaceholderNotPlusOne; ?>" value="" size="25" maxlength="254" tabindex="<?php echo $tabIndex++; ?>" /></td>
    </tr>
    <tr>
      <td>
        <input type="hidden" name="new_isAttending[<?php echo $firstAttID; ?>]" id="new_isAttending[<?php echo $firstAttID; ?>]" value="" size="1" maxlength="1" tabindex="<?php echo $tabIndex++; ?>" />
        <label for="new_isPlusOne[<?php echo $firstAttID; ?>]"><img src="resources/icons/plusOne.svg" width="15" height="15" id="img_new_isPlusOne[<?php echo $firstAttID; ?>]" name="img_new_isPlusOne[<?php echo $firstAttID; ?>]" class="imgGray" /></label>
        <input type="checkbox" disabled="true" name="new_isPlusOne[<?php echo $firstAttID; ?>]" id="new_isPlusOne[<?php echo $firstAttID; ?>]" tabindex="<?php echo $tabIndex++; ?>" />
      </td>
      <td>
        <label for="new_isChild[<?php echo $firstAttID; ?>]"><img src="resources/icons/rsvp_children.png" width="15" height="15" name="img_new_isChild[<?php echo $firstAttID; ?>]" id="img_new_isChild[<?php echo $firstAttID; ?>]" class="imgGray" /></label>
        <input type="checkbox" name="new_isChild[<?php echo $firstAttID; ?>]" id="new_isChild[<?php echo $firstAttID; ?>]" tabindex="<?php echo $tabIndex++; ?>" />
        <label for="new_isInfant[<?php echo $firstAttID; ?>]"><img src="resources/icons/rsvp_pacifier.png" width="15" height="15" name="img_new_isInfant[<?php echo $firstAttID; ?>]" id="img_new_isInfant[<?php echo $firstAttID; ?>]" class="imgGray" /></label>
        <input type="checkbox" name="new_isInfant[<?php echo $firstAttID; ?>]" id="new_isInfant[<?php echo $firstAttID; ?>]" tabindex="<?php echo $tabIndex++; ?>" />

      </td>
    </tr>
    
    <tr>
      <td colspan="2"><hr /></td>
    </tr>
    <tr>
      <td><input type="button" onClick="addAttRow()" name="addAttendee" id="addAttendee" value="Add Attendee" tabindex="9000" /></td>
      <td></td>
    </tr>
    <tr>
      <td colspan="2"><hr /></td>
    </tr>
    <tr>
      <td colspan="2"><input type="submit" name="submitted" id="submitted" value="Create User" tabindex="9042" /></td>
    </tr>
    </table>
    <?php
    /*
    <tr>
      <td><label for="">: </label></td>
      <td><input type="text" name="" id="" value="" placeholder="" size="25" maxlength="254" tabindex="<?php echo $tabIndex++; ?>" /></td>
    </tr>
    */
    ?>
    <script type="text/javascript">
      changeTabIndex(<?php echo $tabIndex; ?>);
    </script>
  </form>

</section>


<?php include("footer.inc"); ?>