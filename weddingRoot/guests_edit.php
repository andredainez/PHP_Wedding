<?php

/*
notes:

for existing attendees, array of controls names will match db fields, like "isAttending" and "isPlusOne", and index [] will match attendeeID
  else, it will be array of "new_isAttending", "new_isPlusOne", and index [] will simply be numeric
      I know i'm mixing underscores and camel-case, but I'll sacrifice the style for readablity in this case. Shrug.

*/

  $isSuperUserPage = true;

  require_once("resources/includePath.inc");
  $page_title = "Edit User";
  include("header.inc");
  
  require_once("classWeddingUser.inc");
  $userEditor = new weddingUser();
  
  $userID = 0;
  if (isset($_REQUEST['userID']) && is_numeric($_REQUEST['userID']))
  {
    $userID = $_REQUEST['userID'];
    // if($userEditor->isUserAdminByID($userID))
    // {
      // echo '<p>Error #1337: Admin users cannot be edited with this form!</p>';
      // include("footer.inc");
      // exit();
    // }
  }
  else
  {
    echo '<p>Error #0x2A: User ID not specified!</p>';
    include("footer.inc");
    exit();
  }
  

  $promptPlaceholderPlusOne = "Leave blank to be 'Guest'";
  $promptPlaceholderNotPlusOne = "Required (If not +1)";
  
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
  
  
  
  // function changePlusOneStyle(attID)
  // {
    // var plusImage = document.getElementById("imgPlusOne[" + attID + "]");
    // if (plusImage.className == "imgGray")
      // plusImage.className = "imgBlack";
    // else
      // plusImage.className = "imgGray";
  // }
  // function changeChildStyle(attID)
  // {
    // var childImage = document.getElementById("imgChild[" + attID + "]");
    // if (childImage.className == "imgGray")
      // childImage.className = "imgBlack";
    // else
      // childImage.className = "imgGray";
  // }
  // function changeInfantStyle(attID)
  // {
    // var infantImage = document.getElementById("imgInfant[" + attID + "]");
    // if (infantImage.className == "imgGray")
      // infantImage.className = "imgBlack";
    // else
      // infantImage.className = "imgGray";
  //}

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
  if(isset($_REQUEST['username']) && $_REQUEST['username'] != '')
  {
    // if ($userID != $userEditor->usernameExists($_REQUEST['username']))
    // if ($retrievedID = $userEditor->usernameExists($_REQUEST['username']) && $retrievedID != $userID)
    if ($retrievedID = $userEditor->usernameExists($_REQUEST['username']))
    {
      if ($retrievedID == $userID)
        $username = $_REQUEST['username'];
      else
        $errors['username'] = 'Username already assigned to a different user.';
    }
    else //username did not exist
    {
      $username = $_REQUEST['username'];
    }
  }
  else
  {
    $errors['username'] = 'Blank Username';
  }
  if(isset($_REQUEST['passwordPlaintext']) && $_REQUEST['passwordPlaintext'] != '')
    $pw = $_REQUEST['passwordPlaintext'];
  else
    $errors['passwordPlaintext'] = 'Blank Password';
  if(isset($_REQUEST['name']) && $_REQUEST['name'] != '')
    $name = $_REQUEST['name'];
  else
    $errors['name'] = 'Blank Main Display Name';
  if(isset($_REQUEST['address']) && $_REQUEST['address'] != '')
    $address = $_REQUEST['address'];
  else
    $address = null;
  if(isset($_REQUEST['notesRSVP']) && $_REQUEST['notesRSVP'] != '')
    $notesRSVP = $_REQUEST['notesRSVP'];
  else
    $notesRSVP = null;
  if(isset($_REQUEST['email']) && $_REQUEST['email'] != '')
    $email = $_REQUEST['email'];
  else
    $email = null;
  if(isset($_REQUEST['gift']) && $_REQUEST['gift'] != '')
    $gift = $_REQUEST['gift'];
  else
    $gift = null;
  if(isset($_REQUEST['thankYouCardNotes']) && $_REQUEST['thankYouCardNotes'] != '')
    $thankYouCardNotes = $_REQUEST['thankYouCardNotes'];
  else
    $thankYouCardNotes = null;
  if (isset($_REQUEST['isRSVP']) && $_REQUEST['isRSVP'] == 'on')
    $isRSVP = true;
  else
    $isRSVP = false;
  if (isset($_REQUEST['isThankYouSent']) && $_REQUEST['isThankYouSent'] == 'on')
    $isThankYouSent = true;
  else
    $isThankYouSent = false;
  // if (isset($_REQUEST['removeLoginTime']) && $_REQUEST['removeLoginTime'] == 'on')
    // $removeLoginTime = true;
  // else
    // $removeLoginTime = false;
  
  if (!isset($errors)) //note: only have examined errors regarding user now; attendees will be processed later
  {
    //commit the edit to the database:
    if($userEditor->editUser($userID, $username, $pw, $isRSVP, $name, $notesRSVP, $email, $gift, $address, $isThankYouSent, $thankYouCardNotes))
    {
      // if ($removeLoginTime)
      // {
        
      // }
    
      //existing attendees
      foreach($_REQUEST['displayName'] as $attID => $displayName)
      {
        if (isset($_REQUEST['isAttending'][$attID]) && $_REQUEST['isAttending'][$attID] == 'on')
          $isAttending = true;
        else
          $isAttending = false;
        if (isset($_REQUEST['isPlusOne'][$attID]) && $_REQUEST['isPlusOne'][$attID] == 'on')
          $isPlusOne = true;
        else
          $isPlusOne = false;
        if (isset($_REQUEST['isInfant'][$attID]) && $_REQUEST['isInfant'][$attID] == 'on')
          $isInfant = true;
        else
          $isInfant = false;
        if (isset($_REQUEST['isChild'][$attID]) && $_REQUEST['isChild'][$attID] == 'on')
          $isChild = true;
        else
          $isChild = false;
        if (isset($displayName) && $displayName != '')
        {
          $name = $displayName;
        }
        else //displayName is blank
        {
          if ($isPlusOne)
            $name = null;
          else
            $errors['displayName[' . $attID . ']'] = 'Blank Attendee Display Name';
        }
      
        if (!$userEditor->editAttendee($attID, $isAttending, $isPlusOne, $isChild, $isInfant, $name))
          $errors['update_attendee[' . $attID . ']'] = 'Error during update attendee [' . $attID . ']';
      }
      
      //new attendees
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
            $errors['new_displayName[' . $attID . ']'] = 'Blank Attendee Display Name';
        }
      
        if ($currNewAttID = $userEditor->newAttendee($userID, $isPlusOne, $isChild, $isInfant, $name))
        {
          //display success for each attendee:
          echo 'New attendee "' . (isset($name) ? $name : 'Guest') . '" created! Name: (SQL ID: ' . $currNewAttID. ')<br /><br />';
        }
        else
        {
          $errors['create_new_attendee[' . $attID . ']'] = 'Error during new attendee [' . $attID . '] create';
        }
      }
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
      echo '<p class="error">Error[' . $key . '] => ' . $value . '</p>';
    }
    $invalidSubmit = true;
  }
  else
  {
    $invalidSubmit = false;
    echo '<h2>User Successfully Edited</h2>' . "\r\n";
    echo '<p>Return to <a href="guests_view.php' . (isset($_REQUEST['returnAnchor']) ? '#' . $_REQUEST['returnAnchor'] : '') . '">View and Edit Guests</a>?</p>' . "\r\n";
  }
}
?>




<?php
  if (!isset($invalidSubmit))
    $invalidSubmit = false;
  
  $userEditor->setCurrentUser($userID);
?>
  <form action="guests_edit.php" method="post" id="editGuest">
    <input type="hidden" name="returnAnchor" id="returnAnchor" value="<?php echo (isset($_REQUEST['returnAnchor']) ? $_REQUEST['returnAnchor'] : ''); ?>" />
    <table class="formTable" id="editUserTable" name="editUserTable">
    <?php $tabIndex = 1; ?>
    <tr>
      <td><label for="displayUserID">userID: </label></td>
      <td><input type="text" name="displayUserID" id="displayUserID" value="<?php echo (($invalidSubmit) ? (isset($_REQUEST['userID']) ? $_REQUEST['userID'] : '') : $userEditor->getCurrentUserID()); ?>" placeholder="auto_increment" size="10" maxlength="254" tabindex="<?php echo $tabIndex++; ?>" disabled />
      <input type="hidden" name="userID" id="userID" value="<?php echo (($invalidSubmit) ? (isset($_REQUEST['userID']) ? $_REQUEST['userID'] : '') : $userEditor->getCurrentUserID()); ?>" /></td>
    </tr>
    <tr>
      <td><label for="username">Username: </label></td>
      <td><input type="text" name="username" id="username" value="<?php echo (($invalidSubmit) ? (isset($_REQUEST['username']) ? $_REQUEST['username'] : '') : $userEditor->getCurrentUsername()); ?>" placeholder="Login Name" size="25" maxlength="254" required tabindex="<?php echo $tabIndex++; ?>" /></td>
      
      <td><label for="isRSVP">RSVP Submitted?</label></td>
      <td><input type="checkbox" <?php echo (($invalidSubmit) ? ((isset($_REQUEST['isRSVP'])) ? 'checked' : '') : (($userEditor->getCurrentIsRSVP()) ? 'checked' : '')) ?> name="isRSVP" id="isRSVP" tabindex="<?php echo $tabIndex++; ?>" /></td>

    </tr>
    <tr>
      <td><label for="passwordPlaintext">Password: </label></td>
      <td><input type="text" name="passwordPlaintext" id="passwordPlaintext" value="<?php echo (($invalidSubmit) ? (isset($_REQUEST['passwordPlaintext']) ? $_REQUEST['passwordPlaintext'] : '') : $userEditor->getCurrentPasswordPlaintext()); ?>" placeholder="Fun Password" size="25" required maxlength="254" tabindex="<?php echo $tabIndex++; ?>" /></td>
      
      <td><label for="notesRSVP">User RSVP Notes: </label></td>
      <td><textarea name="notesRSVP" id="notesRSVP" cols="20" rows="1" placeholder="User-Submitted Notes" tabindex="<?php echo $tabIndex++; ?>"><?php echo (($invalidSubmit) ? (isset($_REQUEST['notesRSVP']) ? $_REQUEST['notesRSVP'] : '') : $userEditor->getCurrentNotesRSVP()); ?></textarea></td>
    </tr>
    <tr>
      <td><label for="name">Display Name: </label></td>
      <td><input type="text" name="name" id="name" value="<?php echo (($invalidSubmit) ? (isset($_REQUEST['name']) ? $_REQUEST['name'] : '') : $userEditor->getCurrentFullName()); ?>" placeholder="Name Shown to User" size="25" maxlength="254" required tabindex="<?php echo $tabIndex++; ?>" /></td>
      
      <td><label for="gift">Admin Gift Notes: </label></td>
      <td><textarea name="gift" id="gift" cols="20" rows="3" placeholder="Our gift notes" tabindex="<?php echo $tabIndex++; ?>"><?php echo (($invalidSubmit) ? (isset($_REQUEST['gift']) ? $_REQUEST['gift'] : '') : $userEditor->getCurrentGift()); ?></textarea></td>
    </tr>
    <tr>
      <td><label for="email">Email: </label></td>
      <td><input type="text" name="email" id="email" value="<?php echo (($invalidSubmit) ? (isset($_REQUEST['email']) ? $_REQUEST['email'] : '') : $userEditor->getCurrentUserEmail()); ?>" size="25" maxlength="254" tabindex="<?php echo $tabIndex++; ?>" /></td>
      
      <td><label for="isThankYouSent">Thank You Sent?</label></td>
      <td><input type="checkbox" <?php echo (($invalidSubmit) ? ((isset($_REQUEST['isThankYouSent'])) ? 'checked' : '') : (($userEditor->getCurrentIsThankYouSent()) ? 'checked' : '')) ?> name="isThankYouSent" id="isThankYouSent" tabindex="<?php echo $tabIndex++; ?>" /></td>
    </tr>
    <tr>
      <td><label for="address">Mailing Address: </label></td>
      <td><input type="text" name="address" id="address" value="<?php echo (($invalidSubmit) ? (isset($_REQUEST['address']) ? $_REQUEST['address'] : '') : $userEditor->getCurrentAddress()); ?>" placeholder="Address" size="25" maxlength="254" tabindex="<?php echo $tabIndex++; ?>" /></td>
      
      <td><label for="thankYouCardNotes">Notes for Thank You: </label></td>
      <td><textarea name="thankYouCardNotes" id="thankYouCardNotes" cols="20" rows="3" placeholder="Thank you card notes" tabindex="<?php echo $tabIndex++; ?>"><?php echo (($invalidSubmit) ? (isset($_REQUEST['thankYouCardNotes']) ? $_REQUEST['thankYouCardNotes'] : '') : $userEditor->getCurrentThankYouCardNotes()); ?></textarea></td>
      
      
    </tr>
  <tr>
    <td colspan="4"><hr /></td>
  </tr>
<?php
  $arrayAtt = $userEditor->getCurrentAttendeesArray();
  foreach($arrayAtt as $attID => $attendee)
  {
    //$arr[$row['attendeeID']] = array(
    // 'displayName' => $row['displayName'], 
    // 'isPlusOne' => $row['isPlusOne'], 
    // 'isAttending' => $row['isAttending'], 
    // 'isInfant' => $row['isInfant'], 
    // 'isChild' => $row['isChild']);
?>
    <tr>
      <td><label for="attendeeID[<?php echo $attID; ?>]">Attendee ID: </label></td>
      <td><input type="text" name="attendeeID[<?php echo $attID; ?>]" id="attendeeID[<?php echo $attID; ?>]" value="<?php echo $attID; ?>" placeholder="auto_increment" disabled size="10" maxlength="254" tabindex="<?php echo $tabIndex++; ?>" /></td>
    </tr>
    <tr>
      <td><label for="displayName[<?php echo $attID; ?>]">Attendee Name: </label></td>
      <td><input type="text" value="<?php echo (($invalidSubmit) ? (isset($_REQUEST['displayName'][$attID]) ? $_REQUEST['displayName'][$attID] : '') : $attendee['displayName']); ?>" name="displayName[<?php echo $attID; ?>]" id="displayName[<?php echo $attID; ?>]" <?php echo (($invalidSubmit) ? ((isset($_REQUEST['isPlusOne'][$attID])) ? '' : 'required') : (($attendee['isPlusOne']) ? '' : 'required')) ?> placeholder="<?php echo $promptPlaceholderNotPlusOne; ?>" size="25" maxlength="254" tabindex="<?php echo $tabIndex++; ?>" /></td>
    </tr>
    <tr>
      <td>
        <label for="isAttending[<?php echo $attID; ?>]">Is Att?</label>
        <input type="checkbox" <?php echo (($invalidSubmit) ? ((isset($_REQUEST['isAttending'][$attID])) ? 'checked' : '') : (($attendee['isAttending']) ? 'checked' : '')) ?> name="isAttending[<?php echo $attID; ?>]" id="isAttending[<?php echo $attID; ?>]" tabindex="<?php echo $tabIndex++; ?>" />
        <label for="isPlusOne[<?php echo $attID; ?>]"><img src="resources/icons/plusOne.svg" width="15" height="15" id="img_isPlusOne[<?php echo $attID; ?>]" name="img_isPlusOne[<?php echo $attID; ?>]" class="<?php echo (($invalidSubmit) ? ((isset($_REQUEST['isPlusOne'][$attID])) ? 'imgBlack' : 'imgGray') : (($attendee['isPlusOne']) ? 'imgBlack' : 'imgGray')) ?>" /></label>
        <input type="checkbox" <?php echo (($invalidSubmit) ? ((isset($_REQUEST['isPlusOne'][$attID])) ? 'checked' : '') : (($attendee['isPlusOne']) ? 'checked' : '')) ?> name="isPlusOne[<?php echo $attID; ?>]" id="isPlusOne[<?php echo $attID; ?>]" tabindex="<?php echo $tabIndex++; ?>" />
      </td>
      <td>
        <label for="isChild[<?php echo $attID; ?>]"><img src="resources/icons/rsvp_children.png" width="15" height="15" name="img_isChild[<?php echo $attID; ?>]" id="img_isChild[<?php echo $attID; ?>]" class="<?php echo (($invalidSubmit) ? ((isset($_REQUEST['isChild'][$attID])) ? 'imgBlack' : 'imgGray') : (($attendee['isChild']) ? 'imgBlack' : 'imgGray')) ?>" /></label>
        <input type="checkbox" <?php echo (($invalidSubmit) ? ((isset($_REQUEST['isChild'][$attID])) ? 'checked' : '') : (($attendee['isChild']) ? 'checked' : '')) ?> name="isChild[<?php echo $attID; ?>]" id="isChild[<?php echo $attID; ?>]" tabindex="<?php echo $tabIndex++; ?>" />
        <label for="isInfant[<?php echo $attID; ?>]"><img src="resources/icons/rsvp_pacifier.png" width="15" height="15" name="img_isInfant[<?php echo $attID; ?>]" id="img_isInfant[<?php echo $attID; ?>]" class="<?php echo (($invalidSubmit) ? ((isset($_REQUEST['isInfant'][$attID])) ? 'imgBlack' : 'imgGray') : (($attendee['isInfant']) ? 'imgBlack' : 'imgGray')) ?>" /></label>
        <input type="checkbox" <?php echo (($invalidSubmit) ? ((isset($_REQUEST['isInfant'][$attID])) ? 'checked' : '') : (($attendee['isInfant']) ? 'checked' : '')) ?> name="isInfant[<?php echo $attID; ?>]" id="isInfant[<?php echo $attID; ?>]" tabindex="<?php echo $tabIndex++; ?>" />
      </td>
    </tr>
    <tr>
      <td colspan="4"><hr /></td>
    </tr>
<?php
  } //end each existing attendee

  /* new attendee:
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
  */

?>

    <tr>
      <td><input type="button" onClick="addAttRow()" name="addAttendee" id="addAttendee" value="Add Attendee" tabindex="9000" /></td>
      <td></td>
    </tr>
    <tr>
      <td colspan="4"><hr /></td>
    </tr>
    <tr>
      <td colspan="4"><input type="submit" name="submitted" id="submitted" value="Save Changes" tabindex="9042" /></td>
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