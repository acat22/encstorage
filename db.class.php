<?php
/**
 * Copyright AB 2015
 * Test 4
 * DB SQLite3 PDO connector and low level operator
 *
 */
namespace Test4;

class DB implements IPhonesStorageDBConnect, IPhonesStorageViewDB 
{
	
	protected $result;
	protected $error;
	protected $errno;
	protected $errOccured;
	protected $dbname = 'test4.sqlite';
	
	/**
	 * Creates an instance and opens a connection (singleton static conection for all instances of the class)
	 * Creates the database if it doesn't exist
	 */
	public function __construct($dbname = '') 
	{
		if ($dbname) $this->dbname = $dbname;
		// open connection
		$this->conn();
		// well, it shouldn't be like that, only for the test
		$this->createDatabase();
	}
	
	// well, it shouldn't be like that, only for the test
	/**
	 * DB creation
	 */
	 public function createDatabase() 
	{
		try {
			$q = "CREATE TABLE IF NOT EXISTS phones (
				id INTEGER PRIMARY KEY AUTOINCREMENT,
				email varchar(128) UNIQUE NOT NULL,
				phone varchar(32) NOT NULL)";
			$this->conn()->exec($q);
		} catch(\PDOException $e) {
			die('Couldn\'t create database. '.$e->getMessage());
		}
	}
	
	/**
	 * get singleton db connection
	 * @return object $connection
	 */
	protected static function connInit($dbname, $kill) 
	{
		static $conn = null;
        if ($conn === null) {
			try {
				$conn = new \PDO('sqlite:'.$dbname);
				$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			} catch(\PDOException $e) {
				die('Couldn\'t connect to SQLite via PDO. '.$e->getMessage());
			}
        } else if ($kill) {
			$conn = null;
		}
        return $conn;
	}
	
	/**
	 * abstraction layer
	 * call singleton db connection
	 */
	protected function conn($kill = false) 
	{
		return self::connInit($this->dbname, $kill);
	}
	
	/**
	 * Execute db query
	 *
	 * @param string $q query
	 * @return bool
	 */
	protected function query($q) 
	{
		try {
			$this->result = $this->conn()->prepare($q);
			$this->result->execute();
			return true;
		} catch(\PDOException $e) {
			$this->errOccured = $e->getCode();
			$this->error = $e->getMessage();
			$this->errno = $e->getCode();
		}
		return false;
	}
	
	/**
	 * @param string
	 * @return string
	 */
	protected function escapeString($str) 
	{
		// we do minimum of escapes, cuz it's important to retrieve clear data
		$str = strval($str);
		$str = str_replace('\\', '\\\\', $str);
		$str = str_replace('\'', '\\\'', $str);
		return $str;
	}
	
	/**
	 * @param string
	 * @return string
	 */
	protected function unescapeString($str) 
	{
		$str = str_replace('\\\'', '\'', $str);
		$str = str_replace('\\\\', '\\', $str);
		return $str;
	}
	
	/**
	 * This function stores our entry in DB
	 * 
	 * !NOTE: we will use our own escape function to unescape it after properly, 
	 * cuz we have encrypted content here and thus can't rely on 3d party escape function.
	 * 
	 * @param string $email
	 * @param string $phone
	 *
	 * @return bool
	 */
	public function store($email, $phone) 
	{
		$params = array(
			'email' => $this->escapeString($email),
			'phone' => $this->escapeString($phone),
		);
		$fields = array();
		$values = array();
		foreach ($params as $field=>$value) {
			$fields[] = $field;
			if (is_string($value)) $value = '\''.$value.'\'';
			$values[] = $value;
		}
		return $this->query('INSERT INTO phones ('.join(',', $fields).') VALUES('.join(',', $values).')');
	}
	
	/**
	 * This function gets the phone by email from DB
	 * 
	 * @param string $phone
	 *
	 * @return string or false
	 */
	public function getPhoneByEmail($email) 
	{
		$this->query("SELECT phone FROM phones WHERE email='".$this->escapeString($email)."'");
		$r = $this->result->fetch(\PDO::FETCH_NUM);
		if ($r) $r = $r[0];
		if ($r) return $this->unescapeString($r);
		$this->errOccured = self::EMAILNOTFOUND;
		return false;
	}
	
	/**
	 * This function gets raw error message
	 * 
	 * @return string
	 */
	public function getError() 
	{
		return $this->error;
	}
	
	/**
	 * This function gets specified by interface constants error message
	 * 
	 * @return string
	 */
	public function getSpecifiedError() 
	{
		if ($this->errOccured) {
			switch ($this->errOccured) {
			case 23000:
				return self::EMAILEXIST;
			case self::EMAILNOTFOUND:
				return self::EMAILNOTFOUND;
			default:
				return 'unknown error '.$this->getError();
			}
		}
		return false;
	}
	
	/**
	 * Drop db
	 */
	public function dropDB($confirm) 
	{
		if ($confirm === 'yes, drop it') {
			//$this->conn()->exec('DROP TABLE phones');
			$this->close();
			unlink($this->dbname);
		}
	}
	
	/**
	 * Get all db entries
	 * @return array
	 */
	public function viewDB() 
	{
		$this->query("SELECT * FROM phones");
		if ($this->errOccured) {
			return false;
		}
		$res = $this->result->fetchAll();
		return $res;
	}
	
	/**
	 * Close db connection
	 */
	public function close() 
	{
		$this->conn(true);
	}
}
