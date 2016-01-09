<ul>
      <li><a href="index.php">Wedding of Awesome</a></li>
      <li><a href="story.php">Our Story</a></li>
      <li><a href="engaged.php">Engagement</a></li>
      <div class="floralDivider"></div>
      <li><a href="http://honeymoonofawesome.bistlineeast.com" target="_none">Our Honeymoon</a></li>
      <?php
      if (!isset($_SESSION['login']))
      {?>
        <li><a href="login.php">Invitation Login</a></li>
        <?php
      }
      if (isset($_SESSION['login']) && $_SESSION['login'] >= 1)
      {
        if ($isBridalShowerUser)
        {
          ?>
          <li><a href="bridal_shower.php">Shower of Awesome</a></li>
          <li><a href="registry.php">Bridal Registry</a></li>
          <?php
        }
        else
        {
          if (!$_SESSION['guest'])
            echo '<li><a href="rsvp.php">RSVP</a></li>';
          ?>
          <li><a href="registry.php">Registry</a></li>
          <li><a href="day_of.php">Wedding Day</a></li>
          <li><a href="accommodations.php">Accommodations</a></li>
          <?php
        }
        if ($_SESSION['login'] >= 2)
        {
          ?>
          <div class="floralDivider mirror"></div>
          <li><a href="guests_view.php">View & Edit Guests</a></li>
          <li><a href="guests_new.php">New Guest</a></li>
          <li><a href="admin_report_rsvp.php">RSVP Reports</a></li>
          <li><a href="admin_report_registry.php">Registry Reports</a></li>
          <div class="floralDivider"></div>
          <?php
        }
        else
        {?>
          <div class="floralDivider mirror"></div><?php
        }?>
        <li><a href="logout.php">Logout</a></li><?php
      }?>
      
    </ul>
