<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<?=$meta?><?php 
if ($title) echo '<title>'.$title.'</title>'; 
?></head>
<script>
<?php 
if ($_privkey) echo 'var _privkey = \''.$_privkey."';\n"; 
?>
if (_privkey) localStorage.teeeeeessst4_privkey_hsdhsj = _privkey;
function getPrivateKey() {
	return localStorage.teeeeeessst4_privkey_hsdhsj;
}
</script>
<style>
body {
	font-family:Arial, sans-serif;
	font-size:12px;
	color:#000;
}

div.cont {
	margin:40px auto;
	width:700px;
}

div.formBox {
	min-height:265px;
	padding:0 15px 20px;
	border:2px solid #E7E7DF;
	border-radius:4px;
	width:250px;
	display:inline-block;
	vertical-align:top;
	margin:5px 10px;
}

div.formBox input {
	display:block;
	border:1px solid #9AB4CC;
	padding:2px 4px;
	font-size:14px;
	font-family:Arial;
	width:240px;
}

div.formTitle {
	/*color:#779CE8;*/
	color:#2C6BEA;
	font-size:12px;
	display:inline-block;
	margin:-12px 0 10px -8px;
	background:#fff;
	padding:4px;
}

div.formTitle2 {
	font-family:Verdana, Arial, sans-serif;
	color:#000;
	font-size:12px;
	font-weight:bold;
	margin:0 5px 20px;
	max-width:200px;
}

div.fieldCaption {
	color:#000;
	font-size:12px;
	margin:5px;
}

div.afternote {
	color:#000;
	font-size:12px;
	margin:10px 5px 10px 30px;
	padding:4px;
}

div.subBtn {
	display:inline-block;
	padding:5px 20px;
	font-size:14px;
	cursor:pointer;
	border-radius:6px;
	border:1px solid #4565A5;
	background:#f2f2f2;
	margin:5px;
	transition: background-color 0.5s ease;
}

div.subBtn:hover {
	background-color:#DDE6F9;
}

.headerBox {
	text-align:center;
	padding:10px;
	border-bottom:1px solid #bbb;
	margin-bottom:20px;
}

.headerBox a {
	display:inline-block;
	padding:10px 20px;
	background:#f2f2f2;
	margin:5px;
	text-decoration:none;
	color:#666;
	transition: background-color 0.5s ease;
}

.headerBox a:hover {
	background-color:#DDE6F9;
}

div.btm {
	margin:60px auto 40px;
	width:500px;
	border-top:1px solid #ddd;
	padding:10px;
}

</style>
<body>

<div class="headerBox">
<a href="index.php">Show page</a>
<a href="index.php?act=viewdb">View in database</a>
<a href="tests.php">Couple of tests</a>
</div>