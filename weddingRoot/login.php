<?php
  session_start();
  require_once("resources/includePath.inc");
  $page_title = "Wedding Guest Login";
  
if (isset($_REQUEST['submitted']))
{
  if (empty($_REQUEST['username']))
  {
    $errors['username'] = 'Please enter your user name.';
  }
  
  if (empty($_REQUEST['password']))
  {
    $errors['password'] = 'Please enter your password';
  }
  
  if (!isset($errors))
  {
    require_once("classWeddingUser.inc");
    
    //Note: model class sanitizes input
    $currentUser = new weddingUser();
    if ($usrID = $currentUser->validateUser($_REQUEST['username'], $_REQUEST['password']))
    {
      $currentUser->setCurrentUser($usrID);
      //set session variables with userID, name, etc
      $_SESSION['userID'] = $currentUser->getCurrentUserID();
      $_SESSION['username'] = $currentUser->getCurrentUsername();
      $_SESSION['name'] = $currentUser->getCurrentFullName();
      if ($currentUser->getCurrentUserIsAdmin())
        $_SESSION['login'] = 2; //elevated access user
      else
        $_SESSION['login'] = 1; //regular user
      
      //"guest" hack:
      if ($_SESSION['username'] == "Guest")
        $_SESSION['guest'] = true;
      else
        $_SESSION['guest'] = false;
      
      if ($currentUser->getCurrentUserIsBridalShower())
      {
        $_SESSION['isBridalShower'] = true; //special sections for exclusive bridal shower guests
        $page_title = "Bridal Shower Login";
      }
      else
      {
        $_SESSION['isBridalShower'] = false; //regular wedding
      }
        
      $currentUser->setCurrentUserLoginTimestamp();
    }
    else //invalid user/pw
    {
      if ($currentUser->usernameExists($_REQUEST['username']))
        $errors['password'] = 'Incorrect password.';
      else
        $errors['username'] = 'Username does not exist.';
    }
  }
}

if (isset($_SESSION['isBridalShower']) && $_SESSION['isBridalShower'])
{
  $page_title = "Bridal Shower Login";
}
  
include("header.inc");
?>

<section class="content">

<?php
if (isset($_SESSION['login']) && isset($_SESSION['name']))
{
  if ($isBridalShowerUser)
  {
    ?>
    <p>
      Welcome, <?php echo $_SESSION['name']; ?>. You have successfully logged in to Allison's bridal shower, hosted by the mother of the bride, Marlene Bistline.
    </p>
    <p>
      Our <a href="registry.php">honeymoon registry</a> is available for bridal shower guests excusively before the celebration. You can also find a few details about the <a href="bridal_shower.php">day of the shower</a>, if you need a refresher (or map!).
    </p>
    <?php
  }
  else
  {
  ?>
    <p>
      You have successfully logged in as a wedding guest, <?php echo $_SESSION['name']; ?>. Hello world!
    </p>
  <?php
  if (!$_SESSION['guest'])
  {
    ?>
      <p>
        Please <a class="fancy" href="rsvp.php">RSVP</a> to let us know if you will be attending, and who will join you in your "adventuring party".
      </p>
    <?php
  }
  ?>
    <p>
      <?php echo ((!$_SESSION['guest']) ? 'After that, w' : 'W' ); ?>e have an online <a href="registry.php">honeymoon registry</a> for you to peruse. We've compiled information about the <a href="day_of.php">day of our celebration</a>, and there is a page to help you find <a href="accommodations.php">accommodations</a> if you wish to stay overnight in the Julian area.
    </p>
    <p>
      Have fun exploring our web site, dear guests!
    </p>
    <?php
  }
}
else //form not submitted yet
{
?>
  <form action="login.php" method="post">
    <p>
      <label for="username">Username from your Invitation</label><br />
      <input type="text" name="username" id="username" required size="15" maxlength="50" <?php echo ((isset($errors['password'])) ? '' : 'autofocus'); ?> tabindex="1" <?php echo ((isset($errors['username'])) ? ' class="error" ' : ''); ?>value="<?php echo ((isset($_REQUEST['username'])) ? $_REQUEST['username'] : ''); ?>" />
      <?php if (isset($errors['username'])) { ?>
        <span class="error"><?php echo $errors['username']; ?></span>
      <?php } ?>
    </p>
    <p>
      <label for="password">Password</label><br />
      <input type="password" name="password" id="password" required size="15" maxlength="25" <?php echo ((isset($errors['password'])) ? 'autofocus' : ''); ?> tabindex="2" <?php echo ((isset($errors['password'])) ? ' class="error" ' : ''); ?> />
      <?php if (isset($errors['password'])) { ?>
        <span class="error"><?php echo $errors['password']; ?></span>
      <?php } ?>
    </p>
    <p>
      <input type="submit" name="submitted" id="submitted" value="Login" tabindex="3" />
    </p>
  </form>
<?php
}
?>
  
</section>
<?php include("footer.inc"); ?>