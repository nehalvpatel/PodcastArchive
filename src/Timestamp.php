<?php

namespace PainkillerAlready;

class Timestamp
{
	// f3
	private $_f3;

	// database
	private $_connection;
	
	// instance
	private $_init_id;
	private $_data;
	
	// etc
	private $_end;
	private $_width;
	
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
	
	public function reloadData($timestamp_id = "")
	{
		if ($timestamp_id === "") {
			$timestamp_id = $this->getID();
		}
		
		if (is_numeric($timestamp_id)) {
			$timestamp_query = "SELECT * FROM `timestamps` WHERE `ID` = :ID";
			$timestamp_parameters = array(
				":ID" => $timestamp_id
			);
			$timestamp_results = $this->_connection->exec($timestamp_query, $timestamp_parameters, 600);
			
			if (count($timestamp_results) > 0) {
				$this->_data = $timestamp_results[0];
			} else {
				throw new \Exception("No timestamp with that number exists");
			}
		} else {
			throw new \Exception("Invalid timestamp ID");
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
			$update_query = "UPDATE `timestamps` SET {$field} = :Value WHERE `ID` = :ID";
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

			$this->_f3->get("log")->addError("Attempt at changing timestamp " . $this->getID() . "'s `" . $field . "` to `" . $value . "` [Database error]", $error_info);
			$this->_f3->error("Database error.");
		}
	}
	
	public function getID()
	{
		return $this->_getValue("ID");
	}
	
	public function getEpisode()
	{
		return new Episode($this->_getValue("Episode"), $this->_f3);
	}
	
	public function getSpecial()
	{
		return filter_var($this->_getValue("Special"), FILTER_VALIDATE_BOOLEAN);
	}
	
	public function setSpecial($special)
	{
		return $this->_setValue("Special", (int)$special);
	}
	
	public function getTimestamp()
	{
		return $this->_getValue("Timestamp");
	}
	
	public function setTimestamp($timestamp)
	{
		return $this->_setValue("Timestamp", $timestamp);
	}
	
	public function getTime()
	{
		return Utilities::convertToHMS($this->getTimestamp());
	}
	
	public function getValue()
	{
		return $this->_getValue("Value");
	}
	
	public function setValue($text)
	{
		return $this->_setValue("Value", $text);
	}
	
	public function getURL()
	{
		return $this->_getValue("URL");
	}
	
	public function setURL($url)
	{
		return $this->_setValue("URL", $url);
	}
	
	public function getBegin()
	{
		return $this->getTimestamp();
	}
	
	public function getEnd()
	{
		return $this->_end;
	}
	
	public function setEnd($end)
	{
		$this->_end = $end;
	}
	
	public function getWidth()
	{
		return $this->_width;
	}
	
	public function setWidth($width)
	{
		$this->_width = $width;
	}
	
	public function __toString()
	{
		return $this->getValue();
	}
}