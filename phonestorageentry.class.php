<?php
/**
 * Copyright AB 2015
 * Test 4
 * 
 * Item of PhoneStorage
 * 
 * Usage:
 * $e = new \Test4\PhoneStorageEntry;
 * $e->email = 'mail@example.com'; // or can be put as $e = new \Test4\PhoneStorageEntry('mail@example.com', '+1000000000');
 * $e->phone = '+1000000000';
 * if (!$e->save()) {
 *     $error = $e->getError();
 * }
 * 
 * Stores data in DB
 * 
 * 
 * $e = new \Test4\PhoneStorageEntry;
 * if (!$e->load('mail@example.com')) { 
 *     $error = $e->getError();
 * }
 *
 * or 
 *
 * $e = new \Test4\PhoneStorageEntry('mail@example.com');
 * if (!$e->load()) { 
 *     $error = $e->getError();
 * }
 * 
 * Now it will contain phone and email, if it was stored
 * 
 */
namespace Test4;

class PhoneStorageEntry 
{
	
	protected $_email = '';
	protected $_phone = '';
	protected $_err = '';
	protected $dbClassName = '\Test4\DB';
	protected $dbc = null;
	
	const EMAIL_INCORRECT = 'emailincorrect';
	const PHONE_INCORRECT = 'phoneincorrect';
	const PARAM_INCORRECT = 'paramincorrect';
	const EMAIL_EXIST     = 'emailexist';
	const EMAIL_NOTFOUND  = 'emailnotfound';
	
	/**
	 * Get inner db connector
	 * the db class must be any of IPhonesStorageDBConnect
	 * @return IPhonesStorageDBConnect
	 */
	protected function db() 
	{
		 if ($this->dbc === null) {
			$className = $this->dbClassName;
            $db_inst = new $className;
			if (!($db_inst instanceof IPhonesStorageDBConnect)) {
				$db_inst = null;
				throw new \Exception('The given class should implement IPhonesStorageDBConnect');
			}
			$this->dbc = $db_inst;
        }
        return $this->dbc;
	}
	
	public function __construct($email = '', $phone = '') 
	{
		if ($email) $this->email = $email;
		if ($phone) $this->phone = $phone;
	}
	
	/**
	 * can set email or phone directly
	 */
	public function __set($param, $value) 
	{
		switch ($param) {
		case 'email':
			if ($this->validateEmail($value)) $this->_email = $value; else $this->_email = '';
			//else throw new \Exception('Wrong email');
			break;
		case 'phone':
			if ($this->validatePhone($value)) $this->_phone = $value; else $this->_phone = '';
			//else throw new \Exception('Wrong phone');
			break;
		}
	}
	
	/**
	 * get email or phone directly
	 */
	public function __get($param) 
	{
		switch ($param) {
		case 'email':
			return $this->_email;
		case 'phone':
			return $this->_phone;
		}
	}
	
	/**
	 * Email validation
	 * @param string email
	 * @return bool
	 */
	public function validateEmail($str) 
	{
		$len = strlen($str);
		if ($len < 4) return false;
		if ($len > 100) return false;
		return preg_match("/^[\-\._a-z0-9]+@(?:[a-z0-9][\-a-z0-9]*\.)+[a-z]{2,6}$/i", $str);
	}
	
	/**
	 * Phone validation
	 * @param string phone
	 * @return bool
	 */
	public function validatePhone($str) 
	{
		$len = strlen($str);
		if ($len < 4) return false;
		if ($len > 16) return false;
		return preg_match("/^\+?\d{4,16}$/i", $str);
	}
	
	/**
	 * Load entry data from db
	 * 
	 * @param string email
	 * @return bool
	 */
	public function load($email = '') 
	{
		if (!$email) $email = $this->email;
		$this->_err = '';
		if (!$this->validateEmail($email)) {
			$this->_err = self::EMAIL_INCORRECT;
			return false;
		}
		$encEmail = $this->encrypt($email, $email);
		$r = $this->db()->getPhoneByEmail($encEmail);
		if (!$r) {
			$err = $this->db()->getSpecifiedError();
			if ($err == IPhonesStorageDBConnect::EMAILNOTFOUND) {
				$this->_err = self::EMAIL_NOTFOUND;
			} else {
				$this->_err = 'unknown '.$err;
			}
			//throw new \Exception($this->db()->getSpecifiedError());
			return false;
		}
		$this->_email = $email;
		$this->_phone = $this->decrypt($r, $email);
		return true;
	}
	
	/**
	 * Store data in db
	 * 
	 * @return bool
	 */
	public function save() 
	{
		$this->_err = '';
		if (!$this->validateEmail($this->email)
			|| !$this->validatePhone($this->phone))
		{
			$this->_err = self::PARAM_INCORRECT;
			//throw new \Exception('Wrong email or phone');
			return false;
		}
		$encEmail = $this->encrypt($this->email, $this->email);
		$encPhone = $this->encrypt($this->phone, $this->email);
		$r = $this->db()->store($encEmail, $encPhone);
		if (!$r) {
			$err = $this->db()->getSpecifiedError();
			if ($err == IPhonesStorageDBConnect::EMAILEXIST) {
				$this->_err = self::EMAIL_EXIST;
			} else {
				$this->_err = 'unknown '.$err;
			}
			//throw new \Exception($this->db()->getSpecifiedError());
			return false;
		}
		return true;
	}
	
	/**
	 * encrypt data
	 * @param string $str to be encrypted
	 * @param string $key
	 * @return string
	 */
	protected function encrypt($str, $key) 
	{
		$key = md5($key); // for stronger encryption
		for ($i = 0; $i < strlen($str); $i++) {
			$char = substr($str, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) + ord($keychar));
			$result .= $char;
		}
		return urlencode(base64_encode($result));
	}
	
	/**
	 * decrypt data
	 * @param string $str to be decrypted
	 * @param string $key
	 * @return string
	 */
	protected function decrypt($str, $key) 
	{
		$key = md5($key);
		$str = base64_decode(urldecode($str));
		$result = '';
		for ($i = 0; $i < strlen($str); $i++) {
			$char = substr($str, $i, 1);
			$keychar = substr($key, ($i % strlen($key)) - 1, 1);
			$char = chr(ord($char) - ord($keychar));
			$result .= $char;
		}
		return $result;
	}
	
	/**
	 * Get latest error
	 * 
	 * @return string
	 */
	public function getError() 
	{
		return $this->_err;
	}
	
}
