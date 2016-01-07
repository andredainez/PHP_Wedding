<?php
  session_start();
  $scavengerGameOn = true;
  if (!isset($usePrettyPhoto))
    $usePrettyPhoto = false;
  if (!isset($useJQuery))
    $useJQuery = false;
  
  
  $errorPageURI = 'http://weddingofawesome.bistlineeast.com/login_error.php';
  if ($isUserPage)
  {
    if (!isset($_SESSION['login']) || $_SESSION['login'] < 1)
    {
      header('Location: ' . $errorPageURI);
      exit();
    }
  }
  elseif ($isSuperUserPage)
  {
    if (!isset($_SESSION['login']) || $_SESSION['login'] < 2)
    {
      header('Location: ' . $errorPageURI);
      exit();
    }
  }
  
  //**** Easter Eggs ****
  if (isset($_SESSION['isBridalShower']) && $_SESSION['isBridalShower'])
    $isBridalShowerUser = true;
  else
    $isBridalShowerUser = false;

  if (isset($_REQUEST['easter_egg']))
  {
    if ($_REQUEST['easter_egg'] == 7)
      $showSmileTree = true;
    elseif ($_REQUEST['easter_egg'] == 50)
      $showKiteEatingTree = true;
  }
  else
  {
    $easterEgg = rand(0,99);
    if ($easterEgg == 7)
      $showSmileTree = true;
    elseif ($easterEgg == 50)
      $showKiteEatingTree = true;
  }
  if ($showSmileTree)
  {
    
  }
  if ($showKiteEatingTree)
  {
    
  }
  
/* Detect current Season */
  date_default_timezone_set('America/Los_Angeles');
  $currDay = date('z', time());
  $firstDaySpring = date('z', strtotime('3/21'));
  $firstDaySummer = date('z', strtotime('6/21'));
  $firstDayFall = date('z', strtotime('9/23'));
  $firstDayWinter = date('z', strtotime('12/21'));
  
  if ($currDay < $firstDaySpring)
    $season = 'spring'; //$season = 'winter';
  elseif ($currDay < $firstDaySummer)
    $season = 'spring';
  elseif ($currDay < $firstDayFall)
    $season = 'summer';
  elseif ($currDay < $firstDayWinter)
    $season = 'fall';
  else
    $season = 'fall'; //$season = 'winter';
?>

<!-- Design Copyright 2013 Lindsey Bistline www.lindseyrosedesigns.com -->

<!DOCTYPE html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>
  <meta charset="utf-8" />
  
  <?php require_once("resources/includePath.inc"); ?>
  
  <!-- Set the viewport width to device width for mobile -->
  <meta name="viewport" content="width=device-width" />

  <title>
    <?php echo (isset($page_title) && $page_title != "") ? $page_title : "Wedding of Awesome"; ?>
  </title>
  
  <link href='http://fonts.googleapis.com/css?family=Gentium+Basic:400,700,400italic,700italic|Tangerine:400,700' rel='stylesheet' type='text/css' />

  <link rel="icon" type="image/png" href="resources/page_resources/icon_<? echo $season; ?>.png" />
  <link rel="shortcut icon" href="resources/page_resources/icon_<? echo $season; ?>.ico" />
  
  <!-- <script src="http://code.jquery.com/jquery-latest.min.js"></script> -->

  <link rel="stylesheet" type="text/css" href="css/style.css" />
  
  <!-- IE Fix for HTML5 Tags -->
  <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->
  

  
  
  <?php
  if ($useJQuery || $usePrettyPhoto)
  {
    //*********** DEBUG *************  echo '<script src="js/jquery-1.10.1.js" type="text/javascript" charset="utf-8"></script>';
    echo '<script src="js/jquery-1.10.1.min.js" type="text/javascript" charset="utf-8"></script>';
  }
  
  if ($usePrettyPhoto)
  {
    ?>
    <link rel="stylesheet" href="css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
    <script src="js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
  <?php
  }
  ?>
  
</head>

<body>

  <!--[if lte IE 9]>
    <div id="IEMsg"><h3>This site is not very compatible with Internet Explorer 9 or below. You may not be able to use our online registry. (Google Chrome works well.)</h3></div>
    <style>
      #treeBackground
      {
        display:none;
      }
      h1
      {
        font-size: 300%;
      }
    </style>
  <![endif]-->
  
  <header>
    <div class="names">
      <h1><?php
          if (isset($page_title))
          {
            if ($page_title != "Home") 
            {
              echo (($page_title != "") ? $page_title : "Wedding of Awesome");
            }
            else
              echo "Allison &amp; Andrew";
          }
          else
          {
            echo "Wedding of Awesome";
          }
      ?></h1>
      <hr>
    </div>
  </header>

  <nav>
    <!-- nav_top_image is set as BG to <nav> -->
    <?php require_once("nav.inc"); ?>
    <div id="nav_bottom_image"></div>
  </nav>
  
<div class="mainContent">

  <!--
    <aside class="sideImage">
      <img src="resources/page_resources/tree.png" id="plain_tree" alt="Tree with No Leaves">
    </aside>
    <aside class="sideImage">
      <img src="resources/page_resources/leaves_<? echo $season; ?>.png" id="leaves" alt="<? echo ucwords($season); ?> Leaves">
    </aside>
  -->
  <div id="treeBackground">
    <div id="fullTree">
      <img src="resources/page_resources/tree_<? echo $season; ?>.png"  alt="Tree for <? echo ucwords($season); ?> Season">
    </div>
  </div>

  <div class="bodyContent">

