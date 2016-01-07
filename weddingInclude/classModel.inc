<?php
class model
{
  private $dbHostname;
  private $dbName;
  private $dbUser;
  private $dbPassword;
  private $dbPort;
  
  private $dbc; //mysqli connection object
  private $result; //current sql result
  
  function __construct($host, $usr, $pw, $name, $port)
  {
    $this->dbHostname = $host;
    $this->dbName = $name;
    $this->dbUser = $usr;
    $this->dbPassword = $pw;
    $this->dbPort = $port;
    
    $this->dbc = new mysqli($this->dbHostname, $this->dbUser, $this->dbPassword, $this->dbName, $this->dbPort);
  }
  public function isConnected()
  {
    return ($this->dbc->connect_errno) ? false : true;
  }
  public function getConnectionError()
  {
    if ($this->dbc->connect_errno)
      return $this->dbc->connect_error;
    else
      return false;
  }
  
  public function sanitize($input)
  {
    //echo 'debug: before sanitize(): ' . $input . '<br />';
    //echo 'debug: after sanitize(): ' . $this->dbc->real_escape_string($input) . '<br />';
    // http://css-tricks.com/snippets/php/cleaning-variables/
    // if(!get_magic_quotes_gps())
    // {
      // $input = addslashes($input);
    // }
    $input = strip_tags($input);    
    return $this->dbc->real_escape_string($input);
  }
  
  public function runQuery($queryString)
  {
    if ($this->results = $this->dbc->query($queryString))
      return true;
    else
      return false;
  }
  
  public function getResultsRow()
  {
    if ($this->results)
      return $this->results->fetch_row();
    else
      return false;
  }
  public function getResultsAssoc()
  {
    if ($this->results)
      return $this->results->fetch_assoc();
    else
      return false;
  }

  // public function getResultsAllAssoc()
  // {
    // if ($this->results)
    // {
      // // return $this->results->fetch_all(MYSQLI_ASSOC);
      // $array = array();
      // while($row = $this->results->fetch_assoc())
      // {
        // $array[] = $row;
      // }
      // return $array;
    // }
    // else
      // return false;
  // }
  
  public function getNumResult()
  {
    if ($this->results)
      return $this->results->num_rows;
    else
      return false;
  }
  
}

?>