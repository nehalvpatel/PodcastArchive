<?php

class Log
{
    // database
	private $_connection;

    public function __construct($connection)
	{
		$this->_connection = $connection;
	}

    public function Log($action, $reference_id, $new_value = "", $previous_value = "")
    {
        if (isset($_SESSION["username"])) {
            $username = $_SESSION["username"];
        } else {
            $username = "-1";
        }

        $stmt = $this->_connection->prepare("INSERT INTO `log` (`time`, `user`, `action`, `reference_id`, `previous_value`, `new_value`) VALUES (:time, :user, :action, :reference_id, :previous_value, :new_value)");
        $stmt->execute(array(
            ":time" => time(),
            ":user" => $username,
            ":action" => $action,
            ":reference_id" => $reference_id,
            ":previous_value" => $previous_value,
            ":new_value" => $new_value
        ));
    }
}