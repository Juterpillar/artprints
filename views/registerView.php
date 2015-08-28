<?php

class RegisterView extends View
{
	// this class displays the registration form for users to sign up
	
	protected function displayContent()  {
		$html = "\t\t\t\t".'<div id="content">'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">register an account</h2>'."\n";
		// check if the form has been submitted
		if (isset($_POST['register'])) {
			// form submitted so validate
			$result = $this->model->validateRegisterForm();
			if ($result['ok']) {
				// no errors in the form so add to the database
				$add = $this->model->addCustomer();
				// check if added successful
				if ($add == 'added') {
					$html .= "\t\t\t\t\t".'<h3 class="validation">thank you <br />You will recieve an email when you account has been activated.</h3>'."\n";
				} 
				// if that email is already signed up
				else if ($add == 'used') {
					$html .= "\t\t\t\t\t".'<h3 class="validation red">you have already registered with us<br /><a href="index.php?page=contact">Email us</a> if you have forgotten your password or need assistance, otherwise <a href="index.php?page=login">login now</a></h3>'."\n";
				}
				else {
					// wasn't added successsful
					$html .= "\t\t\t\t\t".'<h3 class="validation">Sorry, that did not work for technical reasons.  Please try again or <a href="index.php?page=contact">contact us</a></h3>'."\n";
				}
			// validation returned errors	
			} else {
				$html .= "\t\t".'<h3 class="validation">please correct the following errors</h3>'."\n";
				$html .= $this->displayRegisterForm($result);				}
			}	
		// form hasn't been submitted yet so display form		
		 else {
			$html .= $this->displayRegisterForm();
		}
		return $html;		
	}


	// this function displays the registration form and shows any validation errors if submitted
	private function displayRegisterForm($error="") {
		$html = "\t\t\t\t\t".'<p>You will receive an email from us when your account has been opened.</p>'."\n";
		$html .= "\t\t\t\t\t".'<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">'."\n";
		// business name
		$html .= "\t\t\t\t\t\t".'<label for="company">company name</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="company" value="'.htmlentities(stripslashes($_POST['company'])).'" />'."\n";
		$html .= $error['company'];
		// first name
		$html .= "\t\t\t\t\t\t".'<label for="first_name">contact first name</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="first_name" value="'.htmlentities(stripslashes($_POST['first_name'])).'" />'."\n";
		$html .= $error['first_name'];
		// last name
		$html .= "\t\t\t\t\t\t".'<label for="last_name">contact last name</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="last_name" value="'.htmlentities(stripslashes($_POST['last_name'])).'" />'."\n";
		$html .= $error['last_name'];
		// phone
		$html .= "\t\t\t\t\t\t".'<label for="phone">phone</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="phone" value="'.htmlentities(stripslashes($_POST['phone'])).'" />'."\n";
		$html .= $error['phone'];
		// email
		$html .= "\t\t\t\t\t\t".'<label for="email">email</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="email" name="email" value="'.htmlentities(stripslashes($_POST['email'])).'" />'."\n";
		$html .= $error['email'];
		// billing address
		$html .= "\t\t\t\t\t\t".'<label for="billing_address">billing address</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input id="bAddress" type="type" name="billing_address" value="'.htmlentities(stripslashes($_POST['billing_address'])).'" />'."\n";
		$html .= $error['billing_address'];
		// billing suburb
		$html .= "\t\t\t\t\t\t".'<label for="billing_suburb">billing suburb</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input id="bSuburb" type="type" name="billing_suburb" value="'.htmlentities(stripslashes($_POST['billing_suburb'])).'" />'."\n";
		$html .= $error['billing_suburb'];
		// billing city
		$html .= "\t\t\t\t\t\t".'<label for="billing_city">billing city</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input id="bCity" type="text" name="billing_city" value="'.htmlentities(stripslashes($_POST['billing_city'])).'" />'."\n";
		$html .= $error['billing_city'];
		// billing postcode
		$html .= "\t\t\t\t\t\t".'<label for="billing_postcode">billing postcode</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input id="bCode" type="text" name="billing_postcode" value="'.htmlentities(stripslashes($_POST['billing_postcode'])).'" />'."\n";
		$html .= $error['billing_postcode'];
		// billing country
		$html .= "\t\t\t\t\t\t".'<label for="billing_country">billing country</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="billing_country" disabled="disabled" value="New Zealand" />'."\n";
		$html .= $error['billing_country'];
		// delivery address same as billing
		$html .= "\t\t\t\t\t\t".'<label for="same" class="ck_label">delivery address is the same as above</label>'."\n";
		if ($_POST['same'] == 'on') {
			$html .= "\t\t\t\t\t\t".'<input id="same" type="checkbox" name="same" checked="checked" />'."\n";
		}
		else {
			$html .= "\t\t\t\t\t\t".'<input id="same" type="checkbox" name="same" />'."\n";
		}
		// delivery address
		$html .= "\t\t\t\t\t\t".'<label for="delivery_address">delivery address</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input id="dAddress" type="type" name="delivery_address" value="'.htmlentities(stripslashes($_POST['delivery_address'])).'"/>'."\n";
		$html .= $error['delivery_address'];
		// delivery suburb
		$html .= "\t\t\t\t\t\t".'<label for="delivery_suburb">delivery suburb</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input id="dSuburb" type="type" name="delivery_suburb" value="'.htmlentities(stripslashes($_POST['delivery_suburb'])).'" />'."\n";
		$html .= $error['delivery_suburb'];
		// delivery city
		$html .= "\t\t\t\t\t\t".'<label for="delivery_city">delivery city</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input id="dCity" type="text" name="delivery_city" value="'.htmlentities(stripslashes($_POST['delivery_city'])).'" />'."\n";
		$html .= $error['delivery_city'];
		// delivery postcode
		$html .= "\t\t\t\t\t\t".'<label for="delivery_postcode">delivery postcode</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input id="dCode" type="text" name="delivery_postcode" value="'.htmlentities(stripslashes($_POST['delivery_postcode'])).'" />'."\n";
		$html .= $error['delivery_postcode'];
		// del island	
		$html .= "\t\t\t\t\t\t".'<label for="island">island</label>'."\n";		
		$html .= "\t\t\t\t\t\t".'<select name="island">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<option value="0">Please select...</option>'."\n";
		$islands = array('North Island', 'South Island');
		foreach ($islands as $island) {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$island.'"';
				if ($island == $_POST['island']) {
					$html .= ' selected="selected"';
				}
				$html .= '>'.$island.'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
		$html .= $error['island'];
		// delivery country
		$html .= "\t\t\t\t\t\t".'<label for="delivery_country" class="clear">delivery country</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="delivery_country" disabled="disabled" value="New Zealand" />'."\n";
		$html .= $error['delivery_country'];
		// agree to the terms and conditions
		$html .= "\t\t\t\t\t\t".'<label for="agree" class="ck_label">I agree with the <a href="index.php?page=terms">terms &amp; conditions</a></label>'."\n";
		if ($_POST['agree'] == 'on') {
			$html .= "\t\t\t\t\t\t".'<input type="checkbox" name="agree" checked="checked" />'."\n";
		}
		else {
			$html .= "\t\t\t\t\t\t".'<input type="checkbox" name="agree" />'."\n";
		}
		$html .= $error['agree'];
		// password
		$html .= "\t\t\t\t\t\t".'<label for="password">password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password" value="'.htmlentities(stripslashes($_POST['delivery_password'])).'" />'."\n";
		$html .= $error['password'];
		// confrim password
		$html .= "\t\t\t\t\t\t".'<label for="password1">confirm password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password1" />'."\n";
		$html .= $error['password1'];
		$html .= "\t\t\t\t\t\t".'<input type="submit" name="register" value="register" value="'.htmlentities(stripslashes($_POST['delivery_password'])).'" />'."\n";
		$html .= "\t\t\t\t\t".'</form>'."\n";
		$html .= "\t\t\t\t\t".'<p class="help"><a href="login.html"> I already have an account!</a></p>'."\n";
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}
}
?>