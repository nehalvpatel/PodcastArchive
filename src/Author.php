<?php

namespace PainkillerAlready;

class Author
{
	// f3
	private $_f3;

	// database
	private $_connection;
	
	// instance
	private $_init_id;
	private $_data;
	
	public function __construct($initiator, $f3)
	{
		$this->_f3 = $f3;
		$this->_connection = $this->_f3->get("DB");
		
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
			$author_query = "SELECT * FROM `admins` WHERE `ID` = :ID";
			$author_parameters = array(
				":ID" => $author_id
			);
			$author_results = $this->_connection->exec($author_query, $author_parameters, 600);
			
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
			$update_query = "UPDATE `admins` SET {$field} = :Value WHERE `ID` = :ID";
			$update_parameters = array(
				":Value" => $value,
				":ID" => $this->getID()
			);
			$this->_connection->exec($update_query, $update_parameters);
			
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

			$this->_f3->get("log")->addError("Attempt at changing author " . $this->getID() . "'s `" . $field . "` to `" . $value . "`", $error_info);
			$this->_f3->error("Database error.");
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