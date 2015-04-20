<?php
/*
 * Some simple and prolly very bad tests, nothing special
 *
 * Copyright AB 2015
 * Test 4
 *
 *
 */
require_once 'interfaces.inc.php';
require_once 'db.class.php';
require_once 'phonestorageentry.class.php';
require_once 'phonestoragetaskcontroller.class.php';

define('DB_NAME', 'test.test4.sqlite');

class TestDB extends \Test4\DB {
	protected $dbname = DB_NAME;
	
	public function testEscapeString($str, $whatshouldbe) {
		return ($this->escapeString($str) == $whatshouldbe);
	}
	
	public function testUnescapeString($str, $whatshouldbe) {
		return ($this->unescapeString($str) == $whatshouldbe);
	}
	
	public function testStore($email, $phone) {
		if (!$this->store($email, $phone)) return false;
		return true;
	}
	
	public function testGetPhoneStored($email, $whatShouldBe) {
		return ($this->getPhoneByEmail($email) == $whatShouldBe);
	}
	
	public function testAlreadyStoredEmail($email, $phone) {
		if ($this->store($email, $phone)) return false;
		if ($this->getSpecifiedError() == self::EMAILEXIST) return true;
		return false;
	}
}

class TestPhoneStorageEntry extends \Test4\PhoneStorageEntry {
	protected $dbClassName = 'TestDB';
	
	public function testSave() {
		return $this->save();
	}
	
	public function testLoadPhoneStored($email, $whatShouldBe) {
		$this->load($email);
		return ($this->phone == $whatShouldBe);
	}
}

class TestPSC extends \Test4\PhoneStorageTaskController {
	protected $itemClassName = 'TestPhoneStorageEntry';
	protected $dbClassName = 'TestDB';
	
	public function testStoreEntry($email, $phone) {
		$st = $this->addEntry($email, $phone);
		return $st->stat;
	}
	
	public function testGetPhoneStored($email, $whatShouldBe) {
		$st = $this->retrievePhone($email);
		if (!$st->stat) return false;
		return ($st->data->phone == $whatShouldBe);
	}
	
	public function testAlreadyStoredEmail($email, $phone) {
		$st = $this->addEntry($email, $phone);
		if ($st->stat) return false;
		$class = $this->itemClassName;
		return ($st->error == $class::EMAIL_EXIST);
	}
	
	public function testStoreWrongEntry($email, $phone) {
		$st = $this->addEntry($email, $phone);
		return !$st->stat;
	}
}

echo '<style>
red {color:red;}
green {color:green;}
td {font-size:14px;}
tr.top td {font-size:16px; padding:20px;}
</style>';
echo '<table><tr class="top"><td width="300">Tests</td><td>&nbsp;</td></tr>';

function testit($result, $testName) {
	if ($result) {
		echo '<tr><td><green>'.$testName.'</green></td><td><green>PASS</green></td></tr>';
	} else {
		echo '<tr><td><red>'.$testName.'</red></td><td><red>FAILED</red></td></tr>';
	}
}

// clear test db
if (file_exists(DB_NAME)) unlink(DB_NAME);

$testDb = new TestDB;
testit(true, 'Connection');

testit($testDb->testEscapeString("dfsdf' LIKE ", "dfsdf\\' LIKE "), 'Escape strings');
testit($testDb->testUnescapeString("\\\\dfsdf\\' LIKE ", "\\dfsdf' LIKE "), 'Unescape strings');

$email = 'tryit@example.com';
testit($testDb->testStore($email, '+662283482'), 'Test correct store');

testit($testDb->testGetPhoneStored($email, '+662283482'), 'Test get stored');

testit($testDb->testAlreadyStoredEmail($email, '+662283482'), 'Test not to write already stored');
$testDb->close();


$email2 = 'tryit2@example.com';
$testE = new TestPhoneStorageEntry;
$testE->email = $email2;
$testE->phone = '2347425824';
testit($testE->testSave(), 'Test save entry');

testit($testE->testLoadPhoneStored($email2, '2347425824'), 'Test get stored');


$email3 = 'tryit3@example.com';
$phone3 = '+847425824';

$testPSC = new TestPSC;
testit($testPSC->testStoreEntry($email3, $phone3), 'Test store correct entry');
testit($testPSC->testGetPhoneStored($email3, $phone3), 'Test get stored');
testit($testPSC->testAlreadyStoredEmail($email3, $phone3), 'Test not to write already stored');
testit($testPSC->testStoreWrongEntry('tryit4@example.com', 'sdhf78238'), 'Test not to store wrong entry');
testit($testPSC->testStoreWrongEntry('dfdfffom', '676674674'), 'Test not to store wrong entry');



echo '</table>';

?>