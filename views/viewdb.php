<div class="cont">
<p>
All emails and phones are encrypted with md5(email) as a key. There is no encryption key on the server.
</p>

<script>
function dropDB() {
	var params = {};
	params.action = 'dropdb';
	ajaxCall('index.php', params, dropDBResult);
}

function ajaxCall(url, params, callback) {
	params._privkey = getPrivateKey();
	$.post(url, params, callback);
}

function dropDBResult(resp) {
	var rep = jQuery.parseJSON(resp);
	if (rep.status == 'ok') {
		location.reload();
	} else {
		switch (rep.data.error) {
		default:
			alert('Error. Unknown error: ' + rep.data.error);
			break;
		}
	}
}

</script>

<p>

<div class="subBtn" onclick="dropDB()">Clear database</div>

</p>
<?php
if ($entries && count($entries)) {
	echo '<table><tr><td>ID</td><td>EMAIL</td><td>PHONE</td></tr>';
	$len = count($entries);
	for ($i = 0; $i < $len; $i++) {
?>
	<tr><td><?=$entries[$i]['id']?></td><td><?=$entries[$i]['email']?></td><td><?=$entries[$i]['phone']?></td></tr>
<?php
	}
	echo '</table>';
} else echo 'No entries';
?>

</div>