<?php

require_once('classModel.inc');

class usersDatabase
{
  private $connectedModel;
  
  public function __construct(model $databaseConnect)
  {
    if ($databaseConnect->isConnected())
      $this->connectedModel = $databaseConnect;
    // else
      // return NULL; //return NULL from a constructor in PHP doesn't work the way I thought it would
  }
  public function isConnected()
  {
    return ($this->connectedModel->isConnected()) ? true : false;
  }
  
  
  //******************************************************************************************
  //***************************  Administrate Users Functions  *******************************
  //******************************************************************************************
  public function newUser($username, $pw, $fullName, $address, $isSuperUser = false)
  {
    $query = 'INSERT INTO users (username, passwordPlaintext, password, name, address, isAdmin) values (';
    $query .= '"' . $this->connectedModel->sanitize($username) . '", ';
    $query .= '"' . $this->connectedModel->sanitize($pw) . '", ';
    $query .= 'UNHEX(SHA1("' . $this->connectedModel->sanitize($pw) . '")), ';
    $query .= '"' . $this->connectedModel->sanitize($fullName) . '", ';
    $query .= '"' . $this->connectedModel->sanitize($address) . '", ';
    $query .= ($isSuperUser) ? '1' : '0';
    $query .= ')';
    // echo 'classUsersDatabase newUser query: ' . $query . '<br />';
    if ($this->connectedModel->runQuery($query))
    {
      if ($this->connectedModel->runQuery('select LAST_INSERT_ID();'))
        if ($row = $this->connectedModel->getResultsRow())
          return $row[0]; //auto_increment column
    }
    else
    {
      return false;
    }
  }
  public function editUserByID($userID, $username, $pw, $isRSVP, $name, $notesRSVP, $email, $gift, $address, $isThankYouSent, $thankYouCardNotes)
  {
    $query = 'UPDATE users SET ';
    $query .= 'username="' . $this->connectedModel->sanitize($username) . '", ';
    $query .= 'passwordPlaintext="' . ($pw = $this->connectedModel->sanitize($pw)) . '", ';
    $query .= '`password`=UNHEX(SHA1("' . $pw . '")), ';
    $query .= 'isRSVP="' . (($isRSVP) ? '1' : '0') . '", ';
    $query .= '`name`="' . $this->connectedModel->sanitize($name) . '", ';
    $query .= 'notesRSVP="' . $this->connectedModel->sanitize($notesRSVP) . '", ';
    $query .= 'email="' . $this->connectedModel->sanitize($email) . '", ';
    $query .= 'gift="' . $this->connectedModel->sanitize($gift) . '", ';
    $query .= 'thankYouCardNotes="' . $this->connectedModel->sanitize($thankYouCardNotes) . '", ';
    $query .= 'address="' . $this->connectedModel->sanitize($address) . '", ';
    $query .= 'isThankYouSent="' . (($isThankYouSent) ? '1' : '0') . '" ';
    $query .= 'where userID = ' . $this->connectedModel->sanitize($userID) . ';';
    if ($this->connectedModel->runQuery($query))
    {
      return true;
      //row count will be 0 if nothing is changed!!
      // if ($this->connectedModel->runQuery('select ROW_COUNT();'))
      // {
        // // echo ' * count query ran * ';
        // $resultsArray = $this->connectedModel->getResultsRow();
        // // var_dump($resultsArray);
        // if ($resultsArray[0] == "1")
          // return true;
        // else
          // return false;
      // }
    }
    else
    {
      return false;
    }
  }
  public function editAttendeeByID($attID, $isAttending, $isPlusOne, $isChild, $isInfant, $name = null)
  {
    $query = 'UPDATE attendees SET ';
    $query .= 'displayName=' . ((isset($name)) ? ('"' . $this->connectedModel->sanitize($name) . '", ') : 'NULL, ');
    $query .= 'isAttending="' . (($isAttending) ? '1' : '0') . '", ';
    $query .= 'isPlusOne="' . (($isPlusOne) ? '1' : '0') . '", ';
    $query .= 'isChild="' . (($isChild) ? '1' : '0') . '", ';
    $query .= 'isInfant="' . (($isInfant) ? '1' : '0') . '" ';
    $query .= 'where attendeeID = ' . $this->connectedModel->sanitize($attID) . ';';
    //echo '<br /><br />qyery = ' . $query . '<br /><br />';
    if ($this->connectedModel->runQuery($query))
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  
  public function newAttendee($userIDForeignKey, $isPlusOne, $isChild, $isInfant, $name = null)
  {
    $query = 'INSERT INTO attendees (userID, displayName, isPlusOne, isInfant, isChild) values (';
    $query .= '"' . $this->connectedModel->sanitize($userIDForeignKey) . '", ';
    $query .= ((isset($name)) ? ('"' . $this->connectedModel->sanitize($name) . '", ') : 'NULL, ');
    $query .= ($isPlusOne) ? '1, ' : '0, ';
    $query .= ($isInfant) ? '1, ' : '0, ';
    $query .= ($isChild) ? '1' : '0';
    $query .= ')';
    // echo 'classUsersDatabase newAttendee query: ' . $query . '<br />';
    if ($this->connectedModel->runQuery($query))
    {
      if ($this->connectedModel->runQuery('select LAST_INSERT_ID();'))
        if ($row = $this->connectedModel->getResultsRow())
          return $row[0]; //auto_increment column
    }
    else
    {
      return false;
    }

  }
  
  public function setLoginTimestampByID($usrID)
  {
    $query = 'update users set lastLoginTime = NOW() where userID = "';
    $query .= $this->connectedModel->sanitize($usrID) . '"';
    if ($this->connectedModel->runQuery($query))
      return true;
    else
      return false;
  }
  
  public function updateUserRSVPByID($usrID, $isRSVP, $email, $notesRSVP)
  {
    $query = 'update users set isRSVP = "' . $this->connectedModel->sanitize($isRSVP) . '"';
    $query .= ', email = "' . $this->connectedModel->sanitize($email) . '"';
    $query .= ', notesRSVP = "' . $this->connectedModel->sanitize($notesRSVP) . '"';
    $query .= ',  lastRSVPTime  = NOW() ';
    $query .= ' where userID = "' . $this->connectedModel->sanitize($usrID) . '"';
    if ($this->connectedModel->runQuery($query))
      return true;
    else
      return false;
  }
  public function updateAttendeeByID($attID, $isAttending, $displayName = null)
  {
    //echo $isAttending . ' for ID: ' . $attID;
    //echo ' sanitized: ' . $this->connectedModel->sanitize($isAttending) . '<br />';
    $query = 'update attendees set isAttending = "' . $this->connectedModel->sanitize($isAttending) . '"';
    if (isset($displayName))
      $query .= ', displayName = "' . $this->connectedModel->sanitize($displayName) . '"';
    $query .= ' where attendeeID = "' . $this->connectedModel->sanitize($attID) . '"';
    if ($this->connectedModel->runQuery($query))
      return true;
    else
      return false;
  }
  
  //******************************************************************************************
  //*******************************  Authentication Functions  *******************************
  //******************************************************************************************
  public function validateUser($username, $pw)
  {
    $query = 'SELECT userID from users ';
    $query .= 'where UCASE(username) = UCASE("' . $this->connectedModel->sanitize($username) . '")';
    $query .= 'AND password = UNHEX(SHA1("' . $this->connectedModel->sanitize($pw) . '"))';
    if ($this->connectedModel->runQuery($query)) //query
    {
      if ($this->connectedModel->getNumResult() == 1) //alternate should be 0, but would be weird if it was 2+ also?
      {
        if ($row = $this->connectedModel->getResultsRow())
          return $row[0]; //userID
        else
          return false; //$results didn't actually return anything (should be impossible, but this just prevents NULL access
      }
      else //num results was not 1
      {
        return false; //bad user & password
      }
    }
    else //query returned false
    {
      //bad database connection
      //use a TRY/CATCH block?
      //make classModel extend Error, Throwable or w/e the syntax is
      return false;
    }
  }
  
  public function usernameExists($username)
  {
    $query = 'SELECT userID from users ';
    $query .= 'where UCASE(username) = UCASE("' . $this->connectedModel->sanitize($username) . '")';
    if ($this->connectedModel->runQuery($query)) //query
    {
      if ($this->connectedModel->getNumResult() == 1) //alternate should be 0, but would be weird if it was 2+ also?
      {
        if ($row = $this->connectedModel->getResultsRow())
        {
          return $row[0];
        }
        else
        {
          return false;
        }
      }
      else
      {
        return false;
      }
    }
    else
    {
      return false;
    }
  }
  
  public function isSuperUserByID($userID)
  {
    $query = 'select isAdmin from users where userID = "';
    $query .= $this->connectedModel->sanitize($userID) . '"';
    //echo '$query: ' . $query . '<br />';
    if ($this->connectedModel->runQuery($query))
    {
      $resultsArray = $this->connectedModel->getResultsAssoc();
      //echo '$resultsArray["isAdmin"]: ' . $resultsArray['isAdmin'] . '<br />';
      if ($resultsArray['isAdmin'] == 1)
        return true;
      else
        return false;
    }
    else
      return false;
  }
  
  
  //******************************************************************************************
  //*******************************  Get a User's Data Functions  ****************************
  //******************************************************************************************
  public function getUserRowByID($usrID)
  {
    // echo 'running getUsersRowByID';
    $query = 'select userID, username, name, passwordPlaintext, email, isAdmin, isRSVP, address, gift, thankYouCardNotes, isThankYouSent, isBridalShower, notesRSVP, lastLoginTime from users where userID = "';
    $query .= $this->connectedModel->sanitize($usrID) . '"';
    if ($this->connectedModel->runQuery($query))
    {
      // echo '<br />query ran<br />';
      return $this->connectedModel->getResultsAssoc(); //false if empty row
    }
    else
    {
      return false;
    }
  }
  
  public function getAttendeesByID($usrID)
  {
    $query = 'select attendeeID, displayName, isPlusOne, isAttending, isInfant, isChild from attendees where userID = "';
    $query .= $this->connectedModel->sanitize($usrID) . '" order by attendeeID;';
    if ($this->connectedModel->runQuery($query))
    {
      $arr = array();
      while ($row = $this->connectedModel->getResultsAssoc())
      {
        $arr[$row['attendeeID']] = array('displayName' => $row['displayName'], 'isPlusOne' => $row['isPlusOne'], 'isAttending' => $row['isAttending'], 'isInfant' => $row['isInfant'], 'isChild' => $row['isChild']);
      }
      return $arr;
    }
    else
    {
      return false;
    }
  }
  
  public function getAttendeeIsAttendingByID($attID)
  {
    $query = 'select isAttending from attendees where attendeeID = "';
    $query .= $this->connectedModel->sanitize($attID) . '"';
    if ($this->connectedModel->runQuery($query))
    {
      $resultsArray = $this->connectedModel->getResultsAssoc();
      if ($resultsArray['isAttending'] == 1)
        return true;
      else
        return false;
    }
    else
    {
      return false;
    }
  }  
  public function getAttendeeDisplayNameByID($attID)
  {
    $query = 'select displayName from attendees where attendeeID = "';
    $query .= $this->connectedModel->sanitize($attID) . '"';
    if ($this->connectedModel->runQuery($query))
    {
      $resultsArray = $this->connectedModel->getResultsAssoc();
      return $resultsArray['displayName'];
    }
    else
    {
      return false;
    }
  }
  
  public function deleteAttendeeByID($attID)
  {
    // echo ' * attID = ' . $attID . ' * ';
    $query = 'delete from attendees where attendeeID = "';
    $query .= $this->connectedModel->sanitize($attID) . '"';
    // echo ' * q=' . $query . ' * ';
    if ($this->connectedModel->runQuery($query))
    {
      // echo ' * query ran * ';
      if ($this->connectedModel->runQuery('select ROW_COUNT();'))
      {
        // echo ' * count query ran * ';
        $resultsArray = $this->connectedModel->getResultsRow();
        // var_dump($resultsArray);
        if ($resultsArray[0] == "1")
          return true;
        else
          return false;
      }
      else
      {
        return false;
      }
    }
    else
    {
      return false;
    }
  }
  
  public function deleteUserWithAttendeesByID($userID)
  {
    $userID = $this->connectedModel->sanitize($userID);
    // echo ' * userID = ' . $userID . ' * ';
    $query = 'delete from users where userID = "' . $userID . '"';
    // echo ' * q=' . $query . ' * ';
    if ($this->connectedModel->runQuery($query))
    {
      // echo ' * query ran * ';
      if ($this->connectedModel->runQuery('select ROW_COUNT();'))
      {
        // echo ' * count query ran * ';
        $resultsArray = $this->connectedModel->getResultsRow();
        // var_dump($resultsArray);
        if ($resultsArray[0] == "1")
        {
          $query = 'delete from attendees where userID = "' . $userID . '"';
          if ($this->connectedModel->runQuery($query))
          {
            return true;
          }
        }
        else
        {
          return false; //not deleted due to SQL (results = 0 or 2+)
        }
      }
      else
      {
        return false;
      }
    }
    else
    {
      return false;
    }
  }
  
  
  //******************************************************************************************
  //*******************************  Data Reporting Functions  *******************************
  //******************************************************************************************
  
  public function getSumAttendingByID($usrID)
  {
    $query = 'select count(attendeeID) from attendees where userID = "';
    $query .= $this->connectedModel->sanitize($usrID) . '" ';
    $query .= 'AND isAttending = "1"';
    if ($this->connectedModel->runQuery($query)) //query
    {
      if ($row = $this->connectedModel->getResultsRow())
        return $row[0]; //count()
    }
    else
    {
      return -1;
    }
  }

  // returns an array[userID] => array( user data )
  public function getAllUsersDataArray()
  {
    $query = 'select userID, username, name, isAdmin, passwordPlaintext, email, isRSVP, isThankYouSent, address, gift, thankYouCardNotes, notesRSVP, lastLoginTime from users order by userID;';
    if ($this->connectedModel->runQuery($query)) //query
    {
      $arrayAllUsers = array();
      while ($row = $this->connectedModel->getResultsAssoc())
      {
        $arrayAllUsers[$row['userID']] = array(
          'userID' => $row['userID'],
          'username' => $row['username'],
          'name' => $row['name'],
          'isAdmin' => (($row['isAdmin'] == '1') ? true : false),
          'passwordPlaintext' => $row['passwordPlaintext'],
          'email' => $row['email'],
          'isRSVP' => (($row['isRSVP'] == '1') ? true : false),
          'isThankYouSent' => (($row['isThankYouSent'] == '1') ? true : false),
          'address' => $row['address'],
          'gift' => $row['gift'],
          'thankYouCardNotes' => $row['thankYouCardNotes'],
          'notesRSVP' => $row['notesRSVP'],
          'lastLoginTime' => $row['lastLoginTime']
        );
      }
      return $arrayAllUsers;
    }
    else
    {
      return false;
    }
  }
  
  // returns an array[userID] => count of that user's associated attendees
  public function getAllUsersCountAttendees()
  {
    $query = 'select attendees.userID as userID, count(attendeeID) as countAtt from users right join attendees on users.userID = attendees.userID group by attendees.userID;';
    if ($this->connectedModel->runQuery($query)) //query
    {
      $arrayCounts = array(0 => 0); //grantees function won't return false
      while ($row = $this->connectedModel->getResultsAssoc())
      {
        $arrayCounts[$row['userID']] = $row['countAtt'];
      }
      return $arrayCounts;
    }
    else
    {
      return false;
    }
  }  
  // returns an array[userID] => count of that user's associated attendees WHO ARE ATTENDING
  public function getAllUsersCountConfirmedAttendees()
  {
    $query = 'select attendees.userID as userID, count(attendeeID) as countAtt from users right join attendees on users.userID = attendees.userID where attendees.isAttending = 1 group by attendees.userID;';
    // echo ' * query = ' . $query . ' * ';
    if ($this->connectedModel->runQuery($query)) //query
    {
      $arrayCounts = array(0 => 0); //grantees function won't return false
      while ($row = $this->connectedModel->getResultsAssoc())
      {
        $arrayCounts[$row['userID']] = $row['countAtt'];
        // echo '<br />' . $row['userID'] . '-' . $row['countAtt'] . '<br />';
      }
      // echo ' * loop fin * ';
      return $arrayCounts;
    }
    else
    {
      // echo ' * query false * ';
      return false;
    }
  }
  // select count(attendeeID) from attendees group by userID;
  // select count(attendeeID), attendees.userID, users.username from users right join attendees on users.userID = attendees.userID group by attendees.userID;
  
  
  
  
  //******************************************************************************************
  //**************************************  Reports  ************************************************
  //******************************************************************************************
  
  //count who has never logged in:
  //select count(userID) from users where isnull(lastLoginTime);
  //also, display list of who has never logged in
  
  //count RSVP'd attendees
  public function getAllAttending()
  {
    $query = 'select count(attendeeID) from attendees where isAttending = 1;';
    if ($this->connectedModel->runQuery($query)) //query
    {
      if ($row = $this->connectedModel->getResultsRow())
        return $row[0]; //count()
    }
    else
    {
      return -1;
    }
  }
  public function getAllAttendingInfants()
  {
    $query = 'select count(attendeeID) from attendees where isAttending = 1 AND isInfant = 1;';
    if ($this->connectedModel->runQuery($query)) //query
    {
      if ($row = $this->connectedModel->getResultsRow())
        return $row[0]; //count()
    }
    else
    {
      return -1;
    }
  }
  //count not RSVP'd attendees
  public function getAllNotResponded()
  {
    $query = 'select count(attendees.attendeeID) from users right join attendees on users.userID = attendees.userID where users.isRSVP = 0;';
    if ($this->connectedModel->runQuery($query)) //query
    {
      if ($row = $this->connectedModel->getResultsRow())
        return $row[0]; //count()
    }
    else
    {
      return -1;
    }
  }
  //count RSVP'd attendees who are tagged not attending
  public function getAllNotAttending()
  {
    $query = 'select count(attendees.attendeeID) from users right join attendees on users.userID = attendees.userID where users.isRSVP = 1 AND attendees.isAttending = 0;';
    if ($this->connectedModel->runQuery($query)) //query
    {
      if ($row = $this->connectedModel->getResultsRow())
        return $row[0]; //count()
    }
    else
    {
      return -1;
    }
  }
  //count total attendees in database
  public function getMaximumAttendees()
  {
    $query = 'select count(attendeeID) from attendees';
    if ($this->connectedModel->runQuery($query)) //query
    {
      if ($row = $this->connectedModel->getResultsRow())
        return $row[0]; //count()
    }
    else
    {
      return -1;
    }
  }
  
  //return an array of users who have not logged in yet
  public function getNonRSVPGuests()
  {
    $query = 'select name, username from users where isRSVP = 0;';
    if ($this->connectedModel->runQuery($query)) //query
    {
      while ($row = $this->connectedModel->getResultsAssoc())
      {
        $array[] = array('name' => $row['name'], 'username' => $row['username']);
      }
      return $array;
    }
    else
    {
      return -1;
    }  
  }
  //return an array of Attendees who rsvp'd, but tagged they weren't going
  public function getNegativeRespondedGuests()
  {
    $query = 'select attendees.displayName as attendeeName, users.name as userName from users right join attendees on attendees.userID = users.userID where attendees.isAttending = 0 AND users.isRSVP = 1;';
    if ($this->connectedModel->runQuery($query)) //query
    {
      while ($row = $this->connectedModel->getResultsAssoc())
      {
        $array[] = array('attendeeName' => $row['attendeeName'], 'userName' => $row['userName']);
      }
      return $array;
    }
    else
    {
      return -1;
    }  
  }  
  public function getRecentlyLoggedInGuests($num_days)
  {
    $query = 'select name, username from users where lastLoginTime >= ( CURDATE() - INTERVAL ';
    $query .= $this->connectedModel->sanitize($num_days);
    $query .= ' DAY );';
    if ($this->connectedModel->runQuery($query)) //query
    {
      while ($row = $this->connectedModel->getResultsAssoc())
      {
        $array[] = array('name' => $row['name'], 'username' => $row['username']);
      }
      return $array;
    }
    else
    {
      return -1;
    }  
  }

  //(to display data) select users.userID, users.username, attendees.attendeeID, attendees.displayName, users.isRSVP, attendees.isAttending from users right join attendees on users.userID = attendees.userID where users.isRSVP = 0;
  
  //simple join users to their corresponding attendees (don't display users with no associated attendees)
  //select users.userID, users.username, attendees.attendeeID, attendees.displayName from users right join attendees on users.userID = attendees.userID;

  
}
?>