<?php

  require_once("classWeddingUser.inc");
  $userData = new weddingUser();
  
  $body .= '<h2>Guests Report</h2>';
  
  
//Summary of completed RSVPs.<br />
$infants = $userData->getAllAttendingInfants();
$body .= '<h3>' . $userData->getTotalAttending() . ' - Confirmed Guests (' . $infants . ' Infant' . (($infants == 1) ? '' : 's') . ')</h3>';
//children
//infants
$body .= '<p>' . $userData->getAllNotAttending() . ' - Attendees Confirmed Not Coming</p>';
$body .= '<p>' . $userData->getAllNotResponded() . ' - Unresponded Attendees</p>';
$body .= '<p>' . $userData->getMaximumAttendees() . ' - Total Invited Persons</p>';
$body .= '<p> </p>';

$body .= '<h3>Who Has Logged in Today?</h3>';
$body .= '<ul>';
$array = $userData->getRecentlyLoggedInGuests(1);
foreach ($array as $value)
{
  $body .= '<li>' . $value['name'] . ' (' . $value['username'] . ')</li>';
}
$body .= '</ul>';

if (!isset($num_days))
  $num_days = 3;
$body .= '<h3>Who Has Logged in the Last ' . $num_days . ' Days?</h3>';
$body .= '<ul>';
$array = $userData->getRecentlyLoggedInGuests($num_days);
foreach ($array as $value)
{
  $body .= '<li>' . $value['name'] . ' (' . $value['username'] . ')</li>';
}
$body .= '</ul>';

$body .= '<h3>List of Non-RSVP\'d Users</h3>';
$body .= '<ul>';
$array = $userData->getNonRSVPGuests();
foreach ($array as $value)
{
  $body .= '<li>' . $value['name'] . ' (' . $value['username'] . ')</li>';
}
$body .= '</ul>';

$body .= '<h3>List of Guests Confirmed Not Coming</h3>';
$body .= '<ul>';
$array = $userData->getNegativeRespondedGuests();
foreach ($array as $value)
{
  $body .= '<li>' . $value['attendeeName'] . ' (' . $value['userName'] . ')</li>';
}
$body .= '</ul>';


//who has logged in but not RSVPd



//Who has RSVP\'d since the last report.<br />

//Who has logged in since the last report.<br />';








?>