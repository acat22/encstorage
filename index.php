<?php
/*
 * Copyright AB 2015
 * Test 4
 *
 *
 */
require_once 'interfaces.inc.php';
require_once 'db.class.php';
require_once 'phonestorageentry.class.php';
require_once 'phonestoragetaskcontroller.class.php';

$ct = new Test4\PhoneStorageTaskController;
$ct->process();
?>