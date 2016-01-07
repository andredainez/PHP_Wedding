<?php
  $isSuperUserPage = true;

  require_once("resources/includePath.inc");
  $page_title = "RSVP Data";
  include("header.inc");
?>

<section class="content">
  <?php
    $num_days = 7;
    include('emailGuestReport.inc');
    echo $body;
  ?>
</section>


<?php include("footer.inc"); ?>