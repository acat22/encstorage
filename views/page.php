<script>
function storeNumber() {
	var params = {};
	params.action = 'addentry';
	params.email = $('#fieldEmail').val();
	params.phone = $('#fieldPhone').val();
	ajaxCall('index.php', params, storeNumberResult);
}

function sendNumber() {
	var params = {};
	params.action = 'getphone';
	params.email = $('#fieldEmail2').val();
	ajaxCall('index.php', params, sendNumberResult);
}

function ajaxCall(url, params, callback) {
	params._privkey = getPrivateKey();
	$.post(url, params, callback);
}

function storeNumberResult(resp) {
	var rep = jQuery.parseJSON(resp);
	if (rep.status == 'ok') {
		alert('Stored successfully'); 
	} else {
		switch (rep.data.error) {
		case 'emailexist':
			alert('Error. This email is already stored in the database');
			break;
		case 'emailincorrect':
			alert('Error: wrong format.\nWrite email as xxxxxxxxx@xxxxx.com. Not less than 4 characters, no more than 100');
			break;
		case 'phoneincorrect':
			alert('Error: wrong format.\nWrite phone as digits or +xxxxxxxx, where x\'s are digits. Not less than 4 characters, no more than 16');
			break;
		case 'paramincorrect':
			alert("Error: wrong format.\nWrite email as xxxxxxxxx@xxxxx.com. Not less than 4 characters, no more than 100.\n\
Write phone as digits or +xxxxxxxx, where x's are digits. Not less than 4 characters, no more than 16");
			break;
		default:
			alert('Error. Unknown error: ' + rep.data.error);
			break;
		}
	}
}

function sendNumberResult(resp) {
	var rep = jQuery.parseJSON(resp);
	if (rep.status == 'ok') {
		alert('We\'ve sent the email to that address. Your phone is: ' + rep.data.phone);
	} else {
		switch (rep.data.error) {
		case 'nomail':
			alert('Your phone is: ' + rep.data.phone + '. An error occured: we couldn\'t send you email.');
			break;
		case 'emailnotfound':
			alert('Error. This email is not stored in the database');
			break;
		case 'emailincorrect':
			alert('Error: wrong format.\nWrite email as xxxxxxxxx@xxxxx.com. Not less than 4 characters, no more than 100');
			break;
		default:
			alert('Error. Unknown error: ' + rep.data.error);
			break;
		}
	}
}
</script>
<div class="cont">

<div class="formBox">

<div class="formTitle">Add your phone number</div>

<div class="formTitle2">Option 1. Add your phone number</div>

<div class="fieldCaption">Enter your PHONE:</div>
<input type="text" maxlength="16" id="fieldPhone" />

<div class="fieldCaption" style="margin-top:20px">Enter your email *:</div>
<input type="text" maxlength="100" id="fieldEmail" />

<div class="afternote">You will be able to retrieve your phone
number later on using your email.</div>

<div class="subBtn" onclick="storeNumber()">Submit</div>

</div>

<div class="formBox">

<div class="formTitle">Retrieve your phone number</div>

<div class="formTitle2">Option 2. Retrieve your phone number</div>

<div class="fieldCaption">Enter your email *:</div>
<input type="text" maxlength="100" id="fieldEmail2" />

<div class="afternote">The phone number will be e-mailed to you.</div>

<div class="subBtn" onclick="sendNumber()">Submit</div>

</div>

</div>