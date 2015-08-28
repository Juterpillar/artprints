<?php

include 'classes/dbClass.php';
include 'classes/uploadClass.php';
include 'classes/resizeImageClass.php';


class Model extends Dbase
{
	// this class contains most of the functions for processing
	// it particularly contains functions that work between the dbclass and views
	// this class is separated by different view


	/* ---------------------------------------------------------------------- */
	/* ------------------------- every / any page --------------------------- */
	/* ---------------------------------------------------------------------- */


	public function __construct() {
		// call the constructor function of the Dbase class
		parent::__construct();   
		$validationPages = array('register', 'login', 'checkout', 'adminartist', 'adminartwork', 'order', 'contact', 'account', 'royalty');
		if (in_array($_GET['page'], $validationPages)) {
			include 'classes/validateClass.php';
			$this->validate = new Validate;
		}
	}


	// this function takes a string, divides it into 2 columns evenly by the number of sentences
	// then displays it in the provided id for the div
	// it is used on many pages including home, about, product etc
	public function textTo2Cols($string, $heading='', $divID='') {
		$sentences = explode(".", $string); 
		$numSentences = count($sentences);
		$sentencesPerCol = floor($numSentences / 2);
		$numSentences = $numSentences - 1;
		$col1 = '';
		$col2 = '';
		$i = 0;
		while ($i < $sentencesPerCol) {
			$col1 .= $sentences[$i] . '. ';
			$i++;
		}
		$i = $sentencesPerCol;
		while ($i < $numSentences) {
			$col2 .= $sentences[$i] . '. ';
			$i++;
		}

		// display the content across the 2 columns
		$html = "\t\t\t".'<div id="'.$divID.'">'."\n";
		if($heading) {
			$html .= "\t\t\t".'<h2>'.$heading.'</h2>'."\n";
		}
		$html .= "\t\t\t\t".'<p>'.stripslashes($col1).'</p>'."\n";
		$html .= "\t\t\t\t".'<p>'.stripslashes($col2).'</p>'."\n";
		$html .= "\t\t\t".'</div> <!-- '.$divID.' -->'."\n";
		return $html;
	}


	/* ---------------------------------------------------------------------- */
	/* ----------------------------- catalogue ------------------------------ */
	/* ---------------------------------------------------------------------- */


	public function filterAside($catalogue) {
		$html = "\t\t\t\t".'<aside>'."\n";
		$html .= "\t\t\t\t\t".'<h2 id="filter_heading" class="mobile">filter by <i class="icon-circle-arrow-down"></i></h2>'."\n";
		$html .= "\t\t\t\t\t".'<div id="filters">'."\n";
		$html .= "\t\t\t\t\t\t".'<h3 id="subject">subject <i class="icon-circle-arrow-down"></i></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="subject_options">'."\n";
		$subjects = $this->getSubjects();
		foreach ($subjects as $subject) {
			$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=s&s='.$subject['subjectID'].'"><h5>'.$subject['subjectName'].'</h5></a>'."\n";
		}
		$html .= "\t\t\t\t\t\t".'</div><!-- subject_options -->'."\n";
		$html .= "\t\t\t\t\t\t".'<h3 id="artist">artist <i class="icon-circle-arrow-down"></i></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="artist_options">'."\n";
		$artists = $this->getArtists();
		foreach ($artists as $artist) {
			$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=a&a='.$artist['artistID'].'"><h5>'.$artist['artistName'].'</h5></a>'."\n";
		}
		$html .= "\t\t\t\t\t\t".'</div><!-- artist_options -->'."\n";
		$html .= "\t\t\t\t\t\t".'<h3 id="size">size <i class="icon-circle-arrow-down"></i></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="size_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=z&z=mini"><h5>mini</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=z&z=small"><h5>small</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=z&z=medium"><h5>medium</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=z&z=large"><h5>large</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=z&z=oversize"><h5>oversize</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- size_options -->'."\n";
		$html .= "\t\t\t\t\t\t".'<h3 id="price">price <i class="icon-circle-arrow-down"></i></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="price_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=p&p=20"><h5>under $20</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=p&p=50"><h5>$20 - $50</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=p&p=100"><h5>$50 - $100</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=p&p=150"><h5>$100 - $150</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=p&p=200"><h5>$150 - $200</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=p&p=250"><h5>$200 - $250</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=p&p=251"><h5>over $251</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- price_options -->'."\n";
		$html .= "\t\t\t\t\t\t".'<h3 id="orientation">orientation <i class="icon-circle-arrow-down"></i></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="orientation_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=o&o=tall"><h5>tall</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=o&o=wide"><h5>wide</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$catalogue.'&f=o&o=square"><h5>square</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- orientation_options -->'."\n";
		$html .= "\t\t\t\t\t".'</div><!-- filters -->'."\n";
		$html .= "\t\t\t\t".'</aside>'."\n";
		return $html;
	}


	// displays provided array $products on catalgoue/search pages
	// if $canvas (it is a canvas product) don't add class box shadow
	// returns html for all items displayed
	public function displayProducts($products, $canvas='', $width='') {
		if ($width != '') {
			$style = ' style="width:'.$width.'px"';
		}
		$html = "\t\t\t\t\t".'<div class="icon_nav"'.$style.'>'."\n";
		$shadow = '';

		foreach ($products as $product) {
			// for each product, collect the sizes available and assign the RRP to the lowest price for display
			$shadow = '';
			if ($canvas == '' && $product['artType'] != 'canvas') {
				$shadow = ' class="image_shadow"';
			}
			if (is_array($product) && $product['artHiddenDate'] == '' && $product['artistHidden'] =='') {
				$product['price'] = array();
				$product[] = $this->getSizePrices($product['artID']);
				if (is_array($product[0])) {
					foreach ($product[0] as $size) {
						if(isset($_SESSION['custID'])) {
							$product['price'][] = $size['sizeWSP'];
							$priceType = 'WSP';
						}
						else {
							$product['price'][] = $size['sizeRRP'];
							$priceType = 'RRP';
						}
					}
					$product['price'] = min($product['price']);
					$html .= "\t\t\t\t\t\t".'<a href="index.php?page=product&amp;id='.$product['artID'].'"><figure class="product_set">'."\n";
					$html .= "\t\t\t\t\t\t\t".'<img'.$shadow.' src="images/thumbs/'.$product['artImage'].'" title="view details" alt="'.$product['artName'].'" />'."\n";
					$html .= "\t\t\t\t\t\t\t".'<figcaption>'."\n";
					$html .= "\t\t\t\t\t\t\t\t".'<h4>'.$product['artName'].'</h4>'."\n";
					$html .= "\t\t\t\t\t\t\t\t".'<h6>by '.$product['artistName'].'</h6>'."\n";
					$html .= "\t\t\t\t\t\t\t\t".'<h4>From $'.$product['price'].' '.$priceType.'</h4>'."\n";
					$html .= "\t\t\t\t\t\t\t".'</figcaption>'."\n";
					$html .= "\t\t\t\t\t\t".'</figure></a> <!-- icon_set -->'."\n";
				}
			}
		}
		$html .= "\t\t\t\t\t".'</div> <!-- icon_nav -->'."\n";
		return $html;
	}


	// checks if an artId has sizes before displaying
	// returns the array $product if has sizes and nothing (false) if not
	public function checkArtHasSize($product) {
		extract($product);
		$size = $this->getSizePrices($artID);
		if (is_array($size)) {
			return $product;
		}
	}


	// This function dipslays the links to navigate between pages created by the batch limit
	// $count 			= number of items in total
	// $prodPerPage 	= number of items to be displayed per page
	// $url 			= is the url for the catalogue type (eg framed/canvas/prints)
	// $bottom 			= is set to true if showing the page links underneath the products displayed
	// $type 			= either framed/canvas/prints
	// returns the pagination for the products
	public function displayPageLinks($count, $prodPerPage, $catNum, $url, $bottom='', $type='') {	
		// if pagination at the bottom of the page add the div
		$html = '';
		if ($bottom) {
			$html .= "\t\t\t\t\t".'<div id="bottom_pagination">'."\n";
		}
		$end = $prodPerPage * $catNum;

		$start = ($end - $prodPerPage) + 1;

		// if the total items is less than the number of items displayed on each page
		if ($count < $prodPerPage || $count == $prodPerPage) {
			$html .= "\t\t\t\t\t".'<h6>showing results '.$start.'-'.$count.' of '.$count.'</h6>'."\n";
			if ($bottom) {
				$html .= "\t\t\t\t\t".'</div><!-- botton_pagination -->'."\n";
			}
			return $html;
		}
		// if on the last page of the pagination
		else if ($end == $count || $end > $count){
			$html .= "\t\t\t\t\t".'<h6>showing results '.$start.'-'.$count.' of '.$count.'</h6>'."\n";
		}
		else {
			$html .= "\t\t\t\t\t".'<h6>showng results '.$start.'-'.$end.' of '.$count.'</h6>'."\n";
		}
		// mobile pagination
		if ($type == 'mobile') {
			$html .= "\t\t\t\t\t\t".'<h4 class="pagination"><a href="index.php?page='.$url.'&amp;catNum='.($catNum - 1).'"><i class="icon-circle-arrow-left"></i> previous  </a> ';
			$html .= '<span class="mobile"></span><a href="index.php?page='.$url.'&amp;catNum='.($catNum + 1).'"> next <i class="icon-circle-arrow-right"></i></a></h4>'."\n";

		}
		// non-mobile pagination
		else {
			if ($catNum == 1) {
				$html .= "\t\t\t\t\t".'<h4 class="pagination">';
			}
			// there are pages before this one so show 'previous' link
			else {
				$html .= "\t\t\t\t\t\t".'<h4 class="pagination"><a href="index.php?page='.$url.'&amp;catNum='.($catNum - 1).'"><i class="icon-circle-arrow-left"></i> previous  </a>'."\n";
			}	
			// if the amount of products divides evenly into the limit of products shown per page
			if ($count % $prodPerPage == 0) {
				$totalPages = $count / $prodPerPage;
			}
			// products not evenly divisible by the limit per page so round down then add and extra page.
			else {
				$totalPages = floor($count / $prodPerPage) + 1;
			}
			// check which page is current and add class "current_page"
			for ($i = 1; $i <= $totalPages; $i++) {
				if ($i == $catNum) {
					$span = '<span class="current_page no_mobile">'.$i.'</span>'."\n";
				}
				else {
					$span = '<span class="no_mobile">'.$i.'</span>';
				}
				$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$url.'&amp;catNum='.$i.'">'.$span.'</a>'."\n";
				if ($i != $totalPages) {
				}
			}
			if ($catNum != $totalPages) {
				$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page='.$url.'&amp;catNum='.($catNum + 1).'">next <i class="icon-circle-arrow-right"></i></a></h4>'."\n";
			}
			else {
				$html .= '</h4>'."\n";
			}
			if ($bottom) {
				$html .= "\t\t\t\t\t".'</div><!-- botton_pagination -->'."\n";
			}
			return $html;
		}
	}


	// this function is used on the catalogue pages to determine the max/min for a price filter
	// returns the min and max price in array $price for the value provided
	public function getMinMax($value) {
		switch ($value) {
	    	// under $20
	    	case '20':
	        	$min = -1;
	        	$max = 21;
	        break;
		    // $20 - $50
		    case '50':
		    	$min = 19;
	        	$max = 51;
		    break;
		    // $50 - $100
		    case '100':
		    	$min = 49;
	        	$max = 101;
		     break;
		    // $100 - $150
		    case '150':
		    	$min = 99;
	        	$max = 151;
		     break;
		    // $150 - $200
		    case '200':
		    	$min = 149;
	        	$max = 201;
 			break;
		    // $200 - $250
		    case '250':
		    	$min = 199;
	        	$max = 249;
		     break;
		    // $over $250
		    case '251':
		     	$min = 249;
	        	$max = 5000;
		    }
		    $price['min'] = $min;
		    $price['max'] = $max;
		    return $price;
	}


	// this function is used on the catalogue pages to determine the min/max size (height plus width in mm)
	// for a particular size label (value)
	// returns the min and max size in array $size for the value provided
	public function getSize($value) {
		switch ($value) {
	    	// mini
	    	case 'mini':
	        	$min = 0;
	        	$max = 351;
	        break;
		    // small
		    case 'small':
		    	$min = 350;
	        	$max = 751;
		    break;
		    // medium
		    case 'medium':
		    	$min = 750;
	        	$max = 1100;
		     break;
		    // large 
		    case 'large':
		    	$min = 1101;
	        	$max = 1601;
		     break;
		    // oversize
		    case 'oversize':
		    	$min = 1600;
	        	$max = 10000;
 			break;
 		}
		$size['min'] = $min;
		$size['max'] = $max;
		return $size;
	}


	/* ---------------------------------------------------------------------- */
	/* ------------------------------ register ------------------------------ */
	/* ---------------------------------------------------------------------- */


	// this function validates the info from the register form and returns $result['ok'] if no errors
	public function validateRegisterForm() {
		extract($_POST);
		$result['company'] = $this->validate->checkName($company);
		$result['first_name'] = $this->validate->checkName($first_name);
		$result['last_name'] = $this->validate->checkName($last_name);
		$result['phone'] = $this->validate->checkPhone($phone);
		$result['email'] = $this->validate->checkEmail($email);
		$result['billing_address'] = $this->validate->checkRequired($billing_address);
		$result['billing_suburb'] = $this->validate->checkRequired($billing_suburb);
		$result['billing_city'] = $this->validate->checkRequired($billing_city);
		$result['billing_postcode'] = $this->validate->checkNumeric($billing_postcode);
		$result['delivery_address'] = $this->validate->checkRequired($delivery_address);
		$result['delivery_suburb'] = $this->validate->checkRequired($delivery_suburb);
		$result['delivery_city'] = $this->validate->checkRequired($delivery_city);
		$result['delivery_postcode'] = $this->validate->checkNumeric($delivery_postcode);
		$result['island'] = $this->validate->checkSelect($island);
		$result['password'] = $this->validate->checkPassword($password);
		$result['password1'] = $this->validate->checkPasswordMatch($password, $password1);		
		$result['agree'] = $this->validate->checkChecked($agree);
		$result['ok'] = $this->validate->checkErrorMessages($result);
		return $result;
	}


	/* ---------------------------------------------------------------------- */
	/* -------------------------- login / logout ---------------------------- */
	/* ---------------------------------------------------------------------- */


	// validates login entries
	// if successful then sends email address and password to function getUser in dbClass to find a match on the db
	// if successfully finds a match, sets session variables
	// returns $result containing either array of validation errors or array matching db info or 'incorrect' if no match found
	public function processLogin() {
		extract($_POST);
		$result['email'] = $this->validate->checkEmail($email);
		$result['password'] = $this->validate->checkPassword($password);		
		$result['ok'] = $this->validate->checkErrorMessages($result);
		// check if validation ok
		if ($result['ok']) {
			// process login
			$result = $this->getCustomer($email, $password);
			if (is_array($result)) {
				if ($result['custStatus'] == 'inactive') {
					$result = 'inactive';
				}
				else if ($result['custStatus'] == 'frozen') {
					$result = 'frozen';
				}
				else {
				// successfully logged in so save as session variables
					$_SESSION['custID'] = $result['custID'];
					$_SESSION['first'] = $result['custFirstName'];
					$_SESSION['last'] = $result['custLastName'];
					$_SESSION['name'] = $result['custFirstName'].' '.$result['custLastName'];
					$_SESSION['company'] = $result['custCompany'];
					$_SESSION['access'] = $result['custAccess'];
					$_SESSION['delAddress'] = $result['custDelAddress'];
					$_SESSION['delSuburb'] = $result['custDelSuburb'];
					$_SESSION['delCity'] = $result['custDelCity'];
					$_SESSION['delPostcode'] = $result['custDelPostcode'];
					$_SESSION['delCountry'] = $result['custDelCountry'];
					$_SESSION['phone'] = $result['custPhone'];
					$_SESSION['email'] = $result['custEmail'];	
					$_SESSION['billAddress'] = $result['custBillAddress'];
					$_SESSION['billSuburb'] = $result['custBillSuburb'];
					$_SESSION['billCity'] = $result['custBillCity'];
					$_SESSION['billPostcode'] = $result['custBillPostcode'];
					$_SESSION['billCountry'] = $result['custBillCountry'];
					$_SESSION['deposit'] = $result['custDeposit'];	
					$_SESSION['status'] = $result['custStatus'];	
					$_SESSION['island1'] = $result['custDelIsland'];	
					$_SESSION['notes'] = $result['custNotes'];	
					$_SESSION['credit'] = $result['custCreditLimit'];	
				}
			}
		}
		return $result;
	}


	// logs user out by unsetting session variables
	public function logout() {
		unset($_SESSION['custID']);		
		unset($_SESSION['name']);
		unset($_SESSION['company']);
		unset($_SESSION['access']);
		unset($_SESSION['delAddress']);
		unset($_SESSION['delSuburb']);
		unset($_SESSION['delCity']);
		unset($_SESSION['delPostocde']);
		unset($_SESSION['delCountry']);
		unset($_SESSION['phone']);
		unset($_SESSION['email']);
		unset($_SESSION['billAddress']);
		unset($_SESSION['billSuburb']);
		unset($_SESSION['billCity']);
		unset($_SESSION['billPostcode']);
		unset($_SESSION['billCountry']);
		unset($_SESSION['deposit']);
		unset($_SESSION['status']);
		unset($_SESSION['island1']);
		unset($_SESSION['notes']);
		unset($_SESSION['credit']);
		session_destroy();
	}  


	// calculates cust limit, balance and credit available
	// returns amount of credit available or 0 if credit limit is reached/exceeded
	// this is displayed on the account page, catalogue pages and product pages if exceeded
	public function calcCreditDets($credit, $custID) {
		$orders = $this->getPastOrders($custID);
		if (is_array($orders)) {
			$owing = '';
			foreach ($orders as $order) {
				extract($order);
				$owing = $owing + $orderBalance;
			}
			if ($credit < $owing) {
				$balance = 0;
			}
			else {
				$balance = $credit - $owing;
			}
			return $balance;
		}
		else {
			return $credit;
		}

	}


	/* ---------------------------------------------------------------------- */
	/* ------------------------------ contact ------------------------------- */
	/* ---------------------------------------------------------------------- */


	// this function validates the contact form before it is sent off
	// returns $result['ok'] if no errors or array $result of errors
	public function validateContactForm() {
		extract($_POST);
		// if $url is not empty, most likely spam from a bot so don't send
		if ($url == '') {
			$result['name'] = $this->validate->checkName($name);
			$result['email'] = $this->validate->checkEmail($email);
			$result['message'] = $this->validate->checkMessage($message);
			$result['ok'] = $this->validate->checkErrorMessages($result);
			return $result;
		}
		else {
			$result['ok'] = false;
		}
	}	


	// this functions send the email from the contact form
	// returns false if fail or $html with success message if worked
	public function sendContactEmail() {			
		extract($_POST);
		// send mail
		$to = 'ju.explores@gmail.com, shop@afas.co.nz';
		$subject = 'New message from www.artprints.kiwi.nz';
		$message = 'From: '.$name."\n".'Phone: '.$phone."\n".'Email: '.$email."\n".'Message: '.$message;
		$headers = 'From: '.$email;
		// The email worked
		if (@mail($to, $subject, $message, $headers)) {
			$html .= "\t\t\t\t\t".'<div class="success">'."\n";
			$html .= "\t\t\t\t\t\t".'<p>Your message has been sent, thank you!</p>'."\n";
			$html .= "\t\t\t\t\t".'</div>'."\n";
		}
		else {
			$html = false;
		}
		return $html;
	}


	/* ---------------------------------------------------------------------- */
	/* ----------------------- account / orders page ------------------------ */
	/* ---------------------------------------------------------------------- */


	// this function collects the past orders from the database for the logged in customer
	// if there are past orders, it displays them in the returned $html
	// $orders 	= array of orders
	// $id 		= customer id
	// $title 	= heading for either admin or customer
	public function displayOrders($orders='', $id='', $title='') {
		// if getting all orders from one customer id
		if ($id) {
			$orders = $this->getPastOrders($id);
		}
		if (is_array($orders)) {
			$html = "\t\t\t\t\t".'<h3>'.$title.'</h3>'."\n";
			$html .= "\t\t\t\t\t".'<div id="all_orders">'."\n";
			$html .= "\t\t\t\t\t\t".'<table>'."\n";
			$html .= "\t\t\t\t\t\t\t".'<tr>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<th>ordered</th>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<th>items</th>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<th>status</th>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<th>balance</th>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<th>total</th>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<th></th>'."\n";
			$html .= "\t\t\t\t\t\t\t".'</tr>'."\n";
			foreach ($orders as $order) {
				extract($order);	
				$html .= "\t\t\t\t\t\t\t".'<tr>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td>'.date('j M y', $orderDate).'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td>'.$orderQty.'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td>'.$orderDelStatus.'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td ';
				if ($orderBalance > 0) {
					$html .= 'class="overdue"';
				}
				$html .= '>$'.sprintf("%01.2f", $orderBalance).'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td>$'.sprintf("%01.2f", $orderTotalValue).'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td><a href="index.php?page=order&amp;id='.$orderID.'">view</a></td>'."\n";
				$html .= "\t\t\t\t\t\t\t".'</tr>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</table>'."\n";
			$html .= "\t\t\t\t\t".'</div>'."\n";
		}
		return $html;
	}

	
	// this function collects all the customers from the db and returns them in a select box
	// or 
	// displays all the months and years in 2 drop down boxes
	// returns $html with the dropdown box of information
	// $options 	= either 'customers' or 'months'
	// $title 		= the heading for the form
	// $action 		= the action for the submitted form
	public function displaySelect($options, $title, $action=''){
		if (!$action) {
			$action = $_SERVER['REQUEST_URI'];
		}
		$html = "\t\t\t\t\t".'<h4>'.$title.'</h4>'."\n";
		$html .= "\t\t\t\t\t".'<form action="'.$action.'" method="post" class="form narrow">'."\n";
		// choosing a customer to view their orders
		if ($options == 'customers') {
			$options = $this->collectCustomer();
			$html .= "\t\t\t\t\t\t".'<label for="customers">customer:</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<select name="customers">'."\n";
			$html .= "\t\t\t\t\t\t\t".'<option value="0">Please select...</option>'."\n";
			foreach ($options as $option) {
				extract($option);
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$custID.':'.$custCompany.'">'.$custCompany.'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
			$html .= "\t\t\t\t\t\t".'<input type="submit" name="customer_select" value="select customer" />'."\n";		
		}
		// else choosing a date to display orders
		else if ($options == 'months') {
			$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
			$years = array(2013, 2014, 2015, 2016, 2017, 2018, 2019, 2020, 2021, 2022, 2023, 2024, 2025, 2026);
			$html .= "\t\t\t\t\t\t".'<label for="month">select a month</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<select name="month">'."\n";
			$html .= "\t\t\t\t\t\t\t".'<option value="13">Please select...</option>'."\n";
			foreach ($months as $key=>$month) {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$key.'">'.$month.'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
			$html .= "\t\t\t\t\t\t".'<label for="year">select a year</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<select name="year">'."\n";
			$html .= "\t\t\t\t\t\t\t".'<option value="0">Please select...</option>'."\n";
			foreach ($years as $year) {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$year.'">'.$year.'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
			$html .= "\t\t\t\t\t\t".'<input type="submit" name="date_select" value="view orders" />'."\n";
		}
		$html .= "\t\t\t\t\t".'</form>'."\n";
		return $html;
	}


	// this function validates the info from the account form
	// returns $result['ok'] if no errors
	// returns array $result of errors if validation errors
	public function validateAccountForm() {
		extract($_POST);
		$result['company'] = $this->validate->checkName($company_name);
		$result['first'] = $this->validate->checkName($first_name);
		$result['last'] = $this->validate->checkName($last_name);
		$result['phone'] = $this->validate->checkPhone($phone);
		$result['email'] = $this->validate->checkEmail($email);
		$result['billAddress'] = $this->validate->checkRequired($billing_address);
		$result['billSuburb'] = $this->validate->checkRequired($billing_suburb);
		$result['billCity'] = $this->validate->checkRequired($billing_city);
		$result['billCode'] = $this->validate->checkNumeric($billing_postcode, true);
		$result['delAddress'] = $this->validate->checkRequired($delivery_address);
		$result['delSuburb'] = $this->validate->checkRequired($delivery_suburb);
		$result['delCity'] = $this->validate->checkRequired($delivery_city);
		$result['delCity'] = $this->validate->checkRequired($delivery_city);
		$result['delCode'] = $this->validate->checkNumeric($delivery_postcode, true);
		if($reset != '') {
			$result['reset'] = $this->validate->checkPassword($reset);
		}
		if($credit != '') {
			$credit = str_replace ('$' ,'' , $credit);
			$result['credit'] = $this->validate->checkNumeric($credit, true);
		}
		$result['password'] = $this->validate->checkPassword($password);	
		$result['ok'] = $this->validate->checkErrorMessages($result);
		if ($result['password'] == '' && $result['ok'] == 1) {
			$identity = $this->getCustomer($_SESSION['email'], $password);
			if (is_array($identity)) {
				$result['password'] = '';
				$result['ok'] = true;
			}
			else {
				$result['password'] = "\t\t\t\t\t\t".'<p class="error">Sorry, your password is incorrect</p>';
				$result['ok'] = false;
			}
		}
		return $result;
	}


	// validation for change of password form
	// $o_password 	= old password
	// $password1 	= new password
	// $password2	= confirmation of new password
	// returns $result['ok'] if no errors
	// returns array of errors in $result if errors
	public function validatePasswordForm() {
		extract($_POST);
		$o_password = $this->checkPassword($o_password);
		if (is_array($o_password['password'])) {
			$result['password'] = '';
		}
		else {
			$result['password'] = $o_password['password'];
		}
		$result['password1'] = $this->validate->checkPassword($password1);
		$result['password2'] = $this->validate->checkPasswordMatch($password1, $password2);
		$result['ok'] = $this->validate->checkErrorMessages($result);	
		return $result;
	}


	/* ---------------------------------------------------------------------- */
	/* ----------------------------- single order --------------------------- */
	/* ---------------------------------------------------------------------- */


	// this function displays a single order in list format
	// $id 			= order ID
	// $custID 		= custID
	// $shipping 	= if $shipping = true, it is a new order about to be placed
	// returns $html of order details
	public function reviewOrder($id='', $custID='', $shipping='') {
		if ($id) {
			$order = $this->getOrders($id);
			if (is_array($order)) {
				$items = $this->getOrderItems($id);
			}
		}
		else {
			$items = $_SESSION['cart'];
		}
		$html = "\t\t\t\t\t".'<h3 id="review">order review <i class="icon-circle-arrow-down"></i></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="review_options" class="last">'."\n";
		if (!$shipping) {
			extract($order[0]);
			$html .= "\t\t\t\t\t\t\t".'<div class="checkout_item">'."\n";
			$customer = $this->collectCustomer($custID);
			$html .= "\t\t\t\t\t\t\t\t".'<p><strong>company<br />'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'date ordered<br />'."\n";  		
			$html .= "\t\t\t\t\t\t\t\t".'delivery status<br />'."\n";  
			$html .= "\t\t\t\t\t\t\t\t".'purchase order #<br />'."\n"; 
			$html .= "\t\t\t\t\t\t\t\t".'courier tracking #<br />'."\n";  	
			$html .= "\t\t\t\t\t\t\t\t".'order notes<br /></strong></p>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<p><strong>'.$customer['custCompany'].'</strong><br/>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".date('j M y', $orderDate).'<br />'."\n";
			$html .= "\t\t\t\t\t\t\t\t".$orderDelStatus.'<br />'."\n";
			$html .= "\t\t\t\t\t\t\t\t".$orderPurcNum.'<br />'."\n";
			$html .= "\t\t\t\t\t\t\t\t".$orderCourierNum.'<br />'."\n";
			$html .= "\t\t\t\t\t\t\t\t".$orderNotes.'</p>'."\n";
			$html .= "\t\t\t\t\t\t\t".'</div>'."\n";

			$html .= "\t\t\t\t\t\t\t".'<div class="checkout_item">'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<p><strong>deliver to:</strong></p>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<p>'.$orderDelFirstName.' '.$orderDelLastName.'<br/>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".$orderDelAddress.'<br />'."\n";
			$html .= "\t\t\t\t\t\t\t\t".$orderDelSuburb.'<br />'."\n";
			$html .= "\t\t\t\t\t\t\t\t".$orderDelCity.', '.$orderDelPostcode.'</p>'."\n";
			$html .= "\t\t\t\t\t\t\t".'</div>'."\n";
			$html .= "\t\t\t\t\t\t\t".'<div class="checkout_item">'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<p><strong>billing to:</strong></p>'."\n";	
			$html .= "\t\t\t\t\t\t\t\t".'<p>'.$orderBillFirstName.' '.$orderBillLastName.'<br/>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".$orderBillAddress.'<br />'."\n";
			$html .= "\t\t\t\t\t\t\t\t".$orderBillSuburb.'<br />'."\n";
			$html .= "\t\t\t\t\t\t\t\t".$orderBillCity.', '.$orderBillPostcode.'</p>'."\n";
			$html .= "\t\t\t\t\t\t\t".'</div>'."\n";
		}
		else {
			$orderValue = 0;
		}
		foreach ($items as $item) {
			extract($item);
			$art = $this->getProductByID($artID, true);
			if (is_array($art)) {
				extract($art[0]);
			}
			else {
				$artName = 'not available';
				$artistName = 'not available';
			}
			$subtotal = $sizePrice * $orderProdQty;
			if ($shipping) {
				$orderValue = $orderValue + $subtotal;
				$orderShipValue = $shipping;
				$orderGSTValue = ($orderShipValue + $orderValue) * .15;
				$orderTotalValue = $orderShipValue + $orderValue + $orderGSTValue;
				$_SESSION['total'] = $orderTotalValue;
				$sizes = $this->getSizePrices($artID, $sizeID);
				extract($sizes[0]);
			}
			$html .= "\t\t\t\t\t\t\t".'<div class="checkout_item">'."\n";
			if ($shipping) {
				$html .= "\t\t\t\t\t\t\t\t".'<p><a href="index.php?page=cart" title="edit">\''.$artName.'\' </a> by '.$artistName.'<br />'."\n";

			}
			else {
				$html .= "\t\t\t\t\t\t\t\t".'<p><a href="index.php?page=product&amp;id='.$artID.'" title="edit">\''.$artName.'\' </a> by '.$artistName.'<br />'."\n";

			}
			$html .= "\t\t\t\t\t\t\t\t\t".'<span>'.$artType.' '.$sizeWidth.' x '.$sizeHeight.'mm</span></p>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<p>'.$orderProdQty.' @ $'.sprintf("%01.2f", $sizePrice).'&nbsp;&nbsp;&nbsp;<strong>subtotal $'.sprintf("%01.2f", $subtotal).' NZD</strong></p>'."\n";
			$html .= "\t\t\t\t\t\t\t".'</div>'."\n";
		}	
		$html .= "\t\t\t\t\t\t\t".'<div class="checkout_item">'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<p>sub total<br />'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<a href="index.php?page=shipping" target="_blank">shipping &amp; handling</a><br />'."\n";  		
		$html .= "\t\t\t\t\t\t\t\t".'GST component<br />'."\n";  	
		$html .= "\t\t\t\t\t\t\t\t".'<strong>grand total<br /></strong></p>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<p>$'.sprintf("%01.2f", $orderValue).' NZD<br/>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".' $'.sprintf("%01.2f", $orderShipValue).' NZD<br />'."\n";
		$html .= "\t\t\t\t\t\t\t\t".' $'.sprintf("%01.2f", $orderGSTValue).' NZD<br />'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<strong>$'.sprintf("%01.2f", $orderTotalValue).' NZD</strong></p>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</div>'."\n";
		if (!$shipping) {
			$dispatchedMonth = date('M y', $orderDispatchDate);
			$monthDue = strtotime(date("M y", strtotime($dispatchedMonth)) . "+1 month");
			$monthDue = date('M y', $monthDue);
			$html .= "\t\t\t\t\t\t\t".'<div class="checkout_item">'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<p><strong>payment received<br />'."\n";
			
			// no deposit has been paid and not dispatched so deposit due
			if ($orderPaymentRec == 0) {
				$now = ' immediately';
				$due = ($orderTotalValue / 100) * $customer['custDeposit'];
				if ($due != 0) {
					$html .= "\t\t\t\t\t\t\t\t".'deposit due'.$now.'<br />'."\n";
				}
			}
			$html .= "\t\t\t\t\t\t\t\t".'balance due by 20 '.$monthDue.'</strong></p>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<p><strong>$'.sprintf("%01.2f", $orderPaymentRec).' NZD<br />'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<span';
			if ($orderBalance > 0) {
				$html .= ' class="overdue"';
			}
			if ($due != 0) {
				$html .= '>$'.sprintf("%01.2f", $due).' NZD</span><br />'."\n";
				$html .= '$'.sprintf("%01.2f", $orderTotalValue).' NZD</strong></p>'."\n";
			}
			else {
				$html .= '>$'.sprintf("%01.2f", $orderBalance).' NZD</span></strong></p>'."\n";
			}
			$html .= "\t\t\t\t\t\t\t".'</div>'."\n";
		}
		
		else {
			$deposit = ($orderTotalValue / 100) * $_SESSION['deposit'];
			$html .= "\t\t\t\t\t\t\t".'<div class="checkout_item">'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<p>deposit required before dispatch</p>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<p>$'.sprintf("%01.2f", $deposit).' NZD</p>'."\n";
			$html .= "\t\t\t\t\t\t\t".'</div>'."\n";
			$_SESSION['shipping'] = $shipping;
		}
		$html .= "\t\t\t\t\t\t".'</div>'."\n";	
		return $html;
	}


	// calculates:
	// - total number of items 	($order['quantity'])
	// - total order value 		($order['orderValue'])
	// - total GST value  		($order['orderGSTValue'])
	// and returns in array $order
	public function calculateFinancesForOrder() {
		$items = $_SESSION['cart'];
		$orderValue = 0;
		$quantity = 0;
		foreach ($items as $item) {
			extract($item);
			$subtotal = $sizePrice * $orderProdQty;
			$orderValue = $orderValue + $subtotal;	
			$quantity = $quantity + $orderProdQty;	
		}
		$order['quantity'] = $quantity;
		$order['orderValue'] = $orderValue;
		$order['orderGSTValue'] = ($_SESSION['shipping'] + $order['orderValue']) * .15;
		return $order;
	}


	// takes provided password and checks it against the current logged in customer
	// to check identity is correct
	// returns $result['ok'] if correct and false if not
	public function checkPassword($password) {
		$identity = $this->getCustomer($_SESSION['email'], $password);
			if (is_array($identity)) {
				$result['password'] = '';
				$result['ok'] = true;
			}
			else {
				$result['password'] = "\t\t\t\t\t\t".'<p class="error">Sorry, your password is incorrect</p>';
				$result['ok'] = false;
			}
		return $result;
	}


	/* ---------------------------------------------------------------------- */
	/* ------------------------------ dashboard ----------------------------- */
	/* ---------------------------------------------------------------------- */


	// this functions shows all the functions available to admin in an aside
	public function displaySidebarOptions() {
		$html = "\t\t\t\t".'<aside>'."\n";
		$html .= "\t\t\t\t\t".'<h2 id="filter_heading" class="mobile">filter by <i class="icon-circle-arrow-down"></i></h2>'."\n";
		$html .= "\t\t\t\t\t".'<div id="filters">'."\n";
		$html .= "\t\t\t\t\t\t".'<h3 id="order_option_heading">view orders by <i class="icon-circle-arrow-down"></i></h3>'."\n";
		// order options
		$html .= "\t\t\t\t\t\t".'<div id="order_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=orders&o=m"><h5>month</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=orders&o=c"><h5>customer</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=orders&o=d"><h5>awaiting dispatch</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=orders&o=o"><h5>money outstanding</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- order_options -->'."\n";
		// royality options
		$html .= "\t\t\t\t\t\t".'<h3 id="royalty_option_heading">view royalties by <i class="icon-circle-arrow-down"></i></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="royalty_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=royalty"><h5>all</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=royalty&r=m"><h5>month</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=royalty&r=a"><h5>artist</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=royalty&r=u"><h5>unpaid</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- royalty_options -->'."\n";
		// crud artists
		$html .= "\t\t\t\t\t\t".'<h3 id="artists_option_heading">artists <i class="icon-circle-arrow-down"></i></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="artists_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=adminartist&add=1"><h5>add</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=adminartist&edit=1"><h5>edit</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- artist_options -->'."\n";
		// crud products
		$html .= "\t\t\t\t\t\t".'<h3 id="product_option_heading">products <i class="icon-circle-arrow-down"></i></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="product_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=adminartwork&add=1"><h5>add</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=adminartwork&edit=1"><h5>edit</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- product_options -->'."\n";
		// accounts
		$html .= "\t\t\t\t\t\t".'<h3 id="accounts_option_heading">accounts <i class="icon-circle-arrow-down"></i></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="account_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=account"><h5>edit customer account</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=account&p=1"><h5>change my password</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- accounts_options -->'."\n";
		// banners
		$html .= "\t\t\t\t\t\t".'<h3 id="banners_option_heading">banners <i class="icon-circle-arrow-down"></i></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="banners_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=banner&a=a"><h5>add</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=banner&a=e"><h5>edit</h5></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- accounts_options -->'."\n";
		$html .= "\t\t\t\t\t".'</div><!-- filters -->'."\n";
		$html .= "\t\t\t\t".'</aside>'."\n";
		return $html;
	}

	
	// this function collects the artist from the database
	// it returns $html with the dropdown box
	public function selectArtistForm($page='') {
		if ($page == 'royalty') {
			$action = 'index.php?page=royalty&r=a';
		}
		else {
			$action = 'index.php?page=adminartist';
		}
		$artists = $this->getArtists('', 'hidden');
		$html = "\t\t\t\t\t".'<h4>Choose an artist from the select box below</h4>'."\n";
		$html .= "\t\t\t\t\t".'<form action="'.$action.'" method="post" class="form">'."\n";
		$html .= "\t\t\t\t\t\t".'<label for="all_artists">Select an artist:</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<select name="all_artists" id="all_artists">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<option value="0">Please select...</option>'."\n";
		foreach ($artists as $artist) {
			if ($page == 'royalty') {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$artist['artistID'].':'.$artist['artistName'].'">'.$artist['artistName'].'</option>'."\n";
			}
			else {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$artist['artistID'].'">'.$artist['artistName'].'</option>'."\n";
			}
		}
		$html .= "\t\t\t\t\t\t".'</select>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="submit" name="artist_select" value="select artist" />'."\n";
		$html .= "\t\t\t\t\t".'</form>'."\n";
		return $html;
	}


	// this function validates the info from the artist form and returns $result['ok'] if no errors
	public function validateArtistForm() {
		extract($_POST);
		$result['first_name'] = $this->validate->checkName($first_name);
		$result['last_name'] = $this->validate->checkName($last_name);
		$result['known_name'] = $this->validate->checkName($known_name);
		$result['phone'] = $this->validate->checkPhone($phone);
		$result['email'] = $this->validate->checkEmail($email);
		$result['password'] = $this->validate->checkPassword($password);	
		$result['ok'] = $this->validate->checkErrorMessages($result);
		if ($result['password'] == '') {
			$identity = $this->getCustomer($_SESSION['email'], $password);
			if (is_array($identity)) {
				$result['password'] = '';
				$result['ok'] = true;
			}
			else {
				$result['password'] = "\t\t\t\t\t\t".'<p class="error">Sorry, your password is incorrect</p>';
				$result['ok'] = false;
			}
		}
		return $result;
	}


	// this function validates the info from the artwork form and returns $result['ok'] if no errors
	public function validateArtworkForm() {
		extract($_POST);
		$result['artist_name'] = $this->validate->checkSelect($artistID);
		$result['art_type'] = $this->validate->checkSelect($art_type);
		$result['art_name'] = $this->validate->checkName($art_name);
		$result['keywords'] = $this->validate->checkRequired($art_keywords);
		// size 1
		$result['width1'] = $this->validate->checkNumeric($width1);
		$result['height1'] = $this->validate->checkNumeric($height1);
		if ($width1 != '') {
			$result['apCode1'] = $this->validate->checkRequired($apcode1);	
		}
		$result['rrp1'] = $this->validate->checkNumeric($rrp1);
		$result['wsp1'] = $this->validate->checkNumeric($wsp1);
		$result['royalty1'] = $this->validate->checkNumeric($royalty1);
		// size 2
		$result['width2'] = $this->validate->checkNumeric($width2);
		$result['height2'] = $this->validate->checkNumeric($height2);
		if ($width2 != '') {
			$result['apCode2'] = $this->validate->checkRequired($apcode2);	
		}
		$result['rrp2'] = $this->validate->checkNumeric($rrp2);
		$result['wsp2'] = $this->validate->checkNumeric($wsp2);
		$result['royalty2'] = $this->validate->checkNumeric($royalty2);
		// size 3
		$result['width3'] = $this->validate->checkNumeric($width3);
		$result['height3'] = $this->validate->checkNumeric($height3);
		if ($width3 != '') {
			$result['apCode3'] = $this->validate->checkRequired($apcode3);	
		}
		$result['rrp3'] = $this->validate->checkNumeric($rrp3);
		$result['wsp3'] = $this->validate->checkNumeric($wsp3);
		$result['royalty3'] = $this->validate->checkNumeric($royalty3);
		$result['password'] = $this->validate->checkPassword($password);	
		$result['ok'] = $this->validate->checkErrorMessages($result);
		if ($result['ok'] == true) {
			$identity = $this->getCustomer($_SESSION['email'], $password);
			if (is_array($identity)) {
				$result['password'] = '';
				$result['ok'] = true;
			}
			else {
				$result['password'] = "\t\t\t\t\t\t".'<p class="error">Sorry, your password is incorrect</p>';
				$result['ok'] = false;
			}
		}
		return $result;
	}


	// this function collects the art in the 3 categories (framed, canvas and prints) from the database
	// it returns a form with a dropdown box for each format/category in $html
	public function selectArtworkForm() {
		$framed = $this->getProductsByBatch('', '', 'framed', 'all');
		$canvas = $this->getProductsByBatch('', '', 'canvas', 'all');
		$prints = $this->getProductsByBatch('', '', 'print', 'all');
		$html .= "\t\t\t\t\t".'<h4>Choose an artwork you wish to edit from one of the select boxes below</h4>'."\n";
		$html .= "\t\t\t\t\t".'<form action="index.php?page=adminartwork" method="post" class="form">'."\n";
		
		// if $framed has an array of all the framed artwork
		if (isset($framed)) {
			$html .= "\t\t\t\t\t\t".'<label for="framed_artwork">framed</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<select name="framed_artwork" id="framed_artwork">'."\n";
			$html .= "\t\t\t\t\t\t\t".'<option value="0">Please select...</option>'."\n";
			foreach ($framed as $artwork) {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$artwork['artID'].'">\''.$artwork['artName'].'\' by '.$artwork['artistName'].'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
		}
		
		// if $framed has an array of all the canvas
		if (isset($canvas)) {
			$html .= "\t\t\t\t\t\t".'<label for="canvas_artwork">canvas</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<select name="canvas_artwork" id="framed_artwork">'."\n";
			$html .= "\t\t\t\t\t\t\t".'<option value="0">Please select...</option>'."\n";
			foreach ($canvas as $artwork) {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$artwork['artID'].'"><strong>\''.$artwork['artName'].'\'</strong> by '.$artwork['artistName'].'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
		}
		
		// if $framed has an array of all the prints
		if (isset($prints)) {
			$html .= "\t\t\t\t\t\t".'<label for="print_artwork">prints</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<select name="print_artwork" id="framed_artwork">'."\n";
			$html .= "\t\t\t\t\t\t\t".'<option value="0">Please select...</option>'."\n";
			foreach ($prints as $artwork) {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$artwork['artID'].'">\''.$artwork['artName'].'\' by '.$artwork['artistName'].'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
		}
		$html .= "\t\t\t\t\t\t".'<input type="submit" name="art_select" value="select artwork" />'."\n";
		$html .= "\t\t\t\t\t".'</form>'."\n";
		return $html;
	}


	// this function gives an error if more than one artwork has been selected
	// it returns a single id in $id if one option has been selected from the 3 drop down menus.
	public function checkArtworkSelected() {
		extract($_POST);
		if ($framed_artwork != 0 && $canvas_artwork != 0 && $print_artwork != 0) {
			$id = 'please select only one artwork to edit';
			return $id;
		}
		else if ($framed_artwork != 0 && $canvas_artwork != 0) {
			$id = 'please select only one artwork to edit';
			return $id;
		}
		else if ($framed_artwork != 0 && $print_artwork != 0) {
			$id = 'please select only one artwork to edit';
			return $id;
		}
		else if ($canvas_artwork != 0 && $print_artwork != 0) {
			$id = 'please select only one artwork to edit';
			return $id;
		}
		if ($framed_artwork != 0) {
			$id = $framed_artwork;
		}
		else if ($canvas_artwork != 0) {
			$id = $canvas_artwork;
		}
		else if ($print_artwork != 0) {
			$id = $print_artwork;
		}		
		return $id;
	}


	// this function updates the selected subjects for an art item
	// if editing it deletes all existing entries on the database and then adds new ones using function updateArtSubject in the dbClass
	// if adding, it adds new subject/artwork relationships using function updateArtSubject in the dbClass
	public function updateSubjects($add='', $id='') {
		// adding a new artwork/subject relatiionship
		if ($add != '' && $id != '') {
			$artID = $id;
		}
		// editing subjects so delete all existing to add new subject selections
		else {
			$artID = $_POST['art_id'];
			$change = $this->deleteArtSubject($artID);
		}
		foreach ($_POST as $key=>$subject) {
			$pos = strpos($key, 'subject');
			if ($pos === false) {}
			else {
				$change = $this->updateArtSubject($subject, $artID);
			}
		}
		return $change;
	}


	// this function updates/adds new size entries for a given art id
	public function editSizes($id='') {
		extract($_POST);
		// size 1
		$sizeInfo['width'] = $width1;
		$sizeInfo['height'] = $height1;
		$sizeInfo['code'] = $apcode1;
		$sizeInfo['rrp'] = $rrp1;
		$sizeInfo['wsp'] = $wsp1;
		$sizeInfo['royalty'] = $royalty1;
		$sizeInfo['artID'] = $art_id;
		// check if there is a size id (if there is an entry on the database already)
		if ($size1_id != '') {
			$newInfo = 0;
			// loop through size info to see if info has been entered
			// add 1 if so
			foreach ($sizeInfo as $info) {
				$newInfo = ($info > 0) ? $newInfo + 1 : $newInfo + 0;
			}
			// if new info contains value of more than 1 then info has been added.
			if ($newInfo > 1) {
				$sizeUpdate[0] = $this->updateSizePrice($sizeInfo, $size1_id);
			}
			// else, admin has removed the info from the entry so delete the record in the database
			else {
				$sizeUpdate[0] = $this->updateSizePrice($sizeInfo, $size1_id, 'delete');
			}
		}
		else {
			$newInfo = 0;
			// loop through size info to see if new info has been entered
			// add 1 if so
			foreach ($sizeInfo as $info) {
				$newInfo = ($info > 0) ? $newInfo + 1 : $newInfo + 0;
			}
			// if new info contains value of more than 0 then new info has been added.
			if ($newInfo > 1) {
				$sizeInfo['artID'] = $id;
				$sizeUpdate[0] = $this->updateSizePrice($sizeInfo);
			}
		}
		
		// size 2
		$sizeInfo['width'] = $width2;
		$sizeInfo['height'] = $height2;
		$sizeInfo['code'] = $apcode2;
		$sizeInfo['rrp'] = $rrp2;
		$sizeInfo['wsp'] = $wsp2;
		$sizeInfo['royalty'] = $royalty2;
		$sizeInfo['artID'] = $art_id;
		// check if there is a size id (if there is an entry on the database already)
		if ($size2_id != '') {
			$newInfo = 0;
			// loop through size info to see if info has been entered
			// add 1 if so
			foreach ($sizeInfo as $info) {
				$newInfo = ($info > 0) ? $newInfo + 1 : $newInfo + 0;
			}
			// if new info contains value of more than 1 then info has been added.
			if ($newInfo > 1) {
				$sizeUpdate[1] = $this->updateSizePrice($sizeInfo, $size2_id);
			}
			// else, admin has removed the info from the entry so delete the record in the database
			else {
				$sizeUpdate[1] = $this->updateSizePrice($sizeInfo, $size2_id, 'delete');
			}
		}
		else {
			$newInfo = 0;
			// loop through size info to see if new info has been entered
			// add 1 if so
			foreach ($sizeInfo as $info) {
				$newInfo = ($info > 0) ? $newInfo + 1 : $newInfo + 0;
			}
			// if new info contains value of more than 0 then new info has been added.
			if ($newInfo > 1) {
				$sizeInfo['artID'] = $id;
				$sizeUpdate[1] = $this->updateSizePrice($sizeInfo);
			}
		}
		
		// size 3
		$sizeInfo['width'] = $width3;
		$sizeInfo['height'] = $height3;
		$sizeInfo['code'] = $apcode3;
		$sizeInfo['rrp'] = $rrp3;
		$sizeInfo['wsp'] = $wsp3;
		$sizeInfo['royalty'] = $royalty3;
		$sizeInfo['artID'] = $art_id;
		// check if there is a size id (if there is an entry on the database already)
		if ($size3_id != '') {
			$newInfo = 0;
			// loop through size info to see if info has been entered
			// add 1 if so
			foreach ($sizeInfo as $info) {
				$newInfo = ($info > 0) ? $newInfo + 1 : $newInfo + 0;
			}
			// if new info contains value of more than 1 then info has been added.
			if ($newInfo > 1) {
				$sizeUpdate[2] = $this->updateSizePrice($sizeInfo, $size3_id);
			}
			// else, admin has removed the info from the entry so delete the record in the database
			else {
				$sizeUpdate[2] = $this->updateSizePrice($sizeInfo, $size3_id, 'delete');
			}
		}
		else {
			$newInfo = 0;
			// loop through size info to see if new info has been entered
			// add 1 if so
			foreach ($sizeInfo as $info) {
				$newInfo = ($info > 0) ? $newInfo + 1 : $newInfo + 0;
			}
			// if new info contains value of more than 0 then new info has been added.
			if ($newInfo > 1) {
				$sizeInfo['artID'] = $id;
				$sizeUpdate[2] = $this->updateSizePrice($sizeInfo);
			}
		}
	}



	/* ---------------------------------------------------------------------- */
	/* --------------- uploading an image for a new product ----------------- */
	/* ---------------------------------------------------------------------- */


	// this function checks the result of upload/resize of photo for a new product
	// $mode 	= either 'add' or 'edit'
	public function uploadArt($mode, $artID) {
		if ($mode == 'update') {
			// if a new image has been submited send it to be resized etc
			if ($_FILES['new_image']['name']) {
				$iresult = $this->uploadAndResizePhoto($artID);
				// if the new file has uploaded successfully, delete the old image
				if ($iresult['resized'] == 1) {
					@unlink('images/thumbs/'.$_POST['art_image']);
					@unlink('images/mobile/'.$_POST['art_image']);
					@unlink('images/tablet/'.$_POST['art_image']);
					@unlink('images/desktop/'.$_POST['art_image']);
					@unlink('images/lightbox/'.$_POST['art_image']);
				}
			}
			// when upload and resize is successful need to add the new name to the database
			if ($iresult['resized']) {
				$iresult['update'] = $this->editArt($artID, $iresult['new_image']);			
			}
			// resize of image failed
			else {
				$iresult = false;
			}
			$iresult['artID'] = $artID;
			return $iresult;
		}
		// if $mode is add
		else {
			$iresult = $this->uploadAndResizePhoto($artID);
			// when upload and resize is successful need to add image title to the database
			if ($iresult['resized']) {
				$iresult['add'] = $this->editArt($artID, $iresult['new_image']);	
			}
			return $iresult;  
		}
	}

	
	// uploads, saves and resizes photo in 5 sizes
	// returns $result['new_image'] = basename($thumbPath) and $result['resized'] = true if successful
	// returns false if fails
	private function uploadAndResizePhoto() {
		$desktopFolder = 'images/desktop';
		$tabletFolder = 'images/tablet';
		$mobileFolder = 'images/mobile';
		$thumbFolder = 'images/thumbs';
		$lightboxFolder = 'images/lightbox';
		$fileTypes = array('image/jpeg', 'image/jpg', 'image/pjpeg');
		// instantialise the Upload class
		$upload = new Upload('new_image', $fileTypes, $desktopFolder);
		$returnFile = $upload->isUploaded();
		// if no file has been uploaded then display error exit as nothing to resize
		if (!$returnFile) {
			$result['image_error'] = $upload->msg;
			$result['resized'] = false;
			return $result;
		}
		
		// there is a file so resize, first set the file paths for all images
		$fileName = basename($returnFile);
		$desktopPath = $desktopFolder.'/'.$fileName;
		$tabletPath = $tabletFolder.'/'.$fileName;
		$mobilePath = $mobileFolder.'/'.$fileName;
		$thumbPath = $thumbFolder.'/'.$fileName;
		$lightboxPath = $lightboxFolder.'/'.$fileName;
		// copy file to all other folders folders
		copy($returnFile, $tabletPath);
		copy($returnFile, $thumbPath);
		copy($returnFile, $mobilePath);
		copy($returnFile, $lightboxPath);
		// if copy fails then exit
		if (!file_exists($tabletPath) || !file_exists($thumbPath) || !file_exists($mobilePath) || !file_exists($lightboxPath)) {
			return false;
		}
		
		$imgInfo = getimagesize($returnFile);
		
		// resize image to 200px wide for the thumbnail
		$resizePhoto = new ResizeImage($thumbPath, 200, $thumbFolder);
		 // if the resize to 200px is unsuccessful
		if (!$resizePhoto->resize()) { 
//			 echo 'Unable to resize image to 200 pixels'; // (development only)
		}

		// resize image to 300px wide for tablet/mobile view
		$resizePhoto1 = new ResizeImage($mobilePath, 300, $mobileFolder);
		 // if the resize to 300px is unsuccessful
		if (!$resizePhoto1->resize()) { 
//			 echo 'Unable to resize image to 300 pixels'; // (development only)
		}

		// resize image to 400px wide for tablet/mobile view
		$resizePhoto2 = new ResizeImage($tabletPath, 400, $tabletFolder);
		 // if the resize to 400px is unsuccessful
		if (!$resizePhoto2->resize()) { 
//			 echo 'Unable to resize image to 400 pixels'; // (development only)
		}

		// resize image to 600px wide for the desktop view
		$resizePhoto3 = new ResizeImage($desktopPath, 600, $desktopFolder);
		 // if the resize to 600px is unsuccessful
		if (!$resizePhoto3->resize()) { 
//			 echo 'Unable to resize image to 600 pixels'; // (development only)
		}

		// resize image to 800px wide for the lightbox view
		$resizePhoto4 = new ResizeImage($lightboxPath, 800, $lightboxFolder);
		 // if the resize to 800px is unsuccessful
		if (!$resizePhoto4->resize()) { 
//			 echo 'Unable to resize image to 800 pixels'; // (development only)
		}
		
		// if the new resized thumb, the tablet and the desktop image exist it worked so return['ok'] true!
		if(file_exists($thumbPath) && file_exists($mobilePath) && file_exists($tabletPath) && file_exists($desktopPath) && file_exists($lightboxPath)) {
			$result['new_image'] = basename($thumbPath);
			$result['resized'] = true;
			return $result;
		}
		else {
			return false;
		} 
	}	


	/* ---------------------------------------------------------------------- */
	/* --------------- uploading an image for a new product ----------------- */
	/* ---------------------------------------------------------------------- */


	// this function adds a new banner
	public function addBanner() {
		if ($_FILES['banner_img']['name']) {
			$iresult = $this->uploadAndResizeBanner();
		}
		// when upload and resize is successful need to add the new name to the database
		if ($iresult['resized']) {
			$iresult['bannerID'] = $this->editBanner($iresult['banner_img']);	
		}
		return $iresult;
	}

	
	// uploads, saves and resizes banner in 3 sizes
	// returns $result['new_image'] = basename($thumbPath) and $result['resized'] = true if successful
	// returns false if fails
	private function uploadAndResizeBanner() {
		$desktopFolder = 'images/banners/desktop';
		$tabletFolder = 'images/banners/tablet';
		$mobileFolder = 'images/banners/mobile';
		$fileTypes = array('image/jpeg', 'image/jpg', 'image/pjpeg');
		// instantialise the Upload class
		$upload = new Upload('banner_img', $fileTypes, $desktopFolder);
		$returnFile = $upload->isUploaded();
		// if no file has been uploaded then display error exit as nothing to resize
		if (!$returnFile) {
			$result['image_error'] = $upload->msg;
			$result['resized'] = false;
			return $result;
		}
		
		// there is a file so resize, first set the file paths for all images
		$fileName = basename($returnFile);
		$desktopPath = $desktopFolder.'/'.$fileName;
		$tabletPath = $tabletFolder.'/'.$fileName;
		$mobilePath = $mobileFolder.'/'.$fileName;
		// copy file to all other folders folders
		copy($returnFile, $tabletPath);
		copy($returnFile, $mobilePath);
		// if copy fails then exit
		if (!file_exists($tabletPath) || !file_exists($mobilePath)) {
			return false;
		}
		
		$imgInfo = getimagesize($returnFile);
		
		// resize image to 960px by 290px for the desktop
		$resizePhoto = new ResizeImage($desktopPath, '', $desktopFolder, '960', '290');
		 // if the resize to 200px is unsuccessful
		if (!$resizePhoto->resize()) { 
//			 echo 'Unable to resize image to 960 pixels'; // (development only)
		}

		// resize image to 735px by 222px for tablet
		$resizePhoto1 = new ResizeImage($tabletPath, '', $tabletFolder, '735', '222');
		 // if the resize to 300px is unsuccessful
		if (!$resizePhoto1->resize()) { 
//			 echo 'Unable to resize image to 735 pixels'; // (development only)
		}

		// resize image to 320px by 160px for mobile 
		$resizePhoto2 = new ResizeImage($mobilePath, '', $mobileFolder, '320', '160');
		 // if the resize to 400px is unsuccessful
		if (!$resizePhoto2->resize()) { 
//			 echo 'Unable to resize image to 320 pixels'; // (development only)
		}
		
		// if the new resized thumb, the tablet and the desktop image exist it worked so return['ok'] true!
		if(file_exists($mobilePath) && file_exists($tabletPath) && file_exists($desktopPath)) {
			$result['banner_img'] = basename($mobilePath);
			$result['resized'] = true;
			return $result;
		}
		else {
			return false;
		} 	
	}	


	/* ---------------------------------------------------------------------- */
	/* ----------------------------- royalties ------------------------------ */
	/* ---------------------------------------------------------------------- */
	

	// gets royalties by checking dispatched art and organising it by artist
	// then forwards array $info to function displayAllRoyalties to write $html to return
	public function allRoyalties($unpaid='') {
		$dispatched = $this->getDispatchedArt();
		if (is_array($dispatched)) {
			$royaltiesByArtistID = array();
			$info['ids'] = array();
			foreach ($dispatched as $item) {
				extract($item);		
				if (!in_array($artistID, $info['ids'])) {
					$info['ids'][] = $artistID;
				}
				$royalty = $royaltyAmount * $orderProdQty;	
	    		$royaltiesByArtistID[$artistID] = ($royaltiesByArtistID[$artistID] + $royalty);
			}
			$artistPayment = array();
			foreach ($royaltiesByArtistID as $artistID => $royalties) {
				$totalPayment = 0;
				$payments[$artistID] = $this->royaltiesPaid($artistID);
				$artist = $this->getArtists('', '', $artistID);
				$artistName[$artistID] = $artist[0];
				
				if (is_array($payments[$artistID])) {
					foreach ($payments[$artistID] as $payment) {
						extract($payment);
						$totalPayment = $totalPayment + $amountPaid;
					}
					$artistPayment[$artistID] = $totalPayment;
				} else {
					$artistPayment[$artistID] = 0;
				}
			}
			$info['total'] = $royaltiesByArtistID;
			$info['paid'] = $artistPayment;
			$info['artist'] = $artistName;
			$html = $this->displayAllRoyalties($info, $unpaid);
		}
		else {
			$html .= '<h3 class="validation red">There are no royalties to display or pay</h3>'."\n";
		}
		return $html;
	}


	// called from the allRoyalties function
	// displays royalties using the array $info
	// returns $html of of data in tabular format
	public function displayAllRoyalties($info, $unpaid='') {
		$html .= "\t\t\t\t\t".'<div id="all_orders">'."\n";
		$html .= "\t\t\t\t\t\t".'<table>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<tr>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<th>artist</th>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<th>total royalties</th>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<th>paid</th>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<th>balance</th>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</tr>'."\n";
		foreach ($info['ids'] as $id) {
			$subtotal = $info['total'][$id];
			$subpaid = $info['paid'][$id];
			$subbalance = $subtotal - $subpaid;
			// if only showing royalties that have a balancing owing
			if ($unpaid == true) {
				if ($subbalance > 0) {
					$html .= "\t\t\t\t\t\t\t".'<tr>'."\n";
					$html .= "\t\t\t\t\t\t\t\t".'<td>'.$info['artist'][$id]['artistName'].'</td>'."\n";
					$html .= "\t\t\t\t\t\t\t\t".'<td>$'.sprintf("%01.2f", $subtotal).'</td>'."\n";
					$html .= "\t\t\t\t\t\t\t\t".'<td class="green">$'.sprintf("%01.2f", $subpaid).'</td>'."\n";
					$html .= "\t\t\t\t\t\t\t\t".'<td class="red">$'.sprintf("%01.2f", $subbalance).'</td>'."\n";
					$html .= "\t\t\t\t\t\t\t".'</tr>'."\n";
				}
			}
			else {
				$html .= "\t\t\t\t\t\t\t".'<tr>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td>'.$info['artist'][$id]['artistName'].'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td>$'.sprintf("%01.2f", $subtotal).'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td class="green">$'.sprintf("%01.2f", $subpaid).'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td class="red">$'.sprintf("%01.2f", $subbalance).'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t".'</tr>'."\n";
			}
			$total = $total + $subtotal;
			$paid = $paid + $subpaid;
			$balance = $balance + $subbalance;
		}
		$html .= "\t\t\t\t\t\t\t".'<tr>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<td class="border">subtotals</td>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<td class="border">$'.sprintf("%01.2f", $total).'</td>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<td class="border green">$'.sprintf("%01.2f", $paid).'</td>'."\n";
			$html .= "\t\t\t\t\t\t\t\t".'<td class="border red">$'.sprintf("%01.2f", $balance).'</td>'."\n";
			$html .= "\t\t\t\t\t\t\t".'</tr>'."\n";
		$html .= "\t\t\t\t\t\t".'</table>'."\n";
		$html .= "\t\t\t\t\t".'</div>'."\n";
		return $html;
	}


	// this function validates the info when recording a new royalty payment
	// returns $result['ok'] if no errors
	public function validateRoyaltyPayment() {
		extract($_POST);
		$result['payment'] = $this->validate->checkAmount($payment);
		$result['artistID'] = $this->validate->checkSelect($artistID);
		$result['password'] = $this->validate->checkPassword($password);	
		$result['ok'] = $this->validate->checkErrorMessages($result);
		if ($result['password'] == '') {
			$identity = $this->getCustomer($_SESSION['email'], $password);
			if (is_array($identity)) {
				$result['password'] = '';
				$result['ok'] = true;
			}
			else {
				$result['password'] = "\t\t\t\t\t\t".'<p class="error">Sorry, your password is incorrect</p>';
				$result['ok'] = false;
			}
		}
		return $result;
	}


	// displays each dispatched item as a royalty items
	// $filter 	= 'month' or ''.
	// returns $html of of data in tabular format
	public function displayEachRoyalty($dispatched, $filter) {
		$html .= "\t\t\t\t\t".'<div id="all_orders">'."\n";
		$html .= "\t\t\t\t\t\t".'<table>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<tr>'."\n";
		if ($filter = 'month') {
				$html .= "\t\t\t\t\t\t\t\t".'<th>artist</th>'."\n";
			}
		$html .= "\t\t\t\t\t\t\t\t".'<th>artwork</th>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<th>format</th>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<th>qty</th>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<th>royalty<br />amount</th>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<th>royalty<br />total</th>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<th>dispatched</th>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<th>view<br />order</th>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</tr>'."\n";
		foreach ($dispatched as $item) {
			$artDetails = $this->getProductByID($item['artID'], true);
			$total = $item['royaltyAmount'] * $item['orderProdQty'];
			$html .= "\t\t\t\t\t\t\t".'<tr>'."\n";
			if(is_array($artDetails[0])) {
				// need an extra column for artist
				if ($filter = 'month') {
					extract($artDetails[0]);
					$html .= "\t\t\t\t\t\t\t\t".'<td>'.$artDetails[0]['artistName'].'</td>'."\n";
				}
				$html .= "\t\t\t\t\t\t\t\t".'<td>'.$artDetails[0]['artName'].'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td>'.$item['artType'].'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td>'.$item['orderProdQty'].'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td>$'.$item['royaltyAmount'].'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td>$'.sprintf("%01.2f", $total).'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td>'.date('j M y', $item['dateDispatched']).'</td>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<td><a href="index.php?page=order&amp;id='.$item['orderID'].'">view</a></td>'."\n";
				$html .= "\t\t\t\t\t\t\t".'</tr>'."\n";
				$sumTotal = $sumTotal + $item['orderProdQty'];
				$grandTotal = $grandTotal + $total;
			}
		}
		$paid = $this->royaltiesPaid($item['artistID']);
		if (is_array($paid)) {
			foreach ($paid as $payment) {
				extract($payment);
				$totalPayment = $totalPayment + $amountPaid;
			} 
		}
		else {
			$totalPayment = 0;
		}
		$balanceToPay = $grandTotal - $totalPayment;
		$html .= "\t\t\t\t\t\t\t".'<tr>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td class="border">total:</td>'."\n";
		if ($filter = 'month') {
				$html .= "\t\t\t\t\t\t\t\t".'<td class="border"></td>'."\n";
		}	
		$html .= "\t\t\t\t\t\t\t\t".'<td class="border"></td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td class="border">'.$sumTotal.'</td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td class="border"></td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td class="border">$'.sprintf("%01.2f", $grandTotal).'</td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td class="border"></td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td class="border"></td>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</tr>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<tr>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td class="green">amount paid:</td>'."\n";
		if ($filter = 'month') {
			$html .= "\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		}
		$html .= "\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td class="green">$'.sprintf("%01.2f", $totalPayment).'</td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</tr>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td class="red">balance to pay:</td>'."\n";
		if ($filter = 'month') {
			$html .= "\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		}
		$html .= "\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td class="red">$'.sprintf("%01.2f", $balanceToPay).'</td>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<td></td>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</tr>'."\n";
		$html .= "\t\t\t\t\t\t".'</table>'."\n";
		$html .= "\t\t\t\t\t".'</div>'."\n";
		return $html;
	}
}

?>
