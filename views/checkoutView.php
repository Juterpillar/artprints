<?php

class CheckoutView extends View
{
	protected function displayContent()
	{
		
		if (!$_SESSION['cart']) {
			header("Location: index.php");
		}
		$html = "\t\t\t\t".'<div id="content">'."\n";
				// if over credit limit
		$credit = $this->model->calcCreditDets($_SESSION['credit'], $_SESSION['custID']);
		if ($credit == 0) {
			$html .= "\t\t\t\t\t".'<h2 class="title red">sorry, you cannot place a new order</h2>'."\n";
			$html .= "\t\t\t\t\t".'<h4 class="validation">You have reached your credit limit.  Please contact us by phone on 0800 101015 to place this order</h4>'."\n";
			$html .= "\t\t\t\t\t".'</div>'."\n";
			return $html;
		}
		else {
			$shipping = $this->cartObj->calculateShipping();
			// if clicked to confirm an order
			if ($_POST['order']) {
				// validate the form
				$result = $this->model->validateAccountForm();
					// if no errors then update the customer in the db
					if ($result['ok']) {
						$update = $this->model->updateCustomer();
						$order = $this->model->createOrder();
						// successfully updated
						if (is_numeric($order)) {
						//	$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageTitle'].'</h2>'."\n";
							$html .= "\t\t\t\t".'<h2 class="title green">Thank you! Your order has been placed</h2>'."\n";
							$html .= "\t\t\t\t".'<p>A confirmation email has been sent to '.$_SESSION['email'].'.  You can check on your order at anytime by logging in and going to <a href="index.php?page=account">\'your account\'</a>. page.</p>'."\n";
							$html .= $this->model->reviewOrder($order);
							unset($_POST);
						}
						else {
							$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageTitle'].'</h2>'."\n";
							$html .= "\t\t\t\t\t".'<h3 class="validation red">Sorry, your order was not placed. Please contact us to place your order.</h3>'."\n";
							$html .= $this->model->reviewOrder('', '', $shipping);
							$html .= $this->displayShippingForm($result);
						}
					}
					// validation errors
					else {
						$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageTitle'].'</h2>'."\n";
						$html .= "\t\t\t\t\t".'<h3 class="validation red">please correct the errors below</h3>'."\n";
						$html .= $this->model->reviewOrder('', '', $shipping);
						$html .= $this->displayShippingForm($result);
					}	

			}
			else {
				$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageTitle'].'</h2>'."\n";
				// if over 50 show warning
				if ($shipping > 50) {
					$html .= "\t\t\t\t\t\t".'<div id="excess_ship"><p>You\'re spending more than $50 on shipping.  You may proceed with your order now and we will contact you and adjust your shipping charges before your order is dispatched if we can find a more cost effective option.  Or, call us now to discuss your options.</p></div>'."\n";
				}
				$html .= $this->model->reviewOrder('', '', $shipping);
				$html .= $this->displayShippingForm();
			}
			$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
			return $html;
		}
	}

		// this function displays the registration form and shows any validation errors if submitted
	private function displayShippingForm($error="") {
		if ($error) {
			$customer['custID'] = $_POST['custID']; 
		    $company = $_POST['company_name'];
		    $first = $_POST['first_name'];
		    $last = $_POST['last_name'];
		    $phone = $_POST['phone'];
		    $email = $_POST['email'];
		    $billAddress = $_POST['billing_address'];
		    $billSuburb = $_POST['billing_suburb'];
		    $billCity = $_POST['billing_city'];
		    $billPostcode = $_POST['billing_postcode'];
		    $delAddress = $_POST['delivery_address'];
		    $delSuburb = $_POST['delivery_suburb'];
		    $delCity = $_POST['delivery_city'];
		    $delPostcode = $_POST['delivery_postcode'];
		    $notes = $_POST['notes'];
 			$status = $_POST['status'];
		    $deposit = $_POST['deposit'];
		    $access = $_POST['access'];
		    $reset = $_POST['reset'];
		    $password = $_POST['password'];
		    $island1 = $_POST['island'];
		    $purchase_order = $_POST['purchase_order'];
		}
		// customer hasn't submitted form
		else {
			extract($_SESSION);
			$customer['custID'] = $custID;
		}
		$html .= "\t\t\t\t\t".'<form id="account_form" action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">'."\n";
		$html .= "\t\t\t\t\t\t".'<h3>grand total: $'.sprintf("%01.2f", $_SESSION['total']).' NZD</h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<p id="billing">billing information <i class="icon-circle-arrow-down"></i></p>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="hidden" name="custID" value="'.$customer['custID'].'" />'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="billing_form">'."\n";
		// company
		$html .= "\t\t\t\t\t\t\t".'<label for="company_name">company name</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="company_name" value="'.$company.'" />'."\n";
		$html .= $error['company'];
		// first
		$html .= "\t\t\t\t\t\t\t".'<label for="first_name">contact first name</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="first_name" value="'.$first.'" />'."\n";
		$html .= $error['first'];
		// last
		$html .= "\t\t\t\t\t\t\t".'<label for="last_name">contact last name</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="last_name" value="'.$last.'" />'."\n";
		$html .= $error['last'];
		// #purchase order
		$html .= "\t\t\t\t\t\t\t".'<label for="purchase_order">purchase order</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="purchase_order" value="'.$purchase_order.'" />'."\n";
		// phone
		$html .= "\t\t\t\t\t\t\t".'<label for="phone">phone</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="phone" value="'.$phone.'" />'."\n";
		$html .= $error['phone'];
		// phone
		$html .= "\t\t\t\t\t\t\t".'<label for="email">email</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="email" name="email" value="'.$email.'" />'."\n";
		$html .= $error['email'];
		// billing address
		$html .= "\t\t\t\t\t\t\t".'<label for="billing_address">billing address</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="type" name="billing_address" value="'.$billAddress.'" />'."\n";
		$html .= $error['billAddress'];
		// billing suburb
		$html .= "\t\t\t\t\t\t\t".'<label for="billing_suburb">billing suburb</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="billing_suburb" value="'.$billSuburb.'" />'."\n";
		$html .= $error['billSuburb'];
		// billing postcode
		$html .= "\t\t\t\t\t\t\t".'<label for="billing_postcode">billing postcode</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="billing_postcode" value="'.$billPostcode.'" />'."\n";
		$html .= $error['billCode'];
		// billing city
		$html .= "\t\t\t\t\t\t\t".'<label for="billing_city">billing city</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="billing_city" value="'.$billCity.'" />'."\n";
		$html .= $error['billCity'];
		// billing country
		$html .= "\t\t\t\t\t\t\t".'<label for="billing_country">billing country</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="billing_country" value="New Zealand" disabled="disabled" />'."\n";
		$html .= "\t\t\t\t\t\t".'</div>'."\n";
		$html .= "\t\t\t\t\t\t".'<p id="billing">shipping information <i class="icon-circle-arrow-down"></i></p>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="shipping">'."\n";
		// delivery address
		$html .= "\t\t\t\t\t\t\t".'<label for="delivery_address">delivery address</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="type" name="delivery_address" value="'.$delAddress.'" />'."\n";
		$html .= $error['delAddress'];
		// delivery suburb
		$html .= "\t\t\t\t\t\t\t".'<label for="delivery_suburb">delivery suburb</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="delivery_suburb" value="'.$delSuburb.'" />'."\n";
		$html .= $error['delSuburb'];
		// delivery postcode
		$html .= "\t\t\t\t\t\t\t".'<label for="delivery_postcode">delivery postcode</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="delivery_postcode" value="'.$delPostcode.'" />'."\n";
		$html .= $error['delCode'];
		// delivery city
		$html .= "\t\t\t\t\t\t\t".'<label for="delivery_city">delivery city</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="delivery_city" value="'.$delCity.'" />'."\n";
		$html .= $error['delCity'];
		// island
		$html .= "\t\t\t\t\t\t".'<label for="island">island</label>'."\n";		
		$html .= "\t\t\t\t\t\t".'<select name="island">'."\n";
		$islands = array('North Island', 'South Island');
		foreach ($islands as $island) {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$island.'"';
				if ($island == $island1) {
					$html .= ' selected="selected"';
				}
				$html .= '>'.$island.'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
		$html .= $error['island'];
		// delivery country
		$html .= "\t\t\t\t\t\t\t".'<label for="delivery_country">delivery country</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="delivery_country" value="New Zealand" disabled="disabled" />'."\n";
		// notes
		$html .= "\t\t\t\t\t\t".'<label for="notes">delivery notes</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<textarea rows="5" cols="40" name="notes">'.htmlentities(stripslashes($notes)).'</textarea>'."\n";
		$html .= "\t\t\t\t\t\t".'</div>'."\n";
		
		// password
		$html .= "\t\t\t\t\t\t".'<label for="password"> your password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password" />'."\n";
		$html .= $error['password'];
		// last
		$html .= "\t\t\t\t\t\t".'<input type="submit" name="order" value="confirm order!">'."\n";
		$html .= "\t\t\t\t\t".'</form>'."\n";
		return $html;
	}
}
?>