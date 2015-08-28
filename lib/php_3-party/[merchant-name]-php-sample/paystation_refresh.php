<?
require 'functions/paystation_makesessionid.php';
require 'functions/paystation_post_transaction.php';

$paystationURL	= "https://www.paystation.co.nz/direct/paystation.dll";
$amount		= ($_POST['amount'] * 100);
$pstn_pi	= "[|paystation-id|]"; //Paystation ID
$pstn_gi	= "[|gateway-id|]"; //Gateway ID
$site = 'site'; //site can be used to differentiate transactions from different websites in admin.
$pstn_mr = urlencode('from sample code'); //merchant reference is optional, but is a great way to tie a transaction in with a customer (this is displayed in Paystation Administration when looking at transaction details). Max length is 64 char. Make sure you use it!

$testMode=true;

/*
Generating a unique ID for the transaction (ms)
----------------------------------------------
For this example we generate a random 8 char string using the makePaystationSessionID function. To make it truly unique you would need to store it in a database, and check this against future makePaystationSessionID's to make sure it is not repeated. There are different ways to generate a unique value - here are some other methods
	- Using php's timestamp function - mktime()
	- Using the primary key off a transaction table
the merchantsession (ms) can be up to 64 char long.
*/

$merchantSession	= urlencode($site.'-'.time().'-'.makePaystationSessionID(8,8)); //max length of ms is 64 char 
//$url = $paystation."&am=".$amount."&pi=".$paystationid."&ms=".$merchantsession.'&merchant_ref='.$merchant_ref;
$paystationParams = "paystation&pstn_pi=".$pstn_pi."&pstn_gi=".$pstn_gi."&pstn_ms=".$merchantSession."&pstn_am=".$amount."&pstn_mr=".$pstn_mr."&pstn_nr=t";
if 	($testMode == true){
	$paystationParams = $paystationParams."&pstn_tm=t";
}

//Do Transaction Initiation POST
$initiationResult=directTransaction($paystationURL,$paystationParams);
$p = xml_parser_create();
xml_parse_into_struct($p, $initiationResult, $vals, $tags);
xml_parser_free($p);
for ($j=0; $j < count($vals); $j++) {
	if (!strcmp($vals[$j]["tag"],"DIGITALORDER") && isset($vals[$j]["value"])){
		//get digital order URL
		$digitalOrder=$vals[$j]["value"];
	}
	if (!strcmp($vals[$j]["tag"],"PAYSTATIONTRANSACTIONID") && isset($vals[$j]["value"])){
		//get Paystation Transaction ID for reference
		$paystationTransactionID=$vals[$j]["value"];
	}
}

## Insert last minute data into the database here.  Important Note: MAKE SURE YOU RECORD EVERYTHING YOU KNOW ABOUT THE ORDER AND CUSTOMER PRIOR TO REDIRECTING THE CUSTOMER TO PAYSTATION!! RECORD THIS TO A DATABASE OR FILE - RELYING ON THE SESSION IS NOT ENOUGH!

if ($digitalOrder) {
	header("Location:".$digitalOrder);
	exit();
} else {
	echo "<pre>".htmlentities($initiationResult)."</pre>";
}

?>
