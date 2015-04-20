<?php
/**
 * Copyright AB 2015
 * Test 4
 *
 *
 */
namespace Test4;

/**
 * Helper class to get status of a function exec
 *
 */
class Stat 
{
	public $stat;
	public $data;
	public $error;
	
	/**
	 * @param bool $stat if the action was successful
	 * @param object $data attached data
	 * @param object $error error if occured
	 */
	public function __construct($stat = true, $data = null, $error = null) 
	{
		$this->stat = $stat;
		$this->data = $data;
		$this->error = $error;
	}
}

class PhoneStorageTaskController 
{
	
	protected $itemClassName = '\Test4\PhoneStorageEntry';
	protected $dbClassName = '\Test4\DB';
	
	/**
	 * Main function.
	 * Process the data, renders views, responds to ajax
	 *
	 */
	public function process() 
	{
		session_start();
		
		$type = '';
		$params = array();
		
		// determine the action
		if ($_POST['action']) {
			switch ($_POST['action']) {
			case 'addentry':
				$type = 'addentry';
				break;
			case 'getphone':
				$type = 'getphone';
				break;
			case 'dropdb':
				$type = 'dropdb';
				break;
			}
		} else {
			if ($_GET['act'] == 'viewdb') $type = 'showdb';
			else if ($_GET['act'] == 'tests') $type = 'tests';
			else $type = 'showpage';
		}
		
		switch ($type) {
		
		default:
		case 'showpage':
			$params['title'] = 'Store entry';
			return $this->render('page', $params);
			break;
			
		case 'showdb':
			$params['title'] = 'DB records';
			$params['entries'] = $this->viewDB();
			return $this->render('viewdb', $params);
			break;
		
		case 'tests':
			$params['title'] = 'Tests';
			return $this->render('tests', $params);
			break;
			
			
		case 'addentry':
			$this->checkPrivateKey($_POST['_privkey']);
			
			$email = $_POST['email']; // no real need to filter it out here, it will be validated anyway
			$phone = $_POST['phone'];
			
			$params = '';
			$st = $this->addEntry($email, $phone);
			if (!$st->stat) {
				$params = array('error'=>$st->error);
			}
			return $this->response($st->stat, $params);
			break;
			
		case 'getphone':
			$this->checkPrivateKey($_POST['_privkey']);
			
			$email = $_POST['email'];
			
			$status = false;
			$params = '';
			$st = $this->retrievePhone($email);
			if ($st->stat) {
				$params = array('phone' => $st->data->phone);
				if ($this->mailData($st->data->email, $st->data->phone)) {
					$status = true;
				} else {
					ob_clean();
					$params['error'] = 'nomail';
				}
			} else {
				$params = array('error'=>$st->error);
			}
			return $this->response($status, $params);
			break;
			
		case 'dropdb':
			$this->checkPrivateKey($_POST['_privkey']);
			
			$st = $this->dropDB();
			if (!$st->stat) {
				$params = array('error'=>$st->error);
			}
			return $this->response($st->stat, $params);
			break;
		}
	}
	
	/**
	 * add a new entry
	 * @return Stat
	 */
	protected function addEntry($email, $phone) 
	{
		$entry = new $this->itemClassName($email, $phone);
		if ($entry->save()) {
			return new Stat(true);
		}
		return new Stat(false, null, $entry->getError());
	}
	
	/**
	 * retrieve the phone number
	 * @return Stat
	 */
	protected function retrievePhone($email) 
	{
		$entry = new $this->itemClassName;
		if ($entry->load($email)) {
			return new Stat(true, $entry);
		}
		return new Stat(false, $email, $entry->getError());
	}
	
	/**
	 * drop db
	 * @return Stat
	 */
	protected function dropDB() 
	{
		$db = new $this->dbClassName;
		if (!($db instanceof IPhonesStorageViewDB)) {
			throw new \Exception('The given class should implement IPhonesStorageViewDB');
		}
		$db->dropDB('yes, drop it');
		return new Stat(true);
	}
	
	/**
	 * get all db entries
	 * @return array entries
	 */
	protected function viewDB() 
	{
		$db = new $this->dbClassName;
		if (!($db instanceof IPhonesStorageViewDB)) {
			throw new \Exception('The given class should implement IPhonesStorageViewDB');
		}
		return $db->viewDB();
	}
	
	/**
	 * Render the output (HTML page) and end the script
	 *
	 * @param string $view
	 * @param array $params
	 */
	protected function render($view, $params) 
	{
		// get all variables here
		extract($params);
		
		// the neccessary step to avoid CSRF attacks
		$_privkey = $this->genPrivateKey();
		
		// draw
		include 'views/top.php';
		include 'views/'.$view.'.php';
		include 'views/bottom.php';
		exit;
	}
	
	/**
	 * Give ajax output and end the script
	 *
	 * @param array $params
	 */
	protected function response($status, $params = '') 
	{
		$response = array('status' => ($status ? 'ok' : 'error'));
		if ($params) $response['data'] = $params;
		exit(json_encode($response));
	}
	
	/**
	 * Generate protected key to avoid CSRF attacks
	 */
	protected function genPrivateKey() 
	{
		$key = uniqid();
		$_SESSION['test4_priv_key_wekn93423l'] = $key;
		return $key;
	}
	
	/**
	 * Check protected key to avoid CSRF attacks
	 * and stop the program if it's wrong
	 * @param string $key
	 */
	protected function checkPrivateKey($key) 
	{
		if ($key !== $_SESSION['test4_priv_key_wekn93423l']) {
			exit('The request key doesn\'t match. You may be a victim of CSRF attack.');
		}
	}
	
	/**
	 * Sends email with the phone number
	 * It is highly recommended to use a professional library for sending emails, such as PHPMailer or similar.
	 * But that goes beyond the test's conditions.
	 * 
	 * @param string $email
	 * @param string $phone
	 *
	 * @return bool
	 */
	protected function mailData($email, $phone) 
	{
		$msg = "Hello, Dear Sir.\nWe send you the phone number you've stored as you requested. Number: ".$phone."\n\nBest Regards";
		if (!mail($email, 'Requested phone number', $msg)) {
			//throw new \Exception('Couldn\'t send email');
			return false;
		}
		return true;
	}
	
}
