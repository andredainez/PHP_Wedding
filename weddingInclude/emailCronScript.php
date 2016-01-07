<?php
/*
example to put into your cron table:
runs this script every third day at 4:25 AM (server time)
replace "asterisk" with an actual asterisk
make sure "/usr/bin/php" is actually where php is located on your server
test the cron by making the first bit "* * * * *", which will run the script once a minute, every minute, until stopped

>crontab -e
25 4 "asterisk"/3 * * /usr/bin/php ~/weddingInclude/cronScript.php

*/





require_once("emailDetails.inc");
$subject = 'Scheduled Email';

$num_days = 3;
require('emailGuestReport.inc');

$body .= '<p>Email sent at: (Server Time) ' . date('l, j-M-y G:i:s T') . '</p>';

           
mail($mailto, $subject, $body, $headers);

echo "done";
?>