<?php

require_once('classModel.inc');
require_once('classUsersDatabase.inc');
require_once('dbdata.inc');


//constructs a database connection that contains a single potentially valid user for our wedding web site
//the above statement is now FALSE! now i made it possible to also be valid for fetching data of "all" users
//to fit into the idea of a single-use class, this class should ONLY fetch one user
// and, I should make a class that fetches more than one user separately
class weddingUser
{
  private $usersModel;
  private $usersDataConnect;
  private $isConnected;
  
  private $currentUserID;
  private $currentUsername;
  private $currentFullName;
  private $currentPasswordPlaintext;
  private $currentUserEmail;
  private $currentUserIsAdmin;
  private $currentUserIsBridalShower;
  private $currentUserIsRSVP;
  private $currentUserAddress;
  private $currentUserGift;
  private $currentUserThankYouCardNotes;
  private $currentUserIsThankYouSent;
  private $currentUserNotesRSVP;
  private $currentUserLastLogin;
  private $currentUserNumAttending;
  
  private $isAllUsersFetched;
  private $currUserPointer;
  private $maxUsers;
  private $allUsersCountAttendeeArray;
  private $allUsersCountConfirmedAttendeeArray;
  private $allUsersDataArray;
  
  
  public function __construct()
  {
    $this->usersModel = new model(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT); //data is from dbdata.inc
    //echo ($this->usersModel->isConnected()) ? 'T' : 'F';
    //echo $this->usersModel->getConnectionError();
    $this->usersDataConnect = new usersDatabase($this->usersModel);
    $this->isConnected = $this->usersDataConnect->isConnected();
    
    //set current user variables to empty(false) initially
    $this->currentUserID = false;
    $this->currentUsername = false;
    $this->currentFullName = false;
    $this->currentPasswordPlaintext = false;
    $this->currentUserEmail= false;
    $this->currentUserIsAdmin = false;
    $this->currentUserIsBridalShower = false;
    $this->currentUserIsRSVP = false;
    $this->currentUserAddress = false;
    $this->currentUserGift = false;
    $this->currentUserThankYouCardNotes = false;
    $this->currentUserIsThankYouSent = false;
    $this->currentUserNotesRSVP = false;
    $this->currentUserLastLoginTime = false;
    $this->currentUserNumAttending = false;
    
    $this->isAllUsersFetched = false;
    $this->currUserPointer = false;
    $this->maxUsers = false;
    $this->allUsersCountAttendeeArray = false;
    $this->allUsersCountConfirmedAttendeeArray = false;
    $this->allUsersDataArray = false;
  }
  
  public function validateUser($usr, $pw)
  {
    if ($this->isConnected)
      if ($validatedUserID = $this->usersDataConnect->validateUser($usr, $pw))
      {
        // echo 'DEBUG: user validated in classWeddingUsers<br />';
        return $validatedUserID;
      }
      else
      {
        return false;
      }
    else
      return false;
  }
  public function usernameExists($usrname)
  {
    if ($this->isConnected)
      return $this->usersDataConnect->usernameExists($usrname);
    else
      return false;
  }
  
  
  
  public function setCurrentUser($usrId)
  {
    // echo 'DEBUG: current user setCurrentUser called<br />';
    if ($this->isConnected)
    {
      if ($rowAssoc = $this->usersDataConnect->getUserRowByID($usrId))
      {
        // echo 'DEBUG: current user setting<br />';
        $this->currentUserID = $rowAssoc['userID'];
        $this->currentUsername = $rowAssoc['username'];
        $this->currentFullName = $rowAssoc['name'];
        $this->currentPasswordPlaintext = $rowAssoc['passwordPlaintext'];
        $this->currentUserEmail = $rowAssoc['email'];
        //echo '$this->currentUserID: ' . $this->currentUserID . '<br />';
        $this->currentUserIsAdmin = $this->usersDataConnect->isSuperUserByID($this->currentUserID);
        //echo '$this->currentUserIsAdmin: ' . (($this->currentUserIsAdmin) ? 'T' : 'F') . '<br />';
        $this->currentUserIsRSVP = $rowAssoc['isRSVP'];
        $this->currentUserAddress = $rowAssoc['address'];
        $this->currentUserGift = $rowAssoc['gift'];
        $this->currentUserThankYouCardNotes = $rowAssoc['thankYouCardNotes'];
        $this->currentUserIsThankYouSent = $rowAssoc['isThankYouSent'];
        $this->currentUserIsBridalShower = $rowAssoc['isBridalShower'];
        $this->currentUserNotesRSVP = $rowAssoc['notesRSVP'];
        $this->currentUserLastLoginTime = $rowAssoc['lastLoginTime'];
        $this->currentUserNumAttending = $this->usersDataConnect->getSumAttendingByID($this->currentUserID);
        // echo 'DEBUG: current user set in classWeddingUsers<br />';
      }
      else
      {
        // echo 'getUserRowByID() returned false<br />';
      }
    }
    else
    {
      //echo 'isConnected() returned false<br />';
    }
  }
  public function setCurrentUserLoginTimestamp()
  {
    $this->usersDataConnect->setLoginTimestampByID($this->currentUserID);
  }
  public function getCurrentUserID()
  {
    return $this->currentUserID;
  }
  public function getCurrentUsername()
  {
    return $this->currentUsername;
  }
  public function getCurrentFullName()
  {
    return $this->currentFullName;
  }
  public function getCurrentPasswordPlaintext()
  {
    return $this->currentPasswordPlaintext;
  }
  public function getCurrentUserIsAdmin()
  {
    return $this->currentUserIsAdmin;
  }
  public function getCurrentUserIsBridalShower()
  {
    return $this->currentUserIsBridalShower;
  }
  public function getCurrentIsRSVP()
  {
    // echo '$this->currentUserIsRSVP == "' . $this->currentUserIsRSVP . '"<br />';
    // echo (($this->currentUserIsRSVP == "1") ? 'true' : 'false');
    return (($this->currentUserIsRSVP == "1") ? true : false);
  }
  public function getCurrentAddress()
  {
    return $this->currentUserAddress;
  }
  public function getCurrentGift()
  {
    return $this->currentUserGift;
  }
  public function getCurrentThankYouCardNotes()
  {
    return $this->currentUserThankYouCardNotes;
  }
  public function getCurrentIsThankYouSent()
  {
    return (($this->currentUserIsThankYouSent == 1) ? true : false);
  }
  public function getCurrentNotesRSVP()
  {
    return $this->currentUserNotesRSVP;
  }
  public function getCurrentLastLoginTime()
  {
    return $this->currentUserLastLoginTime;
  }
  public function getCurrentUserEmail()
  {
    return $this->currentUserEmail;
  }
  public function getCurrentAttendeesArray()
  {
    return $this->usersDataConnect->getAttendeesByID($this->currentUserID);
    //$arr[$row['attendeeID']] = array('displayName' => $row['displayName'], 'isPlusOne' => $row['isPlusOne'], 'isAttending' => $row['isAttending'], 'isInfant' => $row['isInfant'], 'isChild' => $row['isChild']);
  }
  public function getCurrentAmountAttending()
  {
    $this->currentUserNumAttending = $this->usersDataConnect->getSumAttendingByID($this->currentUserID);
    //return $this->currentUserNumAttending;
    if ($this->currentUserNumAttending == 0)
      return 'no';
    elseif ($this->currentUserNumAttending == 1)
      return 'one';
    else
      return $this->currentUserNumAttending;
  }
  public function getCurrentNumAttending()
  {
    $this->currentUserNumAttending = $this->usersDataConnect->getSumAttendingByID($this->currentUserID);
    return $this->currentUserNumAttending;
    //return 42;
  }
  
  public function updateCurrentRSVP($email, $notes)
  {
    $this->usersDataConnect->updateUserRSVPByID($this->currentUserID, 1, $email, $notes); //always sets isRSVP to TRUE
    $this->currentUserIsRSVP = true;
    $this->currentUserEmail = $email;
    $this->currentUserNotesRSVP = $notes;
  }
  public function updateAttendeeRSVP($attID, $isAtt, $isPlusOne = false, $plusOneName = null)
  {
    if ($isPlusOne)
      if (isset($plusOneName))
        $this->usersDataConnect->updateAttendeeByID($attID, (($isAtt) ? '1' : '0'), $plusOneName);
      else
        $this->usersDataConnect->updateAttendeeByID($attID, (($isAtt) ? '1' : '0'), 'Guest');
    else
      $this->usersDataConnect->updateAttendeeByID($attID, (($isAtt) ? '1' : '0'));
  }
  
  public function getAttendeeIsAttending($attID)
  {
    return $this->usersDataConnect->getAttendeeIsAttendingByID($attID);
  }
  public function getAttendeeDisplayName($attID)
  {
    return $this->usersDataConnect->getAttendeeDisplayNameByID($attID);
  }
  
  
  public function newUser($username, $pw, $fullName, $address)
  {
    if ($this->isConnected)
    {
      if($newUserID = $this->usersDataConnect->newUser($username, $pw, $fullName, $address))
          return $newUserID;
      else
        return false;
    }
  }
  public function newSuperUser()
  {
    //stub. should this be implemented?
    return false;
  }
  
  public function editUser($userID, $username, $password, $isRSVP, $name, $notesRSVP, $email, $gift, $address, $isThankYouSent, $thankYouCardNotes)
  {
    if ($this->isConnected)
    {
      if ($this->usersDataConnect->editUserByID($userID, $username, $password, $isRSVP, $name, $notesRSVP, $email, $gift, $address, $isThankYouSent, $thankYouCardNotes))
        return true;
      else
        return false;
    }
  }
  
  public function newAttendee($userIDForeignKey, $isPlusOne, $isChild, $isInfant, $name = null)
  {
    if ($this->isConnected)
    {
      if($newAttID = $this->usersDataConnect->newAttendee($userIDForeignKey, $isPlusOne, $isChild, $isInfant, $name))
          return $newAttID;
      else
        return false;
    }
  }
  public function editAttendee($attID, $isAttending, $isPlusOne, $isChild, $isInfant, $name = null) //choose not to change foreign key
  {
    //echo ' * called wedd.edit * ';
    if ($this->isConnected)
    {
      if($this->usersDataConnect->editAttendeeByID($attID, $isAttending, $isPlusOne, $isChild, $isInfant, $name))
        return true;
      else
        return false;
    }
  }

  
  
  //************************************************************
  //***************  All Users Table Functions *****************
  //************************************************************
    

  public function getTotalAttending()
  {
    return $this->usersDataConnect->getAllAttending();
  }
  public function getAllAttendingInfants()
  {
    return $this->usersDataConnect->getAllAttendingInfants();
  }
  public function getAllNotResponded()
  {
    return $this->usersDataConnect->getAllNotResponded();
  }
  public function getAllNotAttending()
  {
    return $this->usersDataConnect->getAllNotAttending();
  }
  public function getMaximumAttendees()
  {
    return $this->usersDataConnect->getMaximumAttendees();
  }
  public function getNonRSVPGuests()
  {
    return $this->usersDataConnect->getNonRSVPGuests();
  }
  public function getNegativeRespondedGuests()
  {
    return $this->usersDataConnect->getNegativeRespondedGuests();
  }
  public function getRecentlyLoggedInGuests($num_days)
  {
    return $this->usersDataConnect->getRecentlyLoggedInGuests($num_days);
  }
  


  public function initializeAllUsers()
  {
    // echo ' * starting init * ';
    if($this->allUsersCountAttendeeArray = $this->usersDataConnect->getAllUsersCountAttendees())
    {
      // echo ' * got count * ';
      if ($this->allUsersCountConfirmedAttendeeArray = $this->usersDataConnect->getAllUsersCountConfirmedAttendees())
      {
        // echo ' * got confirmed * ';
        // var_dump($this->allUsersCountAttendeeArray);
        // echo $this->displayTree($this->allUsersCountAttendeeArray);
        if($this->allUsersDataArray = $this->usersDataConnect->getAllUsersDataArray())
        {
          // echo ' * got data array * ';
          // echo $this->displayTree($this->allUsersDataArray);
          $this->currUserPointer = 0;
          $this->maxUsers = count($this->allUsersDataArray);
          $this->isAllUsersFetched = true;
          return true;
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
  
  public function getNextUser()
  {
    if ($this->isAllUsersFetched)
    {
      $val = current($this->allUsersDataArray);
      next($this->allUsersDataArray);
      return $val;
    }
    else
    {
      return false;
    }
  }
  
  public function getUserCountAttendee($usrID)
  {
    if ($this->isAllUsersFetched)
    {
      if(isset($this->allUsersCountAttendeeArray[$usrID]))
      {
        return $this->allUsersCountAttendeeArray[$usrID];
      }
      else
      {
        return 0;
      }
    }
    else
    {
      return false;
    }
  }
  
  public function getUserCountConfirmedAttendee($usrID)
  {
    if ($this->isAllUsersFetched)
    {
      if(isset($this->allUsersCountConfirmedAttendeeArray[$usrID]))
      {
        return $this->allUsersCountConfirmedAttendeeArray[$usrID];
      }
      else
      {
        return 0;
      }
    }
    else
    {
      return false;
    }
  }
  
  public function getConfirmedAttendingByID($usrID)
  {
    if ($this->isConnected)
    {
      return $this->usersDataConnect->getSumAttendingByID($usrID);
    }
    else
    {
      return false;
    }
  }
  
  public function getAttendeesArrayByID($usrID)
  {
    if ($this->isConnected)
    {
      return $this->usersDataConnect->getAttendeesByID($usrID);
    }
    else
    {
      return false;
    }
  }
  
  public function deleteAttendee($attID)
  {
    if ($this->isConnected)
    {
      return $this->usersDataConnect->deleteAttendeeByID($attID);
    }
    else
    {
      return false;
    }
  }
  public function deleteUser($userID)
  {
    if ($this->isConnected)
    {
      // echo ' * is connected * ';
      return $this->usersDataConnect->deleteUserWithAttendeesByID($userID);
    }
    else
    {
      return false;
    }
  }
  
  public function isUserAdminByID($userID)
  {
    return $this->usersDataConnect->isSuperUserByID($userID);
  }
}
?>