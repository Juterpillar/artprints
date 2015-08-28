<html>
<head>
<LINK REL="stylesheet" TYPE="text/css" HREF="paystation.css">
</head>
<script language="Javascript">
function verify(){
  window.open('https://www.paymark.co.nz/dart/darthttp.dll?etsl&tn=verify&merchantid=[|merchant-id|]', 'verify', 'scrollbars=yes, width=400, height=400');
}
//-->
</script>

<body class="client" link="#ff9900" vlink="#ff9900" alink="#FFCC00" BACKGROUND="images/face-circlef.gif">
<table border=0 cellspacing=0 cellpadding=3>
	<tr>
		<td WIDTH="190" VALIGN="top">
  			<IMG SRC="images/blank.gif" WIDTH="1" HEIGHT="15"><br>
			<IMG SRC="images/blank.gif" WIDTH="15" HEIGHT="1"><IMG SRC="images/paystation-logo.jpg" WIDTH="221" HEIGHT="101"><BR>
			<IMG SRC="images/blank.gif" WIDTH="250" HEIGHT="1">
	</TD>
	<TD>
<IMG SRC="images/blank.gif" WIDTH="1" HEIGHT="55"><br>
<font class="head"><b>Paystation Online Payments<br>3-PARTY SAMPLE<br>[|merchant-name|]</b></font>
<br><IMG SRC="images/blank.gif" WIDTH="1" HEIGHT="10"><br>
<table border="0" cellpadding="0" cellspacing="0">
<tr>
	<td><font class="medium">
		<img src="images/blank.gif" width="300" height="1" border=0 alt=""><br>

<?
###########################################################################################
# Update the database (use $ms to tie back to previous session)
###########################################################################################

#=====================================================
#ec response code lookup
#=====================================================
#ec = 0 : No error - transaction succesful
#ec = 1 : Unknown error
#ec = 2 : Bank declined transaction
#ec = 3 : No reply from bank
#ec = 4 : Expired card
#ec = 5 : Insufficient funds
#ec = 6 : Error cummunicating with bank
#ec = 7 : Payment server system error
#ec = 8 : Transaction type not supported
#ec = 9 : Transaction faile
#ec = 10 : Purchase amount less or greater than merchant values
#ec = 11 : Paystation couldnt create order based on inputs
#ec = 12 : Paystation couldnt find merchant based on merchant ID
#ec = 13 : Transaction already in progress
#note: These relate to qsiResponseCode from client
#=====================================================

############################################################################################
# NO PAYMENT INFORMATION FOUND
############################################################################################
if (!isset($_GET['em'])) {
?>
	Sorry, we couldn't find the payment information. Here is the information I could find about your transaction.
	<P>
	<b>Amount (am):</b> <? echo ((isset($_GET['am']))?$_GET['am']:'Not defined'); ?><br>
	<b>Merchant Session (ms):</b> <? echo ((isset($_GET['ms']))?$_GET['ms']:'Not defined'); ?><br>
	<b>Transaction ID (ti):</b> <? echo ((isset($_GET['ti']))?$_GET['ti']:'Not defined'); ?><br>
	<b>Error Code (ec):</b> <? echo ((isset($_GET['ec']))?$_GET['ec']:'Not defined'); ?><br>
<?
}
############################################################################################
# PAYMANT RESPONSE RECIEVED
############################################################################################
elseif ($_GET['ec']=="0") {
	#=============================================================
	#Transaction Approved
	#=============================================================
	$newamount = $_GET['am'] / 100;
	
	?>
		Thank you, your payment of $<? printf("%01.2f",$newamount)?> has been processed successfully.
	<?
	
	//put processing for successful transaction
	
	#=============================================================
}
else {
	#=============================================================
	#Transaction failed
	#=============================================================
	?>
		<B>The following error occurred:</B>
		<P>
		<I><? echo $_GET['em']; ?></I>
		<P>
		Here is the other information I could find about your transaction.
		<P>
		
		<b>Amount (am):</b> <? echo ((isset($_GET['am']))?$_GET['am']:'Not defined'); ?><br>
		<b>Merchant Session (ms):</b> <? echo ((isset($_GET['ms']))?$_GET['ms']:'Not defined'); ?><br>
		<b>Transaction ID (ti):</b> <? echo ((isset($_GET['ti']))?$_GET['ti']:'Not defined'); ?><br>
		<b>Error Code (ec):</b> <? echo ((isset($_GET['ec']))?$_GET['ec']:'Not defined'); ?><br>
	<?
	#=============================================================
}
############################################################################################
?>
		<br>
		<IMG SRC="images/blank.gif" WIDTH="1" HEIGHT="25"><br>
		<A href="javascript:verify()"><IMG SRC="images/paymark-click-to-verify.gif" WIDTH="60" HEIGHT="76" BORDER="0" ALT=""></a><br><IMG SRC="images/blank.gif" WIDTH="1" HEIGHT="10">
	</font></td>
	<td width="100%">&nbsp;</td>
</tr>
</table>

</TD>
</TR>
</TABLE>
</body>
</html>
