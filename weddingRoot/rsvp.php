<?php
$isUserPage = true;

require_once("resources/includePath.inc");
$page_title = "RSVP";
include("header.inc");
?>
<script type="text/javascript" charset="utf-8">
  function changeAttStyle(attID)
  {
    var containerDiv = document.getElementById("attendeeBox[" + attID + "]");
    var checkbox = document.getElementById("isAttending[" + attID + "]");
    //alert("isAttending[" + attID + "]" + " is checked? " + checkbox.checked + " className: " + containerDiv.className);
    if (checkbox.checked)
      containerDiv.className = "attendeeBox";
    else
      containerDiv.className = "nonAttendeeBox";
    //alert("isAttending[" + attID + "]" + " is checked? " + checkbox.checked + " className: " + containerDiv.className);
  }
  
  function toggleLock()
  {
    var elem = document.getElementById("lockButton");
    //http://javascript.info/tutorial/searching-elements-dom#methods
    var inputs = document.getElementsByTagName("input");
    if (elem.className == "lockedButton")
    {
      for (var i = 0; i < inputs.length; ++i)
        inputs[i].disabled = false;
      document.getElementById("notesRSVP").disabled = false;
      elem.className = "unlockedButton";
      elem.value = "Unlocked";
      elem.disabled = false;
    }
    else
    {
      for (var i = 0; i < inputs.length; ++i)
        inputs[i].disabled = true;
      document.getElementById("notesRSVP").disabled = true;
      elem.className = "lockedButton";
      elem.value = "Locked";
      elem.disabled = false;
    }
  }
</script>

<section class=content>
<?php

require_once("classWeddingUser.inc");
$currentUser = new weddingUser();
$currentUser->setCurrentUser($_SESSION['userID']);

if (isset($_REQUEST['submitted']))
{
  //echo print_r($_REQUEST);
  $isUpdate = $currentUser->getCurrentIsRSVP();
  $oldTotal = $currentUser->getCurrentNumAttending();
  $oldSumAttending = $currentUser->getTotalAttending();
  if ($isUpdate)
  {
    $emailMsg = '<strong>Changed RSVP Submitted</strong><br /> ' . date(DATE_RFC850) . '<br />--------<pre>';
    $emailMsg .= 'Name: ' . $currentUser->getCurrentFullName() . "\r\n";
    $emailMsg .= 'Username: ' . $currentUser->getCurrentUsername() . "\r\n";
    $emailMsg .= 'UserID: ' . $currentUser->getcurrentUserID() . "\r\n\r\n";
  }
  foreach($_REQUEST['isPlusOne'] as $attID => $isPlusOne)
  {
    if ($isUpdate)
    {
      $emailMsg .= '[' . (($_REQUEST['isAttending'][$attID] == 'on') ? 'x' : ' ') . '] ' . $_REQUEST['displayName'][$attID] . '(ID: ' . $attID . ")";
      $emailMsg .= ' - Previous: [' . (($currentUser->getAttendeeIsAttending($attID)) ? 'x' : ' ') . '] ' . $currentUser->getAttendeeDisplayName($attID) . "\r\n";
    }
    if ($_REQUEST['displayName'][$attID] != '')
      $currentUser->updateAttendeeRSVP($attID, (($_REQUEST['isAttending'][$attID] == 'on') ? true : false), $isPlusOne, $_REQUEST['displayName'][$attID]);
    else
      $currentUser->updateAttendeeRSVP($attID, (($_REQUEST['isAttending'][$attID] == 'on') ? true : false), $isPlusOne);
  }
  $currentUser->updateCurrentRSVP($_REQUEST['email'], $_REQUEST['notesRSVP']);
  if ($isUpdate)
  {
    $emailMsg .= 'TotalAttending: ' . $currentUser->getCurrentNumAttending() . "\r\n";
    $emailMsg .= 'Previous Total: ' . $oldTotal . "\r\n";
    $delta = -($oldTotal - intval($currentUser->getCurrentNumAttending()));
    $emailMsg .= 'Delta:         ' . (($delta >= 0) ? '+' : '') . $delta . "\r\n\r\n";
    $emailMsg .= 'Email: ' . $currentUser->getcurrentUserEmail() . "\r\n";
    $emailMsg .= "\r\n\r\nNotes: \r\n" . $currentUser->getCurrentNotesRSVP();
    $emailMsg .= '</pre>--------<br />';
    $emailMsg .= 'Total Currently RSVP\'d: ' . $currentUser->getTotalAttending() . '<br />';
    $emailMsg .= 'Before this update:     ' . $oldSumAttending;
    require_once("emailDetails.inc");
    //echo 'DEBUG:<br />Mailto: ' . $mailto . '<br />Subject: ' . 'Updated RSVP - ' . $currentUser->getCurrentFullName() . '<br />Headers: ' . $headers . '<br />Email Body:<br />' . $emailMsg . '<br /><br />';
    mail($mailto, 'Updated RSVP - ' . $currentUser->getCurrentFullName(), $emailMsg, $headers);
  }
  
  
  echo '<p>Thanks for the ' . (($isUpdate) ? 'update' : 'response' ) . '! We have ' . $currentUser->getCurrentAmountAttending() . ' confirmed guests in your party. ';
  if ($currentUser->getCurrentNumAttending() == 0)
    echo 'That\'s a <em>little</em> bit sad, but it will be OK.</p>';
  else
    echo 'Huzzah!</p>';
  if ($isUpdate)
    echo '<p>An email has been sent for you.</p>';
}

if (!$currentUser->getCurrentIsRSVP())
{
  echo '<p>Hello, ' . $currentUser->getCurrentFullName() . '! Are you ready to create your adventuring group for our wedding?</p><hr style="width:65%;margin-top:20px;margin-bottom:20px;" /><p>Please check off who can attend:</p>';
}
else
{
  echo '<p>You can unlock your RSVP to edit at any time. We\'ll get an email that you\'ve made changes so we can take note of it, no worries.</p>';
  echo '<p><input type="button" id="lockButton" class="lockedButton" onclick="toggleLock()" value="Locked" /></p>';
}
?>
<form action="rsvp.php" method="post" id="rsvpForm">
<?php
$attendees = $currentUser->getCurrentAttendeesArray();
foreach ($attendees as $attID => $attendee)
{
  //note: placing a single space after each element.
  echo '<div class="' . (($attendee['isAttending']) ? 'a' : 'nonA') . 'ttendeeBox" id="attendeeBox[' . $attID . ']">' . "\n  ";
  
  echo '<input tabindex="1" type="checkbox" name="isAttending[' . $attID . ']" id="isAttending[' . $attID . ']" ' . (($attendee['isAttending']) ? 'checked ' : '') . 'onClick="changeAttStyle(' . $attID . ')" class="isAttending"' . (($currentUser->getCurrentIsRSVP()) ? ' disabled' : '') . ' /> ';
  if ($attendee['isPlusOne'])
  {
    echo '<label for="isAttending[' . $attID . ']">Plus One! (Name):</label> ';
    echo '<input type="text" name="displayName[' . $attID . ']"  id="displayName[' . $attID . ']" value="' . ((isset($attendee['displayName'])) ? $attendee['displayName'] : '') . '" size="30" placeholder="Leave blank to be called \'Guest\'"' . (($currentUser->getCurrentIsRSVP()) ? ' disabled' : '') . ' /> ';
  }
  else
  {
    echo '<label for="isAttending[' . $attID . ']">' . $attendee['displayName'] . '</label> ';
    echo '<input type="hidden" name="displayName[' . $attID . ']" id="displayName[' . $attID . ']" value="' . ((isset($attendee['displayName'])) ? $attendee['displayName'] : '') . '" /> ';
  }
  if ($attendee['isInfant'])
  {
    echo '<img src="resources/icons/rsvp_pacifier.png" /> ';
  }
  if ($attendee['isChild'])
  {
    echo '<img src="resources/icons/rsvp_children.png" /> ';
  }
  echo "\n  " . '<input type="hidden" name="isPlusOne[' . $attID . ']"  id="isPlusOne[' . $attID . ']" value="' . $attendee['isPlusOne'] . '" />';
  echo "\n</div>\n";
}
?>
<hr style="width:65%;margin-top:20px;margin-bottom:20px;" />

<p>
  <label for="email">Your email address (for event updates): </label>
  <input type="email" name="email" id="email" placeholder="Your Email" value="<?php echo $currentUser->getCurrentUserEmail(); ?>"<?php echo (($currentUser->getCurrentIsRSVP()) ? ' disabled' : ''); ?>>
</p>
<p>
  <label for="notesRSVP">Would you like to leave any special notes for us? (Also, please note if the people in your adventuring party have changed.):</label><br />
  <textarea name="notesRSVP" id="notesRSVP" cols="30" rows="5" placeholder="Notes"<?php echo (($currentUser->getCurrentIsRSVP()) ? ' disabled' : ''); ?>><?php echo $currentUser->getCurrentNotesRSVP(); ?></textarea>
</p>

<hr style="width:65%;margin-top:20px;margin-bottom:20px;" />

<p style="text-align:center;">
  <input type="submit" name="submitted" id="submitted" style="font-size:150%" value="<?php echo (($currentUser->getCurrentIsRSVP()) ? 'RSVP & Email' : 'RSVP'); ?>" tabindex="42"<?php echo (($currentUser->getCurrentIsRSVP()) ? ' disabled' : ''); ?> class="<?php echo (($currentUser->getCurrentIsRSVP()) ? 'disabledButton' : 'enabledButton'); ?>" />
</p>

</form>
</section>

<?php include("footer.inc"); ?>