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
        if (empty($previous_value) && empty($new_value)) {
            $stmt = $this->_connection->prepare("INSERT INTO `log` (`time`, `user`, `action`, `reference_id`) VALUES (:time, :user, :action, :reference_id)");
            $stmt->execute(array(
                ":time" => time(),
                ":user" => $_SESSION["username"],
                ":action" => $action,
                ":reference_id" => $reference_id
            ));
        } else if (!empty($previous_value) && !empty($previous_value)) {
            $stmt = $this->_connection->prepare("INSERT INTO `log` (`time`, `user`, `action`, `reference_id`, `previous_value`, `new_value`) VALUES (:time, :user, :action, :reference_id, :previous_value, :new_value)");
            $stmt->execute(array(
                ":time" => time(),
                ":user" => $_SESSION["username"],
                ":action" => $action,
                ":reference_id" => $reference_id,
                ":previous_value" => $previous_value,
                ":new_value" => $new_value
            ));				
        }
    }
}