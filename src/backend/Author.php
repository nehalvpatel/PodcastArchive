<?php

class Author
{
	// database
	private $_connection;
	
	// instance
	private $_init_id;
	private $_data;
	
	public function __construct($connection, $initiator)
	{
		$this->_connection = $connection;
		
		if (is_array($initiator)) {
			$this->_init_id = $initiator["ID"];
			$this->_data = $initiator;
		} else {
			$this->_init_id = $initiator;
		}
	}
	
	public function checkData()
	{
		if (count($this->_data) == 0) {
			$this->reloadData($this->_init_id);
		}
	}
	
	public function reloadData($author_id = "")
	{
		if ($author_id === "") {
			$author_id = $this->getID();
		}
		
		if (is_numeric($author_id)) {
			$author_query = $this->_connection->prepare("SELECT * FROM `admins` WHERE `ID` = :ID");
			$author_query->bindValue(":ID", $author_id);
			$author_query->execute();
			$author_results = $author_query->fetchAll();
			
			if (count($author_results) > 0) {
				$this->_data = $author_results[0];
			} else {
				throw new \Exception("No author with that ID exists");
			}
		} else {
			throw new \Exception("Invalid author ID");
		}
	}
	
	private function _getValue($field)
	{
		$this->checkData();
		return $this->_data[$field];
	}
	
	private function _setValue($field, $value)
	{
		$this->checkData();
		try {
			$update_query = $this->_connection->prepare("UPDATE `admins` set `" . $field . "` = :Value WHERE `ID` = :ID");
			$update_query->bindValue(":Value", $value);
			$update_query->bindValue(":ID", $this->getID());
			$update_query->execute();
			
			$this->reloadData();
			
			return true;
		} catch (\PDOException $e) {
			$error_info = array(
				"parameters" => $update_parameters,
				"error" => array(
					"mesage" => $e->getMessage(),
					"trace" => $e->getTrace()
				)
			);
		}
	}
	
	public function getID()
	{
		return $this->_getValue("ID");
	}
	
	public function getType()
	{
		return $this->_getValue("Type");
	}
	
	public function setType($type)
	{
		return $this->_setValue("Type", $type);
	}
	
	public function getUsername()
	{
		return $this->_getValue("Username");
	}
	
	public function getName()
	{
		return $this->_getValue("Name");
	}
	
	public function setName($name)
	{
		return $this->_setValue("Name", $name);
	}
	
	public function getDisplayName()
	{
		if ($this->getName() != "") {
			return $this->getName();
		} else {
			return $this->getUsername();
		}
	}
	
	public function getPraise()
	{
		return $this->_getValue("Praise");
	}
	
	public function setPraise($praise)
	{
		return $this->_setValue("Praise", $praise);
	}
	
	public function getReddit()
	{
		return $this->_getValue("Reddit");
	}
	
	public function setReddit($reddit)
	{
		return $this->_setValue("Reddit", $reddit);
	}
	
	public function getLink()
	{
		return $this->_getValue("Link");
	}
	
	public function setLink($link)
	{
		return $this->_setValue("Link", $link);
	}
	
	public function getDisplayLink()
	{
		if ($this->getLink() != "") {
			return $this->getLink();
		} elseif ($this->getReddit() != "") {
			return "http://www.reddit.com/user/" . $this->getReddit();
		} else {
			return false;
		}
	}
	
	public function __toString()
	{
		return $this->getDisplayName();
	}
}