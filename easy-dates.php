<?php

Class easyDate extends Datetime
{
	protected $_seconds;
	protected $_minutes;
	protected $_hours;
	protected $_fullYear;
	protected $_year;
	protected $_month;
	protected $_monthName;
	protected $_monthAbbr;
	protected $_day;
	protected $_dayName;
	protected $_dayAbbr;
	protected $_timzone;
	protected $_timestamp;
 
	public function __construct($timezone = null) 
	{
		if($timezone) {
			$this->_timezone = new dateTimeZone($timezone);
			parent::__construct('now', $this->_timezone);
		} else {
			parent::__construct('now');
		}
 
		$this->_seconds   = (int) $this->format('s');
		$this->_minutes   = (int) $this->format('i');
		$this->_hours     = (int) $this->format('H');
		$this->_year      = (int) $this->format('y');
		$this->_fullYear  = (int) $this->format('Y');
		$this->_month     = (int) $this->format('n');
		$this->_monthName = (int) $this->format('F');
		$this->_monthAbbr = (int) $this->format('M');
		$this->_day       = (int) $this->format('j');
		$this->_dayName   = (int) $this->format('l');
		$this->_dayAbbr   = (int) $this->format('D');
		$this->_timestamp = time();
	}
 
	public function day(){ 				return $this->_day;}
	public function dayName(){ 			return $this->_dayName;}
	public function dayAbbr(){ 			return $this->_dayAbbr;}
	public function month(){ 			return $this->_month;}        
	public function monthName(){ 		return $this->_monthName;}
	public function monthAbbr(){ 		return $this->_monthAbbr;}
	public function fullYear(){ 		return $this->_fullYear;}
	public function year(){ 			return $this->_year;}
	public function datetime(){ 		return $this->_month.'/'.$this->_day.'/'.$this->_fullYear.', '.$this->_hours.':'.$this->_minutes.':'.$this->_seconds;}
	public function usaDate(){ 			return $this->_month.'/'.$this->_day.'/'.$this->_fullYear;}
	public function englishDate(){ 		return $this->_day.'/'.$this->_month.'/'.$this->_fullYear;}
	public function time(){	 			return $this->_hours.':'.$this->_minutes.':'.$this->_seconds;}
	public function timestamp(){	 	return $this->_timestamp;}
	public function timeAgoUnix($unix){ return $this->timeAgoMake($unix);}
 
	public function createTimeDate($hours, $minutes, $seconds, $fullYear, $month, $day) 
	{
		$this->createTime($hours, $minutes, $seconds);
		$this->createDate($fullYear, $month, $day);     
	}
 
	public function createTime($hours, $minutes, $seconds = 0) 
	{
		if (!is_numeric($hours) || !is_numeric($minutes) || !is_numeric($seconds)) {
			throw new Exception('createTime() needs either 2 or 3 auguments to create a new time');
		}
 
		$outOfRange = false;
		if($hours < 0 || $hours > 23) {
			$outOfRange = true;
		}
		if($minutes < 0 || $minutes > 59) {
			$outOfRange = true;
		}
		if($seconds < 0 || $seconds > 59) {
			$outOfRange = true;
		}
		if ($outOfRange) {
			throw new Exception('Check the auguments your throwing at me are correct! Cant build a time with those digits!');
		}
 
		$this->_seconds   = (int) $seconds;
		$this->_minutes   = (int) $minutes;
		$this->_hours     = (int) $hours;
		parent::setTime($this->_hours, $this->_minutes, $this->_seconds);
	}
 
	public function createDate($fullYear, $month, $day) 
	{
		if (!is_numeric($fullYear) || !is_numeric($month) || !is_numeric($day)) {
			throw new Exception('createDate() needs 3 auguments to create a new date, all numeric!');
		}
 
		if(!checkdate($month, $day, $fullYear)) {
			throw new Exception('Non-existent date');
		}
 
		$this->_fullYear  = (int) $fullYear;
		$this->_month = (int) $month;
		$this->_day   = (int) $day;
		parent::setDate($this->_fullYear, $this->_month, $this->_day);
	}
 
	public function setDateByType($date, $type="us") 
	{       
		$dateparts = preg_split('{[-/ :.]}', $date);
 
		switch($type) { 
			case "us":
 
			if (!is_array($dateparts) || count($dateparts) != 3) {
				throw new Exception('setDateByType with type=us expects date as MM/DD/YYYY');
			}
 
			$this->createDate($dateparts[2], $dateparts[0], $dateparts[1]);
			break;
 
			case "eu":
 
			if (!is_array($dateparts) || count($dateparts) != 3) {
				throw new Exception('setDateByType with type=us expects date as DD/MM/YYYY');
			}
 
			$this->createDate($dateparts[2], $dateparts[1], $dateparts[0]);
			break;
		}   
	}
 
	public function timeAgo() 
	{       
		$english = mktime($this->_hours, $this->_minutes, $this->_seconds, $this->_month, $this->_day, $this->_fullYear);
		return $this->timeAgoMake($english);
	}
 
	public function timeAgoMake($time, $granularity=1) 
	{ 
		$retval = '';
		$difference = $this->_timestamp - $time;
		$periods = array(
			'decade' => 315360000,
			'Year' => 31536000,
			'month' => 2628000,
			'week' => 604800, 
			'day' => 86400,
			'hour' => 3600,
			'minute' => 60,
			'second' => 1
			);
 
		foreach ($periods as $key => $value) 
		{
			if ($difference >= $value) 
			{
				$time = floor($difference/$value);
				$difference %= $value;
				$retval .= ($retval ? ' ' : '').$time.' ';
				$retval .= (($time > 1) ? $key.'s' : $key);
				$granularity--;
			}
 
			if ($granularity == '0') { break; }
		}
 
		return ' about '.$retval.' ago';      
	}
 
	public function msqlToDateTime($mysqldate) 
	{
		$dateparts = preg_split('{[-/ :.]}', $mysqldate);
 
		if (!is_array($dateparts) || count($dateparts != 3)) { 
			throw new Exception("msqlToDateTime() expects a date as YYYY-MM-DD");
		} else {
			$this->createDate($dateparts[0], $dateparts[1], $dateparts[2]);
		}
	}
}
