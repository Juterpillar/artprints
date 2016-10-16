<?php

//	This class contains the method to connect to the database as well as all the functions that query the database
//  This class is seperate by view/related functions

include '../config.php';

class Dbase
{
	private $db;

	//	this function establishes a database connection
	public function __construct() {	
		try {
			$this->db = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);	
			if (mysqli_connect_errno()) {
				throw new Exception("Unable to establish database connection");
			}
		}	
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}


	/* ---------------------------------------------------------------------- */
	/* ---------------------------- every page ------------------------------ */
	/* ---------------------------------------------------------------------- */


	// this function gets the info to render the page
	// returns array $pageInfo of all info from row in database
	public function getPageInfo($page) {		
		$qry = "SELECT pageID, pageName, pageTitle, pageHeading, pageDescription, pageContent, pageKeywords FROM pages WHERE pageName = '$page'";
		$rs = $this->db->query($qry);
		if ($rs) {
			if ($rs->num_rows > 0) {
				// convert the page data into an associative array $pageInfo[]		
				$pageInfo = $rs->fetch_assoc(); 	
				return $pageInfo;
			}
		}
		else {
//			echo 'Error executing query '.$qry; // (development only)
		}
	}


	// used if magic quotes is off to add slashes etc so queries etc do not break	
	// no return here as '&' overwrites what's held in $_POST	
	public function sanitiseInput() {
		foreach($_POST as &$post) {
			$post = $this->db->real_escape_string($post);
		}
	}


	/* ---------------------------------------------------------------------- */
	/* ---------- queries related to customers/registering/logging in ------- */
	/* ---------------------------------------------------------------------- */


	// this function adds a new customer to the databse through $_POST on the register form
	// the customer is added with a status of 'inactive' so they cannot login/order etc until 
	// their account is approved by admin
	// returns 'added', 'used', or 'fail' and sends an email to admin and customer if 'added'
	public function addCustomer() {
		if (!get_magic_quotes_gpc()) {
			// if magic_quotes isn't on then sanitise so doesn't break query
			$this->sanitiseInput();
		}
		foreach ($_POST as &$post) {
			$post = trim($post);
		}
		extract($_POST);
		$email = strtolower($email);
		// add a salt as extra security
		$salt = substr(uniqid(), 0, 10);
		$hash = sha1($salt . $password);
		$password = $salt . ':' . $hash;
		$qry = "INSERT INTO customers VALUES (NULL, '$first_name', '$last_name', '$company', '$password', '$delivery_address', '$delivery_suburb', '$delivery_city', '$delivery_postcode', '$island', 'New Zealand', '$billing_address', '$billing_suburb', '$billing_city', '$billing_postcode', 'New Zealand', '$phone', '$email', '50', 'inactive', 'customer', '', 2000)";
		$rs = $this->db->query($qry);	//	execute the query
		if ($rs) {
			// successful
			if ($this->db->affected_rows > 0) {
				$to = $email;
				$subject = 'Artprints Account Request';
				$headers .= 'CC: info@artprints.kiwi.nz'."\n";
				$headers .= 'BCC: niki@afas.co.nz'."\n";
				$headers .= 'From: info@artprints.kiwi.nz'."\n";
				$headers .= 'MIME-Version: 1.0'."\n";
				$headers .= 'Content-Type: text/html; charset=ISO-8859-1'."\n";
				$message = '<html><body>';
				$message .= '<img src="http://www.artprints.kiwi.nz/images/logo_desktop.jpg" alt="Artprints: Publishers and Distributors" />'."\n";
				$message .= '<h4>Your Artprints account request has been received.</h4>'."\n";
				$message .= "<p>Hi $first_name,</p>"."\n";
				$message .= "<p>Thank you for registering for an account for $company.</p>"."\n";
				$message .= '<p>You will receive an email from us once your registration has been approved.  You\'re welcome <a href="http://www.artprints.kiwi.nz/index.php?page=contact">contact us</a> in the interim.  Please do not reply to this email.</p>'."\n";
				$message .= "<p>We look forward to doing business with you.</p>"."\n";
				$message .= "<p>Kind regards,</p>"."\n";
				$message .= "<p>The Team at Artprints</p>"."\n";
				$message .= '<img src="http://www.artprints.kiwi.nz/images/banners/mobile_artprints_team.jpg" />'."\n";
				$message .= "</body></html>";
				// The email worked
				$message = wordwrap($message, 70, "\r\n");
				$message = stripslashes($message);
				if (mail($to, $subject, $message, $headers)) {
				} 
				$msg = 'added';  
			}
			// No record created - this is an unlikely scenario
			else {
				$msg = 'fail';  
			}
		}
		// email already exists on the database
		else {
			$msg = 'used';  
		}
		return $msg; 
	}


	// this function checks there is a customer that matches the supplied name and password from the login form
	// returns array $cust with all info if successful or $result = 'incorrect' if wrong
	public function getCustomer($email, $password)
	{
		if (!get_magic_quotes_gpc()) {
			$password = $this->db->real_escape_string($password);
		}
		// encrypt the password from the form so it matches how it's saved on the table
		$email = strtolower($email);
		$email = trim($email);
		$qry = "SELECT custID, custFirstName, custLastName, custCompany, custPassword, custDelAddress, custDelSuburb, custDelCity,  custDelPostcode, custDelIsland, custDelCountry, custBillAddress, custBillSuburb, custBillCity,  custBillPostcode, custBillCountry, custPhone, custEmail, custDeposit, custNotes, custStatus, custAccess, custCreditLimit FROM customers WHERE custEmail = '$email'";
		$rs = $this->db->query($qry);
		if ($rs) {
			if ($rs->num_rows > 0) {
				//	converts the user record into an associative array $user		
				$cust = $rs->fetch_assoc(); 	
				
				// split the password into hash and salt to check against submitted password
				$splitP = explode(':', $cust['custPassword']);
				$salt = $splitP[0];
				$hash = $splitP[1];
				$password = sha1($salt.$password);
				// get the userID to save onto session to use for uploads etc if they match
				if ($password == $hash) {
					return $cust;
				}
				// passwords don't match so return 'incorrect'
				else {
					$result = 'incorrect';
					return $result;
				}
			}
			// didn't retrieve a row from the database as no match for that email
			else {
				$result = 'incorrect';
				return $result;
			}
		}
		// something went wrong with the query (development only)
		else {
//			echo 'Error executing query '.$qry; 
		}
	}


	// updates the customers information from the myAccount form
	// this is used by a logged in customer for themselves or admin for any customer
	// updates $_SESSION customer details if customer
	// sends email to customer and admin if customer password is updated
	// returns $result = 'nochange' if no rows affected
	public function updateCustomer() {
		if (!get_magic_quotes_gpc()) {
			// if magic_quotes isn't on then sanitise so doesn't break query
			$this->sanitiseInput();
		}
		extract($_POST);
		if ($_SESSION['access'] == 'customer') {
			$qry = "UPDATE customers SET custFirstName = '$first_name', custLastName = '$last_name', custCompany = '$company_name', custDelAddress = '$delivery_address', custDelSuburb = '$delivery_suburb', custDelCity = '$delivery_city', custDelPostcode = $delivery_postcode, custDelIsland = '$island', custBillAddress = '$billing_address', custBillSuburb = '$billing_suburb', custBillCity = '$billing_city', custBillPostcode = $billing_postcode, custPhone = '$phone', custEmail = '$email', custNotes = '$notes' WHERE custID = $custID";
		}
		else if ($reset != ''){
			$email = strtolower($email);
			// add a salt as extra security
			$salt = substr(uniqid(), 0, 10);
			$hash = sha1($salt . $reset);
			$password = $salt . ':' . $hash;
			$qry = "UPDATE customers SET custFirstName = '$first_name', custLastName = '$last_name', custCompany = '$company_name', custPassword = '$password', custDelAddress = '$delivery_address', custDelSuburb = '$delivery_suburb', custDelCity = '$delivery_city',  custDelPostcode = '$delivery_postcode', custDelIsland ='$island', custBillAddress = '$billing_address', custBillSuburb ='$billing_suburb', custBillCity ='$billing_city',  custBillPostcode ='$billing_postcode', custPhone ='$phone', custEmail = '$email', custNotes = '$notes', custDeposit = $deposit, custStatus = '$status', custCreditLimit = $credit WHERE custID = $custID";
		}
		else {
			$qry = "UPDATE customers SET custFirstName = '$first_name', custLastName = '$last_name', custCompany = '$company_name', custDelAddress = '$delivery_address', custDelSuburb = '$delivery_suburb', custDelCity = '$delivery_city',  custDelPostcode = '$delivery_postcode', custDelIsland ='$island', custBillAddress = '$billing_address', custBillSuburb ='$billing_suburb', custBillCity ='$billing_city',  custBillPostcode ='$billing_postcode', custPhone ='$phone', custEmail = '$email', custNotes = '$notes', custDeposit = $deposit, custStatus = '$status', custCreditLimit = $credit WHERE custID = $custID";
		}
		$rs = $this->db->query($qry);
			if ($rs) {
				// if successful, update session details.
				if ($this->db->affected_rows > 0) {
					$result = 'success'; 
					if ($_SESSION['access'] == 'customer') {
						$_SESSION['first'] = $first_name;
						$_SESSION['last'] = $last_name;
						$_SESSION['name'] = $first_name.' '.$last_name;
						$_SESSION['company'] = $company_name;
						$_SESSION['delAddress'] = $delivery_address;
						$_SESSION['delSuburb'] = $delivery_suburb;
						$_SESSION['delCity'] = $delivery_city;
						$_SESSION['delPostcode'] = $delivery_postcode;
						$_SESSION['phone'] = $phone;
						$_SESSION['email'] = $email;
						$_SESSION['billAddress'] = $billing_address;
						$_SESSION['billSuburb'] = $billing_suburb;
						$_SESSION['billCity'] = $billing_city;
						$_SESSION['billPostcode'] = $billing_postcode;
						$_SESSION['island1'] = $island;
						$_SESSION['notes'] = $notes;
					}
					else if ($reset != '') {
						$to = $email;
						$subject = 'Your Artprints password has been reset';
						$headers = 'CC: nzartprints@gmail.com'."\n";
						$headers .= 'BCC: julialarsen.nz.co.nz'."\n";
						$headers .= 'From: nzartprints@gmail.com'."\n";
						$headers .= 'MIME-Version: 1.0'."\n";
						$headers .= 'Content-Type: text/html; charset=ISO-8859-1'."\n";
						$message = '<html><body>';
						$message .= '<img src="http://www.artprints.kiwi.nz/images/logo_desktop.jpg" alt="Artprints: Publishers and Distributors" />'."\n";
						$message .= '<p>Your Artprints account password has been reset to \''.$reset.'\'.  Simply go to <a href="http://www.artprints.kiwi.nz/index.php?page=login">www.artprints.kiwi.nz</a> to log in using your reset password.  Once you are logged in you can change your password on <a href="http://www.artprints.kiwi.nz/index.php?page=account">your account </a>page. And of course, we\'re only an email away if you\'d like to get in <a href="http://www.artprints.kiwi.nz/index.php?page=contact">contact </a>with us.</p>'."\n";
						$message .= '<p>Happy shopping from the team at Artprints!</p>'."\n";
						$message .= '<img src="http://www.artprints.kiwi.nz/images/banners/mobile_artprints_team.jpg" />'."\n";
						$message .= "</body></html>";
						// The email worked
						$message = wordwrap($message, 70, "\r\n");
						$message = stripslashes($message);
						if (@mail($to, $subject, $message, $headers)) {
						}
					}
				}
				// no change
				else {
					$result = 'nochange';
				}
			}
			// error with query, unlikey except in development
			else {
//				 echo 'Error updating user details'.$qry;  
			}
		return $result;   
	}


	// gets a customer by id or all customers without needing a password
	// used for selecting a customer when logged in as admin
	// returns an array of $customers if all and $customer if requested by id
	public function collectCustomer($id='') {
		// get all customers
		if (!$id && $_SESSION['access'] == 'admin') {
			$qry = "SELECT custID, custFirstName, custLastName, custCompany, custPassword, custDelAddress, custDelSuburb, custDelCity,  custDelPostcode, custDelIsland, custDelCountry, custBillAddress, custBillSuburb, custBillCity,  custBillPostcode, custBillCountry, custPhone, custEmail, custDeposit, custStatus, custAccess, custNotes, custCreditLimit FROM customers";
		}
		// get  single customer
		else if ($id == $_SESSION['custID'] || $_SESSION['access'] == 'admin') {
			$splitId = explode(':', $id);
			$id = $splitId[0];
			$qry = "SELECT custID, custFirstName, custLastName, custCompany, custPassword, custDelAddress, custDelSuburb, custDelCity,  custDelPostcode, custDelIsland, custDelCountry, custBillAddress, custBillSuburb, custBillCity,  custBillPostcode, custBillCountry, custPhone, custEmail, custDeposit, custStatus, custAccess, custNotes, custCreditLimit FROM customers WHERE custID = '$id'";
		}
		$rs = $this->db->query($qry);
		if ($rs) {
			if ($rs->num_rows > 0) {
				if ($id) {
					// convert the page data into an associative array $customer[]		
					$customer = $rs->fetch_assoc(); 	
					return $customer;
				}
				else {
					$customers = array();
					// numeric indexed array
					while($row = $rs->fetch_assoc()) {
						$customers[] = $row;	
					}
					return $customers;	
				}
			}
			else {
//				 echo 'customer not found'; // (development only)
			}
		}
		else {
//			echo 'Error executing query '.$qry; // (development only)
		}
	}


	// this function updates a password
	// uses $id if admin to select appropriate customer
	// returns true if successful
	public function updatePassword($id='') {
		if(!$id) {
			$id = $_SESSION['custID'];
		}
		if (!get_magic_quotes_gpc()) {
			// if magic_quotes isn't on then sanitise so doesn't break query
			$this->sanitiseInput();
		}
		$password = trim($_POST['password1']);
		// add a salt as extra security
		$salt = substr(uniqid(), 0, 10);
		$hash = sha1($salt . $password);
		$password = $salt . ':' . $hash;

		$qry = "UPDATE customers SET custPassword = '$password' WHERE custID = $id";
		$rs = $this->db->query($qry);
		if (!$rs) {
//			echo 'Query error! '. $qry; 
		}
		else {
			if ($this->db->affected_rows > 0) {
				return true;
			}
			else {
//				echo $qry;
				return false;
			}
		}
	}


	/* ---------------------------------------------------------------------- */
	/* ------------------------------- artists ------------------------------ */
	/* ---------------------------------------------------------------------- */
	

	// this function gets all the artists from the artists table
	// returns array $artist
	public function getArtists($id='', $all='', $name='') {
		if ($id) {
			$qry = "SELECT artistID, artistFirstName, artistLastName, artistName, artistPhone, artistEmail, artistBankDets, artistBio, artistHidden FROM artists WHERE artistID = $id ";
		}
		else if ($all) {
			$qry = "SELECT artistID, artistName FROM artists ORDER BY artistName ASC ";
		}
		else if ($name) {
			$qry = "SELECT artistName FROM artists WHERE artistID = $name";
		}
		else {
			$qry = "SELECT artistID, artistName, artistBio FROM artists WHERE artistHidden = '' ORDER BY artistName ASC ";
		}
		$rs = $this->db->query($qry);
		// if at least 1 record retrieved
		if($rs->num_rows > 0) { 
			$artists = array();
			// numeric indexed array
			while($row = $rs->fetch_assoc()) {
				$artists[] = $row;		
			}
		}
		 // no rows retrieved (the user has not made a purchase while logged in)
		else { 
//			echo "Error with getArtists query".$qry; 
		}
		return $artists;
	}


	// this function adds artist info
	// or edits an artists' info if $id is provided
	// returns the id if adding a new artist
	// returns true if a successful edit
	// returns false if a failed edit
	public function editArtist($id='') {
		if (!get_magic_quotes_gpc()) {
			// if magic_quotes isn't on then sanitise so doesn't break query
			$this->sanitiseInput();
		}
		extract($_POST);
		// updating
		if ($id) {
			$qry = "UPDATE artists SET artistFirstName = '$first_name', artistLastName = '$last_name', artistName = '$known_name', artistPhone = '$phone', artistEmail = '$email', artistBankDets = '$bank_account', artistBio = '$bio', artistHidden = '$hidden' WHERE artistID = $id";
		}
		// adding
		else {
			$qry = "INSERT INTO artists VALUES (NULL, '$first_name', '$last_name', '$known_name', '$phone', '$email', '$bank_account', '$artistBio', '$artistHidden')";
		}
		$rs = $this->db->query($qry);
		if (!$rs) {
//			 echo 'Query error! '. $qry; // unlikely except in development
		}
		else {
			if ($this->db->affected_rows > 0) {
				if (!$id) {
					$id = $this->db->insert_id; 
					return $id;
				}
				return true;
			}
			else {
				return false;
			}
		}
	}


	/* ---------------------------------------------------------------------- */
	/* --------------- artworks / filters for catalgoue pages --------------- */
	/* ---------------------------------------------------------------------- */


	// this function gets all the subjects from the subjects table
	// returns array $subjects
	public function getSubjects() {
		$qry = "SELECT subjectID, subjectName, subjectDescription FROM subjects ORDER BY subjectName ASC";
		$rs = $this->db->query($qry);
		// if at least 1 record retrieved
		if($rs->num_rows > 0) { 
			$subjects = array();
			// numeric indexed array
			while($row = $rs->fetch_assoc()) {
				$subjects[] = $row;		
			}
		}
		 // no rows retrieved (the user has not made a purchase while logged in)
		else { 
//			echo "Error with getSubjects query"; 
		}
		return $subjects;
	}


	// counts the amount of products in the database, either 
	// subject, artist, size, price or whole catalogue (ie all framed prints)
	// $type = the filter eg subject/artist/size etc
	// $value = the value for that search ie "abstract"
	public function countArtworks($type, $value, $format='') {
		// count matches to a particular subject
		if ($type == 'subject') {
			$qry = "SELECT artSubjects.artID FROM `art` left join `artists` ON art.artistID = artists.artistID left join `artSubjects` ON artSubjects.artID = art.artID left join `subjects` ON artSubjects.subjectID = subjects.subjectID WHERE artType = '$format' and artHiddenDate = '' and artistHidden = '' and artSubjects.subjectID = $value";
		}

		// count matches to a particular artist
		else if ($type == 'artist') {
			$qry = "SELECT artID FROM `art` left join `artists` ON art.artistID = artists.artistID WHERE artType = '$format' and artHiddenDate = '' and artistHidden = '' and art.artistID = $value";
		}

		// count matches to a particular size
		else if ($type == 'size'){
			$size = $this->getSize($value);
			$min = $size['min'];
			$max = $size['max'];
			$qry = "SELECT art.artID, artistName, artists.artistID, artImage, artDescription, artImage, artName, sizeRRP, sizeWSP, sizeWidth, sizeHeight FROM `sizePrices` left join `art` ON art.artID = sizePrices.artID left join `artists` ON art.artistID = artists.artistID WHERE artHiddenDate = '' and artistHidden = '' AND artType = '$format' ORDER BY artName DESC";
		}

		// count matches to a particular price
		else if ($type == 'price') {
			$price = $this->getMinMax($value);
			$min = $price['min'];
			$max = $price['max'];
			// wholesaler so show wholesale prices
			if (isset($_SESSION['access'])) {
				$qry = "SELECT art.artID, artistName, artists.artistID, artImage, artDescription, artImage, artName, sizeRRP, sizeWSP, sizeWidth, sizeHeight FROM `sizePrices` left join `art` ON art.artID = sizePrices.artID left join `artists` ON art.artistID = artists.artistID WHERE artHiddenDate = '' and artistHidden = '' AND artType = '$format' AND sizeWSP > $min AND sizeWSP < $max ORDER BY artName DESC";
			}
			// public so show retail prices
			else {
				$qry = "SELECT art.artID, artistName, artists.artistID, artImage, artDescription, artImage, artName, sizeRRP, sizeWSP, sizeWidth, sizeHeight FROM `sizePrices` left join `art` ON art.artID = sizePrices.artID left join `artists` ON art.artistID = artists.artistID WHERE artHiddenDate = '' and artistHidden = '' AND artType = '$format' AND sizeRRP > $min AND sizeRRP < $max ORDER BY artName DESC";
			}		
		}

		// else if main catalogue page, value = framed/prints/canvas
		else if ($type == 'catalogue') {
			$qry = "SELECT artID FROM `art` left join `artists` ON art.artistID = artists.artistID WHERE  artHiddenDate = '' and artistHidden = '' and artType = '$value'";
		}
		$rs = $this->db->query($qry);	
		//	test if query is successful
		if ($rs) {	
			//	if a record is retrieved
			$allProducts = array();
			if ($type == 'price' || $type == 'size') {
				if($rs->num_rows > 0) {   
					while($row = $rs->fetch_assoc()) {
						// numeric indexed array
						$allProducts[] = $row;	
				    }
					if (is_array($allProducts)) {
			    		$products = array();
			    		foreach ($allProducts as $product) {
			    			$id = $product['artID'];
			    			if ($type == 'price') {
				    			if (!in_array($id, $products)) {
				    				$products[$id]['height'] = $product['sizeHeight'];
				    				$products[$id]['width'] = $product['sizeWidth'];
				   					$products[$id]['artistName'] = $product['artistName'];
				   					$products[$id]['artName'] = $product['artName'];
				   					$products[$id]['artID'] = $product['artID'];
				   					$products[$id]['artistID'] = $product['artistID'];
				   					$products[$id]['artImage'] = $product['artImage'];
				    			}
				    		}
				    		else {
				    			$size = $product['sizeHeight'] + $product['sizeWidth'];
				    			if (!in_array($id, $products) && $size > $min && $size < $max) {
				    				$products[$id]['height'] = $product['sizeHeight'];
				    				$products[$id]['width'] = $product['sizeWidth'];
				   					$products[$id]['artistName'] = $product['artistName'];
				   					$products[$id]['artName'] = $product['artName'];
				   					$products[$id]['artID'] = $product['artID'];
				   					$products[$id]['artistID'] = $product['artistID'];
				   					$products[$id]['artImage'] = $product['artImage'];
				    			}
				    		}
				    	}
				    	$artCount = count($products);
				    	return $artCount;
				    }
				}
			}
			else if ($type != 'price' && $rs->num_rows > 0) {	
				while($row = $rs->fetch_assoc()) {
					// numeric indexed array
					$allProducts[] = $row;	
				}
				$products['artCount'] = 0;
				foreach ($allProducts as $product) {
					$size = $this->getSizePrices($product['artID']);
					if (is_array($size)) {
						$products['artCount'] = $products['artCount'] + 1;
					}
				}
				return $products['artCount'];
			}	
			//	no row retrieved
			else {			
//				echo "No products found";
			}
		}
		// error with query, development
		else {
//			echo "Error executing query".$qry;
		}
		return false; 
	}


	// collects products from the database in specified batch sizes rather than all results at once
	public function getProductsByBatch($shopNum='', $limit='', $type='', $all='', $search='', $filter='', $value='') {
		if ($shopNum) {
			$start = ($shopNum - 1) * $limit;
		}
		if ($all != '') {
			// batch for catalogue ie framed/prints/canvas
			if($limit != '') {
				$qry = "SELECT artID, art.artistID, artName, artDescription, artImage, artType, artKeywords, artistName, artHiddenDate, artistHidden FROM `art` left join `artists` ON art.artistID = artists.artistID WHERE artType = '$type' ORDER BY artUpdateDate DESC LIMIT $start, $limit";
			}
			// list of artworks for admin to select from in dropdown box for editing
			else {
				$qry = "SELECT artID, art.artistID, artName, artDescription, artImage, artType, artKeywords, artistName FROM `art` left join `artists` ON art.artistID = artists.artistID WHERE artType = '$type' ORDER BY artName";
			}
		}
		else if ($type && $filter == '') {
			$qry = "SELECT artID, art.artistID, artName, artDescription, artImage, artistName, artHiddenDate, artistHidden FROM `art` left join `artists` ON art.artistID = artists.artistID WHERE artType = '$type' and artHiddenDate = '' and artistHidden = '' ORDER BY artUpdateDate DESC LIMIT $start, $limit";
		}
		// for search
		else if (isset($_GET['search'])) {
			$search = $_GET['search'];
			if (!get_magic_quotes_gpc()) {		
				// if magic_quotes isn't on then sanitise so doesn't break query
				$search = $this->db->real_escape_string($search);
			}	
			$search = strip_tags($search);
			$search = trim($search);
			$qry = "SELECT artID, art.artistID, artName, artDescription, artImage, artType, artKeywords, artistName, artHiddenDate, artistHidden FROM `art` left join `artists` ON art.artistID = artists.artistID WHERE artHiddenDate = '' AND artistHidden = '' AND artName LIKE '%$search%' OR artDescription LIKE '%$search%' OR artKeywords LIKE '%$search%' OR artistName LIKE '%$search%' ORDER BY artUpdateDate DESC LIMIT $start, $limit";
		}
		// for filter subject
		if ($filter == 'subject') {
			$qry = "SELECT artSubjects.artID, art.artistID, artName, artDescription, artImage, artistName, subjectName, artHiddenDate, artistHidden FROM `art` left join `artists` ON art.artistID = artists.artistID left join `artSubjects` ON artSubjects.artID = art.artID left join `subjects` ON artSubjects.subjectID = subjects.subjectID WHERE artType = '$type' and artHiddenDate = '' and artistHidden = '' and artSubjects.subjectID = $value ORDER BY artUpdateDate DESC LIMIT $start, $limit";
		}
		// for particular artist
		else if ($filter == 'artist') {
			$qry = "SELECT artID, art.artistID, artName, artDescription, artImage, artistName, artHiddenDate, artistHidden FROM `art` left join `artists` ON art.artistID = artists.artistID WHERE artType = '$type' and artHiddenDate = '' and artistHidden = '' and art.artistID = $value ORDER BY artUpdateDate DESC LIMIT $start, $limit";
		}
		// for a particular size
		else if ($filter == 'size'){
			$size = $this->getSize($value);
			$min = $size['min'];
			$max = $size['max'];
			$qry = "SELECT art.artID, artistName, artists.artistID, artImage, artDescription, artImage, artName, sizeRRP, sizeWSP, sizeWidth, sizeHeight, artHiddenDate, artistHidden FROM `sizePrices` left join `art` ON art.artID = sizePrices.artID left join `artists` ON art.artistID = artists.artistID WHERE artHiddenDate = '' and artistHidden = '' AND artType = '$type' ORDER BY artUpdateDate DESC";
		}
		// for a particular price
		else if ($filter == 'price') {
			$price = $this->getMinMax($value);
			$min = $price['min'];
			$max = $price['max'];
			// wholesaler so show wholesale prices
			if (isset($_SESSION['access'])) {
				$qry = "SELECT art.artID, artistName, artists.artistID, artImage, artDescription, artImage, artName, sizeRRP, sizeWSP, sizeWidth, sizeHeight FROM `sizePrices` left join `art` ON art.artID = sizePrices.artID left join `artists` ON art.artistID = artists.artistID WHERE artHiddenDate = '' and artistHidden = '' AND artType = '$type' AND sizeWSP > $min AND sizeWSP < $max ORDER BY artUpdateDate DESC";
			}
			// public so show retail prices
			else {
				$qry = "SELECT art.artID, artistName, artists.artistID, artImage, artDescription, artImage, artName, sizeRRP, sizeWSP, sizeWidth, sizeHeight FROM `sizePrices` left join `art` ON art.artID = sizePrices.artID left join `artists` ON art.artistID = artists.artistID WHERE artHiddenDate = '' and artistHidden = '' AND artType = '$type' AND sizeRRP > $min AND sizeRRP < $max ORDER BY artUpdateDate DESC LIMIT $start, $limit";
			}
		}
		// for a particular orientation
		else if ($filter == 'orientation') {
			$qry = "SELECT art.artID, artistName, artists.artistID, artImage, artDescription, artImage, artName, sizeWidth, sizeHeight, artHiddenDate, artistHidden FROM `sizePrices` left join `art` ON art.artID = sizePrices.artID left join `artists` ON art.artistID = artists.artistID WHERE artHiddenDate = '' and artistHidden = '' AND artType = '$type' ORDER BY artUpdateDate DESC";
		}
//		echo 'qry = '.$qry.'<br />';
		$rs = $this->db->query($qry);  
		if($rs) {
			// if at least 1 record retrieved
			if($rs->num_rows > 0) {  
				$allProducts = array();
				while($row = $rs->fetch_assoc()) {
					// numeric indexed array
					$allProducts[] = $row;	
					if (is_array($allProducts)) {
			    		$products = array();
			    		if ($filter == 'price') {
				    		foreach ($allProducts as $product) {
				    			$id = $product['artID'];
				    			if (!in_array($id, $products)) {
				    				$products[$id]['height'] = $product['sizeHeight'];
				    				$products[$id]['width'] = $product['sizeWidth'];
				   					$products[$id]['artistName'] = $product['artistName'];
				   					$products[$id]['artName'] = $product['artName'];
				   					$products[$id]['artID'] = $product['artID'];
				   					$products[$id]['artistID'] = $product['artistID'];
				   					$products[$id]['artImage'] = $product['artImage'];
				   					$products[$id]['artHiddenDate'] = $product['artHiddenDate'];
				   					$products[$id]['artistHidden'] = $product['artistHidden'];
				    			}
				    		}
				    	}
				    	else if ($filter == 'size') {
				    		foreach ($allProducts as $product) {
				    			$id = $product['artID'];
				    			$size = $product['sizeHeight'] + $product['sizeWidth'];
				    			if (!in_array($id, $products) && $size > $min && $size < $max) {
				    				$products[$id]['height'] = $product['sizeHeight'];
				    				$products[$id]['width'] = $product['sizeWidth'];
				   					$products[$id]['artistName'] = $product['artistName'];
				   					$products[$id]['artName'] = $product['artName'];
				   					$products[$id]['artID'] = $product['artID'];
				   					$products[$id]['artistID'] = $product['artistID'];
				   					$products[$id]['artImage'] = $product['artImage'];
				   					$products[$id]['artHiddenDate'] = $product['artHiddenDate'];
				   					$products[$id]['artistHidden'] = $product['artistHidden'];
				    			}
				    		}
				    	}
			    	}
				}
			}
			// unlikely except in development
			else {
				$allProducts = "No matches"; 
			}
			if($filter == 'price' || $filter == 'size') {
				$pagination = $shopNum - 1;	
				$num = count($products);
				if ($num != 0) {
					if (is_array($products)) {
						$products = array_chunk($products, $limit);	
						$products = $products[$pagination];
					}
				}
				else {
					$products = 0;
				}
				return $products;
			}
			return $allProducts;
		}
		// error executing query  (development only)
		else {  
//			 echo "Error finding products"; 
		}	
		return false;
	}


	// collects sizePrice info for a specific artID
	// returns array $sizes if successful or false if not
	public function getSizePrices($artID, $sizeID='') {
		// collect info by size id
		if ($sizeID) {
			$qry = "SELECT artID, sizeId, APCode, sizeWidth, sizeHeight, sizeRRP, sizeWSP, sizeRoyalty FROM `sizePrices` WHERE sizePrices.artID = $artID AND sizeId = $sizeID";
		}
		// collect info by art id
		else {
			$qry = "SELECT artID, sizeId, APCode, sizeWidth, sizeHeight, sizeRRP, sizeWSP, sizeRoyalty FROM `sizePrices` WHERE sizePrices.artID = $artID";
		}
		$rs = $this->db->query($qry);  
		if($rs) {
			// if at least 1 record retrieved
			if($rs->num_rows > 0) {   
				$sizes = array();
				while($row = $rs->fetch_assoc()) {
					// numeric indexed array
					$sizes[] = $row;		
				}
			}
			// unlikely except in development
			else {
				$sizes = "no matches"; 
			}
//			echo $qry.'<br />';	
			return $sizes;
		}
		// error executing query  (development only)
		else {  
//			 echo "Error finding sizes"; 
		}
		return false;
	}


	// returns all types ie framed, print, canvas for matching aritstId and artName
	public function getTypesForProduct($artistID, $artName) {
		if (!get_magic_quotes_gpc()) {
			// if magic_quotes isn't on then sanitise so doesn't break query
			$artName = $this->db->real_escape_string($artName);
		}
		$qry = "SELECT artID, artDescription, artImage, artType FROM `art` WHERE artistID = $artistID and artName = '$artName' and artHiddenDate = ''";
		$rs = $this->db->query($qry);  
		// check result returned and put all info into $types array
		if($rs && $rs->num_rows > 0) {   
			$types = array();
			while($row = $rs->fetch_assoc()) {
				// numeric indexed array
				$types[] = $row;		
			}
		}
		// no matches
		else {
			$types = "No matches"; 
		}
		return $types;
	}


	// returns all info from the database for a given product id
	public function getProductByID($productID, $admin='') {
		if ($admin) {
			$qry = "SELECT artID, art.artistID, artName, artDescription, artKeywords, artImage, artType, artistName, artistBio, artHiddenDate FROM `art` left join `artists` ON art.artistID = artists.artistID WHERE artID = $productID";
		}
		else {
			$qry = "SELECT artID, art.artistID, artName, artDescription, artKeywords, artImage, artType, artistName, artistBio, artHiddenDate FROM `art` left join `artists` ON art.artistID = artists.artistID WHERE artID = $productID and artHiddenDate = ''";
		}
		$rs = $this->db->query($qry);  
		// check result returned and put all info into $product array
		if($rs && $rs->num_rows > 0) {   
			$product = array();
			while($row = $rs->fetch_assoc()) {
				// numeric indexed array
				$product[] = $row;		
			}
		}
		// no matches
		else {
			$product = "No matches".$qry; 
		}
		return $product;
	}

	
	// this function collects the subjects for a particular art id
	public function getSubjectsByArtID($id) {
		$qry = "SELECT artSubID, subjectID, artID FROM `artSubjects` WHERE artID = $id";
		$rs = $this->db->query($qry);  
		// check result returned and put all info into $types array
		if($rs && $rs->num_rows > 0) {   
			$subjects = array();
			while($row = $rs->fetch_assoc()) {
				// numeric indexed array
				$subjects[] = $row;		
			}
		}
		// no matches
		else {
			$subjects = "No matches"; 
		}
		return $subjects;
	}


	/* ---------------------------------------------------------------------- */
	/* --------------------------- add / editing art ------------------------ */
	/* ---------------------------------------------------------------------- */


	// this function adds/edits art info
	// returns $id if successful
	public function editArt($id='', $image='') {
		if (!get_magic_quotes_gpc()) {
			// if magic_quotes isn't on then sanitise so doesn't break query
			$this->sanitiseInput();
		}
		extract($_POST);
		// updating
		$name = $_SESSION['name'];
		if ($id && $image) {
			$now = getdate();
			$year = $now['year'];
			$month = $now['mon'];
			$date = $now['mday'];
			$hour = $now['hours'];
			$min = $now['minutes'];
			$sec = $now['seconds'];
			$timeDate = $year.'-'.$month.'-'.$date.' '.$hour.':'.$min.':'.$sec;
			$qry = "UPDATE art SET artImage = '$image', artUpdatedBy = '$name', artUpdateDate = '$timeDate' WHERE artID = $id";
		}
		else if ($id) {
			$now = getdate();
			$year = $now['year'];
			$month = $now['mon'];
			$date = $now['mday'];
			$hour = $now['hours'];
			$min = $now['minutes'];
			$sec = $now['seconds'];
			$timeDate = $year.'-'.$month.'-'.$date.' '.$hour.':'.$min.':'.$sec;
			$qry = "UPDATE art SET artistID = '$artistID', artName = '$art_name', artDescription = '$art_description', artType = '$art_type', artKeywords = '$art_keywords', artUpdatedBy = '$name', artUpdateDate = '$timeDate', artHiddenDate = '$hidden' WHERE artID = $id";
		}
		// adding
		else {
			$qry = "INSERT INTO art (artistID, artName, artDescription, artType, artKeywords, artCreatedBy, artCreationDate, artHiddenDate) 
			VALUES ($artistID, '$art_name', '$art_description', '$art_type', '$art_keywords', '$name', NULL, '$hidden')";
		}
		$rs = $this->db->query($qry);
		if (!$rs) {
//			 echo 'Query error! '. $qry; // unlikely except in development
		}
		else {
			if ($this->db->affected_rows > 0) {
				if (!$id) {
					$id = $this->db->insert_id; 
					return $id;
				}
				return $id;
			}
			else {
				return false;
			}
		}
	}


	// this function deletes any existing art/subject entries by id and returns true if successful
	public function deleteArtSubject($id) {
		$qry = "DELETE FROM artSubjects WHERE artID = $id";
		$rs = $this->db->query($qry);
		if ($rs) {
			if ($this->db->affected_rows > 0) {
				$result = true;
			}
			else {
				$result = false;
			}
			return $result;
		}
		// error with query (development only)
		else {
//			echo 'Error deleting art/subject entry '.$qry; 
		}	
	}


	// this function adds art/subject entries and return true if successful
	public function updateArtSubject($subjectID, $artID) {
		$qry = "INSERT INTO artSubjects VALUES (NULL, '$subjectID', '$artID')";
		$rs = $this->db->query($qry);
		if (!$rs) {
//			 echo 'Query error! '. $qry; // unlikely except in development
		}
		else {
			if ($this->db->affected_rows > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}


	// this function updates info in the sizePrice table for a particular size returns true if successful
	public function updateSizePrice($sizeInfo, $id='', $delete='') {
		foreach($sizeInfo as $key=>&$info) {
			if ($key != 'code') {
				settype($info, "float");
			}
		}
		extract($sizeInfo);
		$rrp = str_replace ('$' ,'' , $rrp);
		$wsp = str_replace ('$' ,'' , $wsp);
		$royalty = str_replace ('$' ,'' , $royalty);
		$code = str_replace ('AP' ,'' , $code);
		$code = str_replace ('ap' ,'' , $code);
		$code = str_replace (' ' ,'' , $code);
		$code = 'AP '.$code;
		if ($delete) {
			$qry = "DELETE from sizePrices WHERE sizeID = $id";
		}
		else if ($id) {
			$qry = "UPDATE sizePrices SET APCode = '$code', artID = $artID, sizeWidth = $width , sizeHeight = $height, sizeRRP = $rrp, sizeWSP = $wsp, sizeRoyalty = $royalty WHERE sizeID = $id";
		}
		else {
			$qry = "INSERT INTO sizePrices SET APCode = '$code', artID = $artID, sizeWidth = $width , sizeHeight = $height, sizeRRP = $rrp, sizeWSP = $wsp, sizeRoyalty = $royalty";
		}
//		echo $qry;
		$rs = $this->db->query($qry);
		if (!$rs) {
//			 echo 'Query error! '. $qry.'<br />'; // unlikely except in development
		}
		else {
			if ($this->db->affected_rows > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}


	/* ---------------------------------------------------------------------- */
	/* ----------------------- admin account functions ---------------------- */
	/* ---------------------------------------------------------------------- */


	// this function gets any account that have been requested but not confirmed/declined yet
	// returns array $accounts if there are some or $accounts = 'no new accounts' if no new requests
	public function getNewAccounts() {
		$qry = "SELECT custID, custFirstName, custLastName, custCompany, custPassword, custDelAddress, custDelSuburb, custDelCity,  custDelPostcode, custDelCountry, custBillAddress, custBillSuburb, custBillCity,  custBillPostcode, custBillCountry, custPhone, custEmail, custDeposit, custStatus, custAccess FROM customers WHERE custStatus = 'inactive'";
		$rs = $this->db->query($qry);
		if($rs) {
			// if at least 1 record retrieved
			if($rs->num_rows > 0) {   
				$accounts = array();
				// numeric indexed array
				while($row = $rs->fetch_assoc()) {
					$accounts[] = $row;		
				}
			}
			 // no rows retrieved (the user has not made a purchase while logged in)
			else { 
				$accounts = 'no new accounts'; 
			}
		}
		 // error executing query (development only)
		else { 
//			echo "Error with new account query" . $qry; 
		}	
		return $accounts;
	}


	// this function updates an account status
	// possible account status is 'inactive', 'active', 'frozen'
	// if was not active and was changed to active, an email is sent to the customer
	// returns true if update successful
	public function updateAccountStatus() {
		extract($_POST);
		if ($approve_cust) {
			$status = 'active';
		}
		else {
			$status = 'frozen';
		}
		$qry = "UPDATE customers SET custStatus = '$status' WHERE custID = $custID";
		$rs = $this->db->query($qry);
		if (!$rs) {
//			echo 'Query error! '. $qry; 
		}
		else {
			if ($this->db->affected_rows > 0) {
				// if the account has been updated to active send the client an email to alert them
				if ($status == 'active') {
					$to = $email;
					$subject = 'Your Artprints account has been activated!';
					$headers .= 'CC: nzartprints@gmail.com'."\n";
					$headers .= 'BCC: julialarsen.nz@gmail.com'."\n";
					$headers .= 'From: nzartprints@gmail.com'."\n";
					$headers .= 'MIME-Version: 1.0'."\n";
					$headers .= 'Content-Type: text/html; charset=ISO-8859-1'."\n";
					$message = '<html><body>';
					$message .= '<img src="http://www.artprints.kiwi.nz/images/logo_desktop.jpg" alt="Artprints: Publishers and Distributors" />';
					$message .= '<h4>Your Artprints account has been activated!</h4>'."\n";
					$message .= '<p>Hi '.stripslashes($_POST['first_name']).',</p>'."\n";
					$message .= '<p>The artprints account for '.stripslashes($_POST['company']).' has been activated.'."\n";
					$message .= 'You can now start ordering.  Simply go to <a href="http://www.artprints.kiwi.nz">www.artprints.kiwi.nz</a> to place your first order of <a href="http://www.artprints.kiwi.nz/index.php?page=prints">prints</a>, <a href="http://www.artprints.kiwi.nz/index.php?page=framed">framed art</a> and <a href="http://www.artprints.kiwi.nz/index.php?page=canvas">canvas</a>.  Check out our <a href="http://www.artprints.kiwi.nz/index.php?page=shipping">shipping</a> and <a href="http://www.artprints.kiwi.nz/index.php?page=faq">faq </a>pages for commonly asked questions.</p>'."\n";
					$message .= 'We\'re only an email away if you\'d like to get in <a href="http://www.artprints.kiwi.nz/index.php?page=contact">contact </a>with us.</p>'."\n";
					$message .= "<p>Happy shopping from the team at Artprints!</p>";
					$message .= '<img src="http://www.artprints.kiwi.nz/images/banners/mobile_artprints_team.jpg" />'."\n";
					$message .= "</body></html>";
					// The email worked
					$message = wordwrap($message, 70, "\r\n");
					$message = stripslashes($message);
					// The email worked
					if (mail($to, $subject, $message, $headers)) {
					} 
				} 
				return true;
			}
			else {
				return false;
			}
		} 
	}

	
	/* ---------------------------------------------------------------------- */
	/* -------------------------------- banners ----------------------------- */
	/* ---------------------------------------------------------------------- */


	// collects banners from the database
	// by id if $id provided
	// only those with bannerVisibility 'visible' for display if $visible
	// all banners if $id and $visible not provided
	// returns one or more banners
	public function getBanners($id='', $visible='') {
		if ($id != '') {
			$qry = "SELECT bannerID, bannerName, bannerAlt, bannerLink, bannerVisibility FROM banners WHERE bannerID = $id ORDER BY bannerUpdated DESC";
		}
		else if ($visible == 'visible') {
			$qry = "SELECT bannerID, bannerName, bannerAlt, bannerLink, bannerVisibility FROM banners WHERE bannerVisibility = 'visible' ORDER BY bannerUpdated DESC";
		}
		else {
			$qry = "SELECT bannerID, bannerName, bannerAlt, bannerLink, bannerVisibility FROM banners ORDER BY bannerUpdated DESC";
		}
		$rs = $this->db->query($qry);
		if($rs->num_rows > 0) {   
			$banners = array();
			// numeric indexed array
			while($row = $rs->fetch_assoc()) {
				$banners[] = $row;		
			}
		}
		// no rows retrieved (error)
		else { 
//			echo 'no banners';
		}
		return $banners;
	}


	// adds a new banner into the database if $name is provided
	// or edits a banner
	// returns true if edit successful, false if not or id if added
	public function editBanner($name='') {
		if (!get_magic_quotes_gpc()) {
			// if magic_quotes isn't on then sanitise so doesn't break query
			$this->sanitiseInput();
		}
		extract($_POST);
		if ($name != '') {
			$qry = "INSERT into banners VALUES(NULL, '$name', '$alt', '$link', 'visible', NULL)";
		}
		else {
			$qry = "UPDATE banners SET bannerAlt = '$alt', bannerLink = '$link', bannerVisibility = '$visibility', bannerUpdated = NULL WHERE bannerID = $banner_id";
		}
		$rs = $this->db->query($qry);
		if($this->db->affected_rows > 0) {   
			$edit = true;
			if ($name != '') {
				$edit = $this->db->insert_id;	
			}
		}
		// no rows retrieved (error)
		else { 
			$edit = false;
		}
		return $edit;
	}


	/* ---------------------------------------------------------------------- */
	/* ------------------------------ checkout ------------------------------ */
	/* ---------------------------------------------------------------------- */


	// this function takes info from the checkout page and adds it to the orders and orderedprods tables
	//it calls function sendConfirmationEmail if added successful and returns the $orderID
	public function createOrder() {
		if (!get_magic_quotes_gpc()) {
			$this->sanitiseInput();
		}
		$finances = $this->calculateFinancesForOrder();
		extract($finances);
		extract($_POST);
		$total = $_SESSION['total'];
		$shipping = $_SESSION['shipping'];
		$qry = "INSERT INTO orders VALUES (NULL, $custID, '$purchase_order', '$first_name', '$last_name', '$delivery_address', '$delivery_suburb', '$delivery_city', $delivery_postcode, 'New Zealand', '$first_name', '$last_name', '$billing_address', '$billing_suburb', '$billing_city', $billing_postcode, 'New Zealand', '', $orderValue, $shipping, $orderGSTValue, $total, '', $total, NULL, 'approved', 'pending', NULL, '', '', $quantity, '$notes')";	
		$rs = $this->db->query($qry);
		if ($rs) {
			if ($this->db->affected_rows > 0) {
				// get the ID generated
				$orderID = $this->db->insert_id;	
				$qry1 = "INSERT INTO orderedArt VALUES ";
				// get the number of items in the cart
				$itemCnt = count($_SESSION['cart']);	
				$i = 0;
				// foreach product insert into orderedprods the item
				foreach($_SESSION['cart'] as $product) {
					extract($product);
					$size = $this->getSizePrices($artID, $sizeID);
					extract($size);
					extract($size[0]);
					$qry1 .= "(NULL, $orderID, $artID, '$APCode', $sizeWidth, $sizeHeight, $sizePrice, $orderProdQty, '$format', 'pending', '', $artistID, $sizeID, $sizeRoyalty, '')";
					if (++$i < $itemCnt) {
						$qry1 .= ',';
					}
				}
				$rs1 = $this->db->query($qry1);
				if ($rs1 && $this->db->affected_rows > 0) {
					// send order created so send confirmation email
					$this->sendConfirmationEmail($orderID);
					if ($shipping > 50) {
						$to = 'nzartprints@gmail.com';
						$subject = 'Order with expensive shipping!';
						$headers = 'BCC: niki@afas.co.nz'."\n";
						$headers .= 'From: nzartprints@gmail.com'."\n";
						$headers .= 'MIME-Version: 1.0'."\n";
						$headers .= 'Content-Type: text/html; charset=ISO-8859-1'."\n";
						$message = '<html><body>';
						$message .= '<img src="http://www.artprints.kiwi.nz/images/logo_desktop.jpg" alt="Artprints: Publishers and Distributors" />';
						$message .= '<h4>A new order has been placed with shipping over $50.</h4>'."\n";
						$message .= "<p>Hi Artprints,</p>";
						$message .= '<p>'.$_SESSION['name'].' from '.$_SESSION['company'].' has just placed an order with a shipping cost of $'.$shipping.'.</p>';
						$message .= '<p>The order number is '.$orderID.'.  Please contact this customer to discuss more cost effective shipping options.</p>';
						$message .= '<p>Customer: '.$_SESSION['name'].'<br />';
						$message .= '<p>Company: '.$_SESSION['company'].'<br />';
						$message .= '<p>Phone: '.$_SESSION['phone'].'<br />';
						$message .= '<p>Email: '.$_SESSION['email'].'<br />';
						$message .= '<p>Order Number: '.$orderID.'</p>';
						$message .= '</body></html>';
						@mail($to, $subject, $message, $headers);
					} 
					unset($_SESSION['cart'], $_SESSION['shipping'], $_SESSION['total']);
					return $orderID;
				}
				// Error executing orderedproducts query (devlopment only)
				else {
//					echo 'Error executing orderedproducts query'.$qry1;
				}
			}
			// unlikely except in development
			else {
//				echo 'No order record created!';
			}	
		}
		// error with qry - unlikely except in developement
		else {
//			echo 'Error executing order query'.$qry;
		} 
	}


	// this function is called from the create order function and sends a confirmation email when an order has been placed.
	private function sendConfirmationEmail($id) {
		$order = $this->getOrders($id);
		$items = $this->getOrderItems($id);
		extract($order[0]);
		$customer = $this->collectCustomer($custID);
		$deposit = ($orderTotalValue / 100) * $_SESSION['deposit'];

		// table heading 	
		$html = '<table style="border: solid 1px #465B8C" cellpadding="8">'."\n";
		$html .= '<tr>'."\n";
		$html .= '<th>Qty</th>'."\n";
		$html .= '<th>Code</th>'."\n";
		$html .= '<th>Title</th>'."\n";
		$html .= '<th>Artist</th>'."\n";
		$html .= '<th>Format</th>'."\n";
		$html .= '<th>Size</th>'."\n";
		$html .= '<th>Unit Price</th>'."\n";
		$html .= '<th>Subtotal</th>'."\n";
		$html .= '</tr>'."\n";
		foreach ($items as $item) {
			extract($item);
			$art = $this->getProductByID($artID, true);
			extract($art[0]);
			$subtotal = $sizePrice * $orderProdQty;
			$html .= '<tr>'."\n";
			$html .= '<td>'.$orderProdQty.'</td>'."\n";
			$html .= '<td>'.$APCode.'</td>'."\n";
			$html .= '<td><a href="http://www.artprints.kiwi.nz/index.php?page=product&id='.$artID.'" title="view">\''.$artName.'\'</a></td>'."\n";
			$html .= '<td>'.$artistName.'</td>'."\n";
			$html .= '<td>'.$artType.'</td>'."\n";
			$html .= '<td>'.$sizeWidth.' x '.$sizeHeight.'mm</td>'."\n";
			$html .= '<td>'.sprintf("%01.2f", $sizePrice).'</td>'."\n";
			$html .= '<td>'.sprintf("%01.2f", $subtotal).'</td>'."\n";
			$html .= '</tr>'."\n";
		}
		$html .= '</table>'."\n";

		$to = $_SESSION['email'];
		$subject = 'Confirmation of your Artprints Order';
		$headers = 'CC: nzartprints@gmail.com'."\n";
		$headers .= 'BCC: niki@afas.co.nz'."\n";
		$headers .= 'From: nzartprints@gmail.com'."\n";
		$headers .= 'MIME-Version: 1.0'."\n";
		$headers .= 'Content-Type: text/html; charset=ISO-8859-1'."\n";
		$message = '<html"><body>';
		$message .= '<img src="http://www.artprints.kiwi.nz/images/logo_desktop.jpg" alt="Artprints: Publishers and Distributors" />';
		$message .= '<h4>Hi '.$_SESSION['name'].',</h4>'."\n";
		$message .= '<p>Thank you for your Artprints order for '.$_SESSION['company'].'. Before your order is dispatched, we required a '.$_SESSION['deposit'].'% desposit. The remaining balance of your order is due by the 20th of next month.  You can log in to your account and view your order status/details anytime on <a href="http://www.artprints.kiwi.nz/index.php?page=account">\'your account\' page.</a> And of course, we\'re only an email away if you\'d like to get in <a href="http://www.artprints.kiwi.nz/index.php?page=contact">contact </a>with us regarding your order.</p>';
		$message .= '<h2>Tax Invoice</h2>'."\n";
		
		// table for supplier, gst, order numb and date issued
		$message .= '<table>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Supplier:</strong></td>'."\n";
				$message .= '<td>Artprints Publishers &amp; Distributors</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>GST:</strong></td>'."\n";
				$message .= '<td>84-030-244</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Invoice/Order #:</strong></td>'."\n";
				$message .= '<td>'.$id.'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Issued:</strong></td>'."\n";
				$message .= '<td>'.date('j M y').'</td>'."\n";
			$message .= '</tr>'."\n";
		$message .= '</table>'."\n";
		
		// order details
		$message .= '<h3>Your Details:</h3>'."\n";
		$message .= '<table>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Company:</strong></td>'."\n";
				$message .= '<td>'.$customer['custCompany'].'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Date Ordered:</strong></td>'."\n";
				$message .= '<td>'.date('j M y', $orderDate).'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Purchase Order #:</strong></td>'."\n";
				$message .= '<td>'.$orderPurcNum.'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Order Notes:</strong></td>'."\n";
				$message .= '<td>'.$orderNotes.'</td>'."\n";
			$message .= '</tr>'."\n";
		$message .= '</table>'."\n";
		
		// deliver and billing addresses
		$message .= '<table style="margin-top: 10px;">'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td style="width: 200px;"><strong>Deliver to:</strong></td>'."\n";
				$message .= '<td style="width: 200px;"><strong>Billing to:</strong></td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td>'.$orderDelFirstName.' '.$orderDelLastName.'</td>'."\n";
				$message .= '<td>'.$orderBillFirstName.' '.$orderBillLastName.'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td>'.$orderDelAddress.'</td>'."\n";
				$message .= '<td>'.$orderBillAddress.'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td>'.$orderDelSuburb.'</td>'."\n";
				$message .= '<td>'.$orderBillSuburb.'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td>'.$orderDelCity.'</td>'."\n";
				$message .= '<td>'.$orderBillCity.'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td>'.$orderDelPostcode.'</td>'."\n";
				$message .= '<td>'.$orderBillPostcode.'</td>'."\n";
			$message .= '</tr>'."\n";
		$message .= '</table>'."\n";

		// order details
		$message .= '<h3>Your Order:</h3>'."\n";
		$message .= $html;

		// table for payment details
		$message .= '<table style="margin-top: 10px;">'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Total:</strong></td>'."\n";
				$message .= '<td>$'.sprintf("%01.2f", $orderValue).' NZD</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Shipping &amp; Handling:</strong></td>'."\n";
				$message .= '<td>$'.sprintf("%01.2f", $orderShipValue).' NZD</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>GST component: </strong></td>'."\n";
				$message .= '<td>$'.sprintf("%01.2f", $orderGSTValue).' NZD</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Grand Total:</strong></td>'."\n";
				$message .= '<td>$'.sprintf("%01.2f", $orderTotalValue).' NZD</td>'."\n";
			$message .= '</tr>'."\n";
			
			$message .= '<tr style="color: red;">'."\n";
				$message .= '<td><strong>Deposit Due Immediately:</strong></td>'."\n";
				$message .= '<td> $'.sprintf("%01.2f", $deposit).' NZD</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Artprints Bank Account:</strong></td>'."\n";
				$message .= '<td>12-345678910-00</td>'."\n";
			$message .= '</tr>'."\n";
		$message .= '</table>'."\n";
		$message .= '<h3>Thanks for shopping at  <a href="http://www.artprints.kiwi.nz/index.php?">Artprints!</a></h3>'."\n";
		$message .= '<img src="http://www.artprints.kiwi.nz/images/banners/mobile_artprints_team.jpg" />'."\n";
		$message .= "</body></html>";
		// The email worked
		if (mail($to, $subject, $message, $headers)) {
		}
	}


	/* ---------------------------------------------------------------------- */
	/* ------------------------------ orders  ------------------------------- */
	/* ---------------------------------------------------------------------- */

	
	// this function gets all the orders particular customer if $id provided
	// or the order matching a provided $orderID
	public function getPastOrders($id='', $orderID='') {
		if($id != '') {
			$qry = "SELECT orderID, orderPurcNum, orderDelFirstName, orderDelLastName, orderDelAddress, orderDelSuburb, orderDelCity, orderDelPostcode, orderDelCountry, orderBillFirstName, orderBillLastName, orderBillAddress, orderBillSuburb, orderBillCity, orderBillPostcode, orderBillCountry, orderReceiptNum, orderValue, orderShipValue, orderGSTValue, orderTotalValue, orderPaymentRec, orderBalance, UNIX_TIMESTAMP(orderDate) AS orderDate, orderStatus, orderDelStatus, UNIX_TIMESTAMP(orderDispatchDate) AS orderDispatchDate, orderCourierNum, orderInvoiceRef, orderQty, orderNotes FROM orders WHERE custID = $id AND orderStatus = 'approved' ORDER BY orderDispatchDate DESC";
		}
		else {
			$qry = "SELECT orderID, orderPurcNum, orderDelFirstName, orderDelLastName, orderDelAddress, orderDelSuburb, orderDelCity, orderDelPostcode, orderDelCountry, orderBillFirstName, orderBillLastName, orderBillAddress, orderBillSuburb, orderBillCity, orderBillPostcode, orderBillCountry, orderReceiptNum, orderValue, orderShipValue, orderGSTValue, orderTotalValue, orderPaymentRec, orderBalance, UNIX_TIMESTAMP(orderDate) AS orderDate, orderStatus, orderDelStatus, UNIX_TIMESTAMP(orderDispatchDate) AS orderDispatchDate, orderCourierNum, orderInvoiceRef, orderQty, orderNotes FROM orders WHERE orderID = $orderID AND orderStatus = 'approved' ORDER BY orderDispatchDate DESC";
		}
		$rs = $this->db->query($qry);
		if($rs) {
			// if at least 1 record retrieved
			if($rs->num_rows > 0) {   
				$orders = array();
				// numeric indexed array
				while($row = $rs->fetch_assoc()) {
					$orders[] = $row;		
				}
			}
			 // no rows retrieved (the user has not made a purchase while logged in)
			else { 
				$orders = 'no orders'; 
			}
		}
		 // error executing query (development only)
		else { 
	//		echo "Error with getPrevious Order query"; 
		}	
		return $orders;
	}


	// this function gets orders by
	// * delivery status 	(ie pending)
	// * money owing
	// * order id
	// * month the order was placed
	// returns array $orders if successful or $orders = 'no orders' if no matches
	public function getOrders($type) {
		// gets orders awaiting dispatch
		if ($type == 'pending' || $type == 'delayed') {
			$qry = "SELECT orderID, custID, orderPurcNum, orderDelFirstName, orderDelLastName, orderDelAddress, orderDelSuburb, orderDelCity, orderDelPostcode, orderDelCountry, orderBillFirstName, orderBillLastName, orderBillAddress, orderBillSuburb, orderBillCity, orderBillPostcode, orderBillCountry, orderReceiptNum, orderValue, orderShipValue, orderGSTValue, orderTotalValue, orderPaymentRec, orderBalance, UNIX_TIMESTAMP(orderDate) AS orderDate, orderStatus, orderDelStatus, UNIX_TIMESTAMP(orderDispatchDate) AS orderDispatchDate, orderInvoiceRef, orderCourierNum, orderQty, orderNotes FROM orders WHERE orderDelStatus = 'pending' OR orderDelStatus = 'delayed' AND orderStatus = 'approved' ORDER BY orderDispatchDate DESC";
		}
		// gets orders with money owing
		else if ($type == 'money'){
			$qry = "SELECT orderID, custID, orderPurcNum, orderDelFirstName, orderDelLastName, orderDelAddress, orderDelSuburb, orderDelCity, orderDelPostcode, orderDelCountry, orderBillFirstName, orderBillLastName, orderBillAddress, orderBillSuburb, orderBillCity, orderBillPostcode, orderBillCountry, orderReceiptNum, orderValue, orderShipValue, orderGSTValue, orderTotalValue, orderPaymentRec, orderBalance, UNIX_TIMESTAMP(orderDate) AS orderDate, orderStatus, orderDelStatus, UNIX_TIMESTAMP(orderDispatchDate) AS orderDispatchDate, orderInvoiceRef, orderCourierNum, orderQty, orderNotes FROM orders WHERE orderBalance > 0 AND orderStatus = 'approved' ORDER BY orderDispatchDate DESC";
		}
		// gets an order by id
		else if (is_numeric($type)){
			$qry = "SELECT orderID, custID, orderPurcNum, orderDelFirstName, orderDelLastName, orderDelAddress, orderDelSuburb, orderDelCity, orderDelPostcode, orderDelCountry, orderBillFirstName, orderBillLastName, orderBillAddress, orderBillSuburb, orderBillCity, orderBillPostcode, orderBillCountry, orderReceiptNum, orderValue, orderShipValue, orderGSTValue, orderTotalValue, orderPaymentRec, orderBalance, UNIX_TIMESTAMP(orderDate) AS orderDate, orderStatus, orderDelStatus, UNIX_TIMESTAMP(orderDispatchDate) AS orderDispatchDate, orderInvoiceRef, orderCourierNum, orderQty, orderNotes FROM orders WHERE orderID = $type ORDER BY orderDispatchDate DESC";
		}
		else if ($type == 'month') {
			$month = $_POST['month'] + 1;
			sprintf("%01.", $month);
			if ($month < 10) {
				$month = '0'.$month;
			}
			$year = $_POST['year'];
			$date = $year.'-'.$month;
			$qry = "SELECT orderID, custID, orderPurcNum, orderDelFirstName, orderDelLastName, orderDelAddress, orderDelSuburb, orderDelCity, orderDelPostcode, orderDelCountry, orderBillFirstName, orderBillLastName, orderBillAddress, orderBillSuburb, orderBillCity, orderBillPostcode, orderBillCountry, orderReceiptNum, orderValue, orderShipValue, orderGSTValue, orderTotalValue, orderPaymentRec, orderBalance, UNIX_TIMESTAMP(orderDate) AS orderDate, orderStatus, orderDelStatus, UNIX_TIMESTAMP(orderDispatchDate) AS orderDispatchDate, orderInvoiceRef, orderQty, orderCourierNum, orderNotes FROM orders WHERE orderDate LIKE '$date%' ORDER BY orderDispatchDate DESC";
		}	
		$rs = $this->db->query($qry);
		if($rs) {
			// if at least 1 record retrieved
			if($rs->num_rows > 0) {   
				$orders = array();
				// numeric indexed array
				while($row = $rs->fetch_assoc()) {
					$orders[] = $row;		
				}
			}
			 // no rows retrieved (the user has not made a purchase while logged in)
			else { 
				$orders = 'no orders'; 
			}
		}
		 // error executing query (development only)
		else { 
//			echo "Error with get Order query"; 
		}	
		return $orders;
	}


	// gets every item in a placed order by order id and returns array $items
	public function getOrderItems($id) {
		$qry = "SELECT ordArtID, artID, APCode, sizeWidth, sizeHeight, sizePrice, orderProdQty, artType, orderedArtStatus, dateDispatched, artistID, sizeID, royaltyAmount, royaltyPaymentID from orderedArt WHERE orderID = $id";
		$rs = $this->db->query($qry);
		if($rs) {
			// if at least 1 record retrieved
			if($rs->num_rows > 0) {   
				$items = array();
				// numeric indexed array
				while($row = $rs->fetch_assoc()) {
					$items[] = $row;		
				}
			}
			 // no rows retrieved (the user has not made a purchase while logged in)
			else { 
				$items = 'no orders'; 
			}
		}
		 // error executing query (development only)
		else { 
//			echo "Error with getOrderItems query" . $qry; 
		}	
		return $items;
	}


	/* ---------------------------------------------------------------------- */
	/* --------------------------- update orders ---------------------------- */
	/* ---------------------------------------------------------------------- */


	// updates details for an order including;
	// allows admin to change shipping amount, delivery status, notes, courier ref, amount paid
	// calculates total order value, GST and balance to pay
	// returns true if update successful
	public function updatePayment() {
		if($_SESSION['access'] == 'admin') {
			extract($_POST);
			if (!get_magic_quotes_gpc()) {
				$notes = $this->db->real_escape_string($notes);
			}
			$orderValue = str_replace ('$' ,'' , $order_value);
			$payment = str_replace ('$' ,'' , $payment);
			$shipping = str_replace ('$' ,'' , $shipping);
			$orderGST = ($shipping + $orderValue) / 100;
			$orderGST = $orderGST * 15;
			$orderTotal = $shipping + $orderValue + $orderGST; 
			$balance = $orderTotal - $payment;
			if ($delivery_status == 'dispatched') {
				$delStatus = $this->getPastOrders('', $order_id);
				// if order is being updated from pending etc to dispatched, sends a dispatch notification email
				if ($delStatus[0]['orderDelStatus'] != 'dispatched') {
					// delivery status has changed to dispatched so send email
					$this->sendDispatchEmail($order_id);
					// foreach orderedArt for this order update orderedArtStatus to dispatched and add date
					$qry1 = "UPDATE orderedArt SET orderedArtStatus = 'dispatched', dateDispatched = NULL WHERE orderID = $order_id";
				}
				$qry = "UPDATE orders SET orderPaymentRec = $payment, orderBalance = $balance, orderDelStatus = '$delivery_status', orderCourierNum = '$courier', orderDispatchDate = NULL, orderNotes = '$notes', orderShipValue = $shipping, orderGSTValue = $orderGST, orderTotalValue = $orderTotal WHERE orderID = $order_id";
			}
			else {
				$qry = "UPDATE orders SET orderPaymentRec = $payment, orderBalance = $balance, orderDelStatus = '$delivery_status', orderCourierNum = '$courier', orderNotes = '$notes', orderShipValue = $shipping, orderGSTValue = $orderGST, orderTotalValue = $orderTotal WHERE orderID = $order_id";
			}
			$rs = $this->db->query($qry);
			if (!$rs) {
//				echo 'Query error! '. $qry; 
			}
			else {
				if ($this->db->affected_rows > 0) {
					if (isset($qry1)) {
						$rs = $this->db->query($qry1);
					}
					return true;
				}
				else {
					return false;
				}
			}
		}
	}


	// this function is called from the update payment function and sends a email when an order has been dispatched.
	private function sendDispatchEmail($id) {
		$order = $this->getOrders($id);
		$items = $this->getOrderItems($id);
		extract($order[0]);
		$cust = $this->collectCustomer($custID);
		extract($cust);
		$to = $custEmail;
		$subject = 'Your Artprints Order Has Been Dispatched!';
		$headers = 'CC: nzartprints@gmail.com'."\n";
		$headers .= 'BCC: niki@afas.co.nz'."\n";
		$headers .= 'From: nzartprints@gmail.com'."\n";
		$headers .= 'MIME-Version: 1.0'."\n";
		$headers .= 'Content-Type: text/html; charset=ISO-8859-1'."\n";
		$message = '<html><body>';
		$message .= '<img src="http://www.artprints.kiwi.nz/images/logo_desktop.jpg" alt="Artprints: Publishers and Distributors" />'."\n";
		$message .= '<h4>Hi '.$orderDelFirstName.' '.$orderDelLastName.',</h4>'."\n";
		$message .= '<p>Your order for '.$custCompany.' has just been dispatched.</p>'."\n";
		if ($_POST['courier'] != '') {
			$message .= '<p>You can trace your order using the Courier Tracking # '.$_POST['courier'].'.</p>'."\n";
		}
		$message .= '<p>We\'re only an email away if you\'d like to get in <a href="http://www.artprints.kiwi.nz/index.php?page=contact">contact </a>with us regarding your order.</p>'."\n";
		$message .= '<p>Thanks for shopping at <a href="http://www.artprints.kiwi.nz/index.php?">artprints.kiwi.nz.</a></p>';

		// table for supplier, gst
		$message .= '<table>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Supplier:</strong></td>'."\n";
				$message .= '<td>Artprints Publishers &amp; Distributors</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>GST:</strong></td>'."\n";
				$message .= '<td>84-030-244</td>'."\n";
			$message .= '</tr>'."\n";	
		$message .= '</table>'."\n";
		
		// order details
		$message .= '<h3>Your Details:</h3>'."\n";
		$message .= '<table>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Company:</strong></td>'."\n";
				$message .= '<td>'.$custCompany.'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Date Ordered:</strong></td>'."\n";
				$message .= '<td>'.date('j M y', $orderDate).'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Purchase Order #:</strong></td>'."\n";
				$message .= '<td>'.$orderPurcNum.'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Invoice/Order #:</strong></td>'."\n";
				$message .= '<td>'.$id.'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Dispatched:</strong></td>'."\n";
				$message .= '<td>'.date('j M y').'</td>'."\n";
			$message .= '</tr>'."\n";	
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Courier Tracking #:</strong></td>'."\n";
				$message .= '<td>'.$_POST['courier'].'</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Order Notes:</strong></td>'."\n";
				$message .= '<td>'.$_POST['notes'].'</td>'."\n";
			$message .= '</tr>'."\n";
		$message .= '</table>'."\n";
		
		$message .= '<h3>Order Details:</h3>'."\n";
		$html = '<table style="border: solid 1px #465B8C" cellpadding="8">'."\n";
		$html .= '<tr>'."\n";
		$html .= '<th>Qty</th>'."\n";
		$html .= '<th>Code</th>'."\n";
		$html .= '<th>Title</th>'."\n";
		$html .= '<th>Artist</th>'."\n";
		$html .= '<th>Format</th>'."\n";
		$html .= '<th>Size</th>'."\n";
		$html .= '<th>Unit Price</th>'."\n";
		$html .= '<th>Subtotal</th>'."\n";
		$html .= '</tr>'."\n";
		foreach ($items as $item) {
			extract($item);
			$art = $this->getProductByID($artID, true);
			extract($art[0]);
			$subtotal = $sizePrice * $orderProdQty;
			$html .= '<tr>'."\n";
			$html .= '<td>'.$orderProdQty.'</td>'."\n";
			$html .= '<td>'.$APCode.'</td>'."\n";
			$html .= '<td><a href="http://www.julia.larsen.natcoll.net.nz/_Assignments/Artprints/www/index.php?page=product&id='.$artID.'" title="view">\''.$artName.'\'</a></td>'."\n";
			$html .= '<td>'.$artistName.'</td>'."\n";
			$html .= '<td>'.$artType.'</td>'."\n";
			$html .= '<td>'.$sizeWidth.' x '.$sizeHeight.'mm</td>'."\n";
			$html .= '<td>'.sprintf("%01.2f", $sizePrice).'</td>'."\n";
			$html .= '<td>'.sprintf("%01.2f", $subtotal).'</td>'."\n";
			$html .= '</tr>'."\n";
		}
		$html .= '</table>'."\n";
		$message .= $html;	
		
		$balance = ($orderTotalValue - $_POST['payment']);
		$currentMonth = date('M y');
		$nextMonth = strtotime(date("M y", strtotime($currentMonth)) . "+1 month");
		$nextMonth = date('M y', $nextMonth);

		// table for payment details
		$message .= '<table style="margin-top: 10px;">'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Total:</strong></td>'."\n";
				$message .= '<td>$'.sprintf("%01.2f", $orderValue).' NZD</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Shipping &amp; Handling:</strong></td>'."\n";
				$message .= '<td>$'.sprintf("%01.2f", $orderShipValue).' NZD</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>GST component: </strong></td>'."\n";
				$message .= '<td>$'.sprintf("%01.2f", $orderGSTValue).' NZD</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Grand Total:</strong></td>'."\n";
				$message .= '<td>$'.sprintf("%01.2f", $orderTotalValue).' NZD</td>'."\n";
			$message .= '</tr>'."\n";
			
			$message .= '<tr style="color: red;">'."\n";
				$message .= '<td><strong>Balance Due by 20 '.$nextMonth.':</strong></td>'."\n";
				$message .= '<td> $'.sprintf("%01.2f", $balance).' NZD</td>'."\n";
			$message .= '</tr>'."\n";
			$message .= '<tr>'."\n";
				$message .= '<td><strong>Artprints Bank Account:</strong></td>'."\n";
				$message .= '<td>12-345678910-00</td>'."\n";
			$message .= '</tr>'."\n";
		$message .= '</table>'."\n";
		$message .= '<h3>Thanks for shopping at Artprints!</h3>'."\n";
		$message .= '<img src="http://www.artprints.kiwi.nz/images/banners/mobile_artprints_team.jpg" />'."\n";
		$message .= "</body></html>";
		// The email worked
		if (mail($to, $subject, $message, $headers)) {
		}
	}


	/* ---------------------------------------------------------------------- */
	/* ------------------------------ royalties ----------------------------- */
	/* ---------------------------------------------------------------------- */

	// collects are to calculate royalties by
	// artistID if $id provided
	// or by month if $month provided
	// or all dispatched if neither $id or $month provided
	// returns array $items
	public function getDispatchedArt($id='', $month='') {
		if (is_numeric($id)) {
			$qry = "SELECT ordArtID, orderID, artID, APCode, sizeWidth, sizeHeight, sizePrice, orderProdQty, artType, orderedArtStatus, UNIX_TIMESTAMP(dateDispatched) AS dateDispatched, artistID, sizeID, royaltyAmount, royaltyPaymentID from orderedArt WHERE orderedArtStatus = 'dispatched' AND artistID = $id";
		}
		else if ($month != '') {
			$month = $_POST['month'] + 1;
			sprintf("%01.", $month);
			if ($month < 10) {
				$month = '0'.$month;
			}
			$year = $_POST['year'];
			$date = $year.'-'.$month;
			$qry = "SELECT ordArtID, orderID, artID, APCode, sizeWidth, sizeHeight, sizePrice, orderProdQty, artType, orderedArtStatus, UNIX_TIMESTAMP(dateDispatched) AS dateDispatched, artistID, sizeID, royaltyAmount, royaltyPaymentID FROM orderedArt WHERE dateDispatched LIKE '$date%'";
		}	
		else {
			$qry = "SELECT ordArtID, orderID, artID, APCode, sizeWidth, sizeHeight, sizePrice, orderProdQty, artType, orderedArtStatus, UNIX_TIMESTAMP(dateDispatched) AS dateDispatched, artistID, sizeID, royaltyAmount, royaltyPaymentID from orderedArt WHERE orderedArtStatus = 'dispatched'";
		}
		$rs = $this->db->query($qry);
		if($rs) {
			// if at least 1 record retrieved
			if($rs->num_rows > 0) {   
				$items = array();
				// numeric indexed array
				while($row = $rs->fetch_assoc()) {
					$items[] = $row;		
				}
			}
			 // no rows retrieved 
			else { 
				$items = 'no dispatched items'; 
			}
		}
		 // error executing query (development only)
		else { 
//			echo "Error with getDispatched Art query" . $qry; 
		}	
		return $items;
	}


	// collects payments made for a particular artist by provided $artistID
	// returns array $payments or 0 if no payments are made
	public function royaltiesPaid($artistID) {
		$qry = "SELECT royaltyPaymentID, artistID, amountPaid, UNIX_TIMESTAMP(datePaid) AS datePaid, reference from royaltyPayments WHERE artistID = $artistID";
		$rs = $this->db->query($qry);
		if($rs) {
			// if at least 1 record retrieved
			if($rs->num_rows > 0) {   
				$payments = array();
				// numeric indexed array
				while($row = $rs->fetch_assoc()) {
					$payments[] = $row;		
				}
			}
			 // no rows retrieved (the user has not made a purchase while logged in)
			else { 
				$payments = '0'; 
			}
		}
		 // error executing query (development only)
		else { 
//			echo "Error with royalties paid query" . $qry; 
		}	
		return $payments;
	}


	// adds a new royalty payment into the database
	// returns true if successful
	public function recordRoyaltyPayment() {
		if (!get_magic_quotes_gpc()) {
			$this->sanitiseInput();
		}
		extract($_POST);
		$payment = str_replace ('$' ,'' , $payment);
		$qry = "INSERT INTO royaltyPayments VALUES (NULL, $artistID, $payment, NULL, '$notes')";	
		$rs = $this->db->query($qry);
		if ($rs) {
			if ($this->db->affected_rows > 0) {
				// successful
				return $result = true;
			}
			// Error executing orderedproducts query (devlopment only)
			else {
//				echo 'Error executing record Royalty Payment'.$qry;
			}
			// unlikely except in development	
		}
		// error with qry - unlikely except in developement
		else {
//			echo 'Error executing record Royalty Payment'.$qry;
		} 
	}
}
?>