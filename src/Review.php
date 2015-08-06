<?php

namespace PainkillerAlready;

class Review
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
	
	public function reloadData($review_id = "")
	{
		if ($review_id === "") {
			$review_id = $this->getID();
		}
		
		if (is_numeric($review_id)) {
			$review_query = "SELECT * FROM `reviews` WHERE `ID` = :ID";
			$review_parameters = array(
				":ID" => $review_id
			);
			$review_results = $this->_connection->exec($review_query, $review_parameters, 600);
			
			if (count($review_results) > 0) {
				$this->_data = $review_results[0];
			} else {
				throw new \Exception("No review with that number exists");
			}
		} else {
			throw new \Exception("Invalid review ID");
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
			$update_query = "UPDATE `reviews` SET {$field} = :Value WHERE `ID` = :ID";
			$update_parameters = array(
				":Value" => $value,
				":ID" => $this->getID()
			);
			$this->_connection->exec($update_query, $update_parameters);
			
			$this->reloadData();
			
			return true;
		} catch (\PDOException $e) {
			die("DATABASE ERROR: " . $e->getMessage());
		}
	}
	
	public function getID()
	{
		return $this->_getValue("ID");
	}
	
	public function getPerson()
	{
		return new Person($this->_getValue("Person"), $this->_f3);
	}
	
	public function getEpisode()
	{
		return new Episode($this->_getValue("Episode"), $this->_f3);
	}
	
	public function getReview()
	{
		return $this->_getValue("Review");
	}
	
	public function __toString()
	{
		return $this->getReview();
	}
}