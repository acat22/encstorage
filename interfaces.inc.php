<?php
namespace Test4;

interface IPhonesStorageDBConnect 
{
	public function store($email, $phone);
	public function getPhoneByEmail($email);
	public function getSpecifiedError();
	const EMAILEXIST = 'emailexist';
	const EMAILNOTFOUND = 'emailnotfound';
}

interface IPhonesStorageViewDB 
{
	public function viewDB();
	public function dropDB($confirm);
}
