<?php
  $isSuperUserPage = true;
  $useJQuery = true;

  require_once("resources/includePath.inc");
  $page_title = "Guests Table";
  include("header.inc");
  
  // Set by header.inc: $currTime = time();  
  $dateWedding = strtotime('2013-10-20 00:01');
?>

<section class="content"> <!-- style="position:absolute; left:210px; top:150px;"> -->
  
<?php
require_once("classWeddingUser.inc");
$userManager = new weddingUser();

$userManager->initializeAllUsers();
?>

<table id="tableUsers">
<thead class="rowHead">
<tr>
  <td colspan="2">Modify</td>
  <td rowspan="2">Username</td>
  <td rowspan="2">Name</td>
  <!--<td>RSVP?</td>-->
  <td rowspan="2">RSVP<br />Notes</td>
  <td rowspan="2">Email</td>
  <td rowspan="2">Address</td>
  <td rowspan="2">Gift Notes</td>
  <td rowspan="2">ThYou Notes</td>
  <td rowspan="2">Thank<br />you?</td>
</tr>
<tr>
  <td colspan="2"><a onclick="expandAll()" id="linkExpandAll" href="javascript:void(0)" class="noUnderline"><img src="resources/icons/magnify_plus.png" width="15px" height="15px" />All</a></td>
  <!--<td><small>LastLogin</small></td>-->
</tr>
</thead>
<tbody>
<?php
$rowCount = 0;

while(!(($currRow = $userManager->getNextUser()) === false))
{
  echo '<tr id="trUser'. $currRow['userID'] . '" class="' . ((++$rowCount % 2 == 0) ? 'rowBlue' : 'rowGreen') . '">' . "\r\n";
  if ($currRow['isAdmin'])
  {
    echo '  <td rowspan="2" colspan="2" style="text-align:center;"><img title="Admin" src="images\icons\shield_admin.png" width="20px" height="20px" class="imgGray" /></td>' . "\r\n";
  }
  else
  {
    echo '  <td rowspan="2"><a title="Edit User" href="guests_edit.php?userID=' . $currRow['userID'] . '&returnAnchor=trUser'. $currRow['userID'] . '" class="noUnderline"><img src="resources/icons/edit_pencil.png" width="20px" height="20px" /></a></td>' . "\r\n";
    echo '  <td rowspan="2"><a title="Delete User" onclick="deleteUser(' . $currRow['userID'] . ')" href="javascript:void(0)" class="noUnderline"><img src="resources/icons/delete_trash.png" width="20px" height="20px" /></a></td>' . "\r\n";
  }
  echo '  <td rowspan="2">' . $currRow['username'] . "</td>\r\n";
  echo '  <td rowspan="2">' . $currRow['name'] . "</td>\r\n";
  //echo '  <td id="tdIsRSVP' . $currRow['userID'] . '" ' . ((!$currRow['isRSVP'] && (isset($currRow['lastLoginTime']) && $currRow['lastLoginTime'] != "")) ? ' class="cellNegative"' : '') . '><small>' . ($currRow['isRSVP'] ? 'RSVP\'d' : 'No') . "</small></td>\r\n";
  echo '  <td rowspan="2">' . ((strlen($currRow['notesRSVP']) < 30) ? $currRow['notesRSVP'] : substr($currRow['notesRSVP'], 0, 30) . '<a title="Edit User (via notes)" href="guests_edit.php?userID=' . $currRow['userID'] . '&returnAnchor=trUser'. $currRow['userID'] . '" class="noUnderline">[...]</a>') . '</td>' . "\r\n";
  echo '  <td rowspan="2">' . $currRow['email'] . "</td>\r\n";
  echo '  <td rowspan="2">' . $currRow['address'] . "</td>\r\n";
  echo '  <td rowspan="2">' . $currRow['gift'] . "</td>\r\n";
  echo '  <td rowspan="2">' . $currRow['thankYouCardNotes'] . "</td>\r\n";
  echo '  <td ' . ((($currTime >= $dateWedding) && !$currRow['isThankYouSent']) ? ' class="cellNegative"' : '') . 'rowspan="2">' . ($currRow['isThankYouSent'] ? 'Yes' : 'No') . "</td>\r\n";
  // echo '  <td>' . $currRow[''] . "</td>\r\n";
  echo "</tr>\r\n";
  echo '<tr id="trUserDuex'. $currRow['userID'] . '" class="' . (($rowCount % 2 == 0) ? 'rowBlue' : 'rowGreen') . '">' . "\r\n";
  //echo '  <td' . ((isset($currRow['lastLoginTime']) && $currRow['lastLoginTime'] != "") ? '' : ' class="cellNegative"') . '><small>' . ((isset($currRow['lastLoginTime']) && $currRow['lastLoginTime'] != "") ? date("M jS", strtotime($currRow['lastLoginTime'])) : 'Never') . "</small></td>\r\n";
  echo "</tr>\r\n";

  if ($userManager->getUserCountAttendee($currRow['userID']) != 0)
  {
    echo '<tr id="trAttCollapsed' . $currRow['userID'] . '" class="' . (($rowCount % 2 == 0) ? 'rowBlue' : 'rowGreen') . '">' . "\r\n";
    echo '  <td colspan="2" style="text-align:center;"><a title="Show Attendees" onclick="expandAttendee(' . $currRow['userID'] . ')" href="javascript:void(0)" class="noUnderline expandIcon"><img src="resources/icons/magnify_plus.png" width="20px" height="20px" /></a></td>' . "\r\n";
    //echo '  <td colspan="2" style="text-align:center;"><a title="Show Attendees" onclick="expandAttendee(' . $currRow['userID'] . ')" href="showAttendees.php?userID=' . $currRow['userID'] . '" class="noUnderline"><img src="resources/icons/magnify_plus.png" width="20px" height="20px" /></a></td>' . "\r\n";
    echo '  <td colspan="8">';
    if ($currRow['isRSVP'])
    {
      echo $userManager->getUserCountConfirmedAttendee($currRow['userID']) . ' Confirmed Guests (' . $userManager->getUserCountAttendee($currRow['userID']) . ' Total Invited)';
    }
    else
    {
      echo $userManager->getUserCountAttendee($currRow['userID']) . ' Total Invited';
    }
    echo "</td>\r\n";
    echo "</tr>\r\n";
  }
}

  // echo '<tr id="trAttCollapsed' . $currRow['userID']. '" class="' . (($rowCount % 2 == 0) ? 'rowBlue' : 'rowGreen') . '">' . "\r\n";
  // echo '  <td></td>' . "\r\n";
  // echo '  <td style="text-align:center;"><a title="Delete Attendee" href="guests_view.php?action=deleteAttendee&id=' . 'ATTENDEE_ID'  . '&returnAnchor=trUser'. $currRow['userID'] . '" class="noUnderline"><img src="resources/icons/delete_trash.png" width="15px" height="15px" /></a></td>' . "\r\n";
  // echo '  <td>displayName</td>' . "\r\n";
  // echo '  <td>Attending?</td>' . "\r\n";
  // echo '  <td><small>Y/N</small></td>' . "\r\n";
  // echo '  <td>+1/C/B</td>' . "\r\n";
  // echo '  <td></td>' . "\r\n";
  // echo '  <td></td>' . "\r\n";
  // echo '  <td><button onClick="insertRow()">Click</button></td>' . "\r\n";
  // echo '  <td id="msg"></td>' . "\r\n";
  // echo '</tr>' . "\r\n";
?>
</tbody>
</table>
</section>

<script type="text/javascript" charset="utf-8">
function expandAll()
{
  $("#linkExpandAll").fadeOut(300);
  $(".expandIcon").click();
}

function expandAttendee(userID)
{
  //event.preventDefault();
  //var rowUser = "#trUser + userID;
  var rowAttendees = "#trAttCollapsed" + userID;
  var rowClass = $(rowAttendees).attr("class");
  var isRSVP = !$("#tdIsRSVP" + userID).hasClass("cellNegative");
  //alert("User has rsvp'd? " + (isRSVP ? "yes" : "no"));
  $.ajax(
  {
    type: "POST",
    url: "js/guests_att_data.php",
    data: {"userID": userID}
  }).done(function(result)
  {
    $(rowAttendees).fadeOut(300, function()
    {
      //var obj = jQuery.parseJSON(result);
      jQuery.each(result, function (index, value)
        {
          var trNew = $("<tr/>", {"class": rowClass, "id": "trAtt" + index});
          $(trNew).addClass("subRow" + userID);
          $(trNew).css({"display": "none"});
          trNew.append("<td></td>");
          var tdDeleteAtt = $("<td/>", {"style": "text-align:center;"});
          var aDeleteAtt = $("<a/>", {"title": "Delete Attendee",
                                      "class": "noUnderline",
                                      "href": "javascript:void(0)",
                                      click: function() {
                                        deleteAttendee(index)
                                      }
                                      });
          var imgDelete = $("<img />", {"src": "resources/icons/delete_trash.png", "width": "15px", "height": "15px"});
          aDeleteAtt.append(imgDelete);
          tdDeleteAtt.append(aDeleteAtt);
          trNew.append(tdDeleteAtt);
          var tdDisplayName = $("<td>" + ((value.displayName != null) ? value.displayName : "<em>Not Given</em>") + "</td>");
          if (value.isPlusOne == "1")
          {
            var imgPlusOne = $("<img />", {"src": "resources/icons/plusOne.svg", "width": "15px", "height": "15px"});
            tdDisplayName.append(imgPlusOne);
          }
          if (value.isChild == "1")
          {
            var imgChild = $("<img />", {"src": "resources/icons/rsvp_children.png", "width": "15px", "height": "15px"});
            tdDisplayName.append(imgChild);
          }
          if (value.isInfant == "1")
          {
            var imgInfant = $("<img />", {"src": "resources/icons/rsvp_pacifier.png", "width": "15px", "height": "15px"});
            tdDisplayName.append(imgInfant);
          }
          trNew.append(tdDisplayName);
          trNew.append("<td style=\"text-align:right;\"><small>Attending:</small></td>");
          trNew.append("<td<?php //" + ((value.isAttending == "1" && isRSVP) ? " class=\"cellNegative\"" : "") + "?> style=\"text-align:center;\"><small>" + (value.isAttending == "1" ? "Yes" : ((isRSVP) ? "---" : "N/A")) + "</small></td>");
          trNew.append("<td colspan=\"5\"></td>");
          $(rowAttendees).before(trNew);
          $(trNew).show(300);
          // alert(index + " : " + value.displayName + " " + value.isPlusOne);
        }
      );
      $(rowAttendees).remove();
    });
    //$("#msg").html( "Result: " + result[1].displayName );
  }
  );
}

function deleteAttendee(attID)
{
  //event.preventDefault();
  //alert("attID: " + attID);
  if(window.confirm(this.title || 'Delete this record?') )
  {
    var rowAttendee = $("#trAtt" + attID);
    var extractedClass = $(rowAttendee).attr("class");
    
    $.ajax(
    {
      type: "POST",
      url: "js/guests_delete_attendee.php",
      data: {"attID": attID}
    }).done(function(result)
    {
      if (result == "true")
      {
        var trDel = $("<tr/>", {"class": extractedClass, "id": "trDeletedUser" + attID});
        trDel.append("<td></td>");
        trDel.append("<td></td>");
        var tdDel = $("<td/>", {"colspan": "8", "text": "DELETED! "});
        var imgCyberman = $("<img />", {"src": "resources/icons/cyberman_color.png", "width": "15px", "height": "15px"});
        tdDel.append(imgCyberman);
        trDel.append(tdDel);
        
        rowAttendee.fadeOut(300, function()
          {
            $(trDel).css({"display": "none"});
            rowAttendee.before(trDel);
            trDel.fadeIn(300);
            
            rowAttendee.remove(); 
          }
        );
      }
      else
      {
        if (window.console)
          console.log("Result from php: " + result);
        alert("Error while deleting!");
      }
    }
    );
  }
}

function deleteUser(userID)
{
  //event.preventDefault();
  //alert("userID: " + userID);
  if(window.confirm(this.title || 'Delete this record?') )
  {
    var rowUser = $("#trUser" + userID);
    var extractedClass = $(rowUser).attr("class");
    
    $.ajax(
    {
      type: "POST",
      url: "js/guests_delete_user.php",
      data: {"userID": userID}
    }).done(function(result)
    {
      if (result == "true")
      {
        var trDel = $("<tr/>", {"class": extractedClass, "id": "trDeletedUser" + userID});
        var tdIcon = $("<td/>", {"colspan": "2", "style": "text-align:center;"});
        var imgCyberman = $("<img />", {"src": "resources/icons/cyberman_color.png", "width": "25px", "height": "25px"});
        tdIcon.append(imgCyberman);
        trDel.append(tdIcon);
        var tdDel = $("<td/>", {"colspan": "8", "text": "DELETED!"});
        trDel.append(tdDel);

        $(".subRow" + userID).fadeOut(300, function() { $(".subRow" + userID).remove(); } );
        $("#trUserDuex" + userID).fadeOut(300, function() { $("#trUserDuex" + userID).remove(); } );
        $("#trAttCollapsed" + userID).fadeOut(300, function() { $("#trAttCollapsed" + userID).remove(); } );
        rowUser.fadeOut(300, function()
          {
            $(trDel).css({"display": "none"});
            rowUser.before(trDel);
            trDel.fadeIn(300);
            
            rowUser.remove(); 
          }
        );
        
      }
      else
      {
        if (window.console)
          console.log("Result from php: " + result);
        alert("Error while deleting!");
      }
    }
    );
  }
}


</script>

<?php include("footer.inc"); ?>