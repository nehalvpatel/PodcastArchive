<?php

class Admin
{
	// database
	private $_connection;

    // instance
    private $errors = array();

    public function __construct($connection)
	{
		$this->_connection = $connection;
	}

    public function getErrors()
    {
        return $this->errors;
    }
    
    public function validateTimestamp($timestamp)
    {
        $pattern = "/^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$/";
        preg_match($pattern, substr($timestamp, 3), $matches, PREG_OFFSET_CAPTURE);
        
        if (count($matches) == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    public function convertTimestamp($timestamp)
    {
        sscanf($timestamp, "%d:%d:%d", $hours, $minutes, $seconds);
        return isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
    }
    
    public function isLoggedIn()
    {
        if (isset($_SESSION["admin"]) && !empty($_SESSION["admin"])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function doLogin($username, $password)
    {
        if (empty($username) || empty($password)) {
            $this->errors[] = "Please make sure all fields were filled in.";
        } else {
            $query = $this->_connection->prepare("SELECT `username` FROM `admins` WHERE `username` = :username AND `password` = :password");
            $query->execute(
                array(
                    ":username" => $username,
                    ":password" => hash("sha512", $password . "305yh83],>")
                )
            );
            $results = $query->fetchAll();
            if (count($results)>0) {
                return true;
            } else {
                $this->errors[] = "Incorrect username or password.";
            }
            
            return false;
        }
    }
    
    public function addAdminAccount($username, $password)
    {
        if (empty($username) || empty($password)) {
            $this->errors[] = "Please make sure all fields were filled in.";
        } else {
            $query = $this->_connection->prepare("INSERT INTO `admins` (`username`, `password`) VALUES (:username, :password)");
            $result = $query->execute(
                array(
                    ":username" => $username,
                    ":password" => hash("sha512", $password . "305yh83],>")
                )
            );

            if ($result === true) {
                return true;
            } else {
                $this->errors[] = "An error occured with the result of the MySQL query.";
            }
            
            return false;
        }
    }

    public function changeAdminPassword($username, $previouspassword, $newpassword)
    {
        if (empty($username) || empty($previouspassword) || empty($newpassword)) {
            $this->errors[] = "Please make sure all fields were filled in.";
        } else {
            $query = $this->_connection->prepare("UPDATE `admins` SET `password` = :newpassword WHERE `username` = :username AND `password` = :previouspassword");
            $result = $query->execute(
                array(
                    ":username" => $username,
                    ":newpassword" => hash("sha512", $newpassword . "305yh83],>"),
                    ":previouspassword" => hash("sha512", $previouspassword . "305yh83],>")
                )
            );

            if ($result === true) {
                return true;
            } else {
                $this->errors[] = "An error occured with the result of the MySQL query.";
            }
            
            return false;
        }
    }
}