<?php

class AccountView extends View
{
	// this view shows order and profile info for the logged in customer
	
	protected function displayContent()  {

		// ---------------------------------------------------------------------------------------- //
		// ------------------------------- if logged in as admin ---------------------------------- //
		// ---------------------------------------------------------------------------------------- //

		if ($_SESSION['access'] == 'admin') {
			$html = $this->model->displaySideBarOptions();
			$html .= "\t\t\t\t".'<div id="content" class="width">'."\n";
			
			// if clicked to update password
			if (isset($_GET['p'])) {
				if (isset($_POST['password'])) {
					$result = $this->model->validatePasswordForm();
					// if no errors then update the customer in the db
					if ($result['ok']) {
						$update = $this->model->updatePassword();
						if ($update == true) {
							$html .= "\t\t\t\t".'<h3 class="validation green">your password has been successfully changed</h3>'."\n";
							unset($_POST);
							unset($_GET);
							$html .= "\t\t\t\t\t".'<h2 class="title">manage customer accounts</h2>'."\n";
							$html .= $this->model->displaySelect('customers', 'choose a customer from the select box below to edit their details', 'index.php?page=account');
						}
						// error with update
						else {
							$html .= "\t\t\t\t".'<h3 class="validation red">sorry, your password was not changed</h3>'."\n";
							$html .= $this->displayPasswordForm($result);
						}
					}
					// validation errors
					else {
						$html .= "\t\t\t\t".'<h3 class="validation red">please correct the errors below</h3>'."\n";
						$html .= $this->displayPasswordForm($result);
					}	
				}
				else {
					$html .= $this->displayPasswordForm();
				}
			}
			// if submitted select customer form
			else if (isset($_POST['customer_select']) && $_POST['customers'] != 0) {
				$customer = $this->model->collectCustomer($_POST['customers']);
				$html .= $this->displayAccountForm($customer);
			}
			// if submitted account update form
			else if (isset($_POST['update'])) {
				$result = $this->model->validateAccountForm();
				// if no errors then update the customer in the db
				if ($result['ok']) {
					$update = $this->model->updateCustomer();
					// successfully updated
					if ($update == 'success') {
						$html .= "\t\t\t\t".'<h3 class="validation green">account update was successful</h3>'."\n";
						unset($_POST);
						unset($_POST);
							unset($_GET);
							$html .= "\t\t\t\t\t".'<h2 class="title">manage customer accounts</h2>'."\n";
							$html .= $this->model->displaySelect('customers', 'choose a customer from the select box below to edit their details', 'index.php?page=account');
					}
					else {
						$html .= "\t\t\t\t\t".'<h3 class="validation red">sorry, update was not successful</h3>'."\n";
						$html .= $this->displayAccountForm('', $result);
						$html .= $this->model->displayOrders('', $_SESSION['custID'], 'overview of past orders');
						$html .= $this->displayPasswordForm();
					}
				}
				// validation errors
				else {
					$html .= "\t\t\t\t\t".'<h3 class="validation red">please correct the errors below</h3>'."\n";
					$html .= $this->displayAccountForm('', $result);
				}
			}
			else {
				$html .= "\t\t\t\t\t".'<h2 class="title">manage customer accounts</h2>'."\n";
				$html .= $this->model->displaySelect('customers', 'choose a customer from the select box below to edit their details');
			}
			 $html .= "\t\t\t\t".'</div><!-- content -->'."\n";
			return $html;
		}

		// ---------------------------------------------------------------------------------------- //
		// ---------------------------- if logged in as a customer -------------------------------- //
		// ---------------------------------------------------------------------------------------- //
		

		else if ($_SESSION['access'] == 'customer') {
			$html = "\t\t\t\t".'<div id="content">'."\n";
			// if over credit limit
			$credit = $this->model->calcCreditDets($_SESSION['credit'], $_SESSION['custID']);
			if ($credit == 0) {
				$html .= "\t\t\t\t\t".'<div class="success brown" id="account_limit"><p>sorry, you cannot place a new order as you are at your credit limit</p>'."\n";
				$html .= "\t\t\t\t\t".'</div>'."\n";
			}
			$html .= "\t\t\t\t\t".'<h6 id="account_links"><a href="#account_form">update your account information</a> | <a href="#password_form">change your password</a></h6>'."\n";
			$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageHeading'].'</h2>'."\n";
			// if submitted form to update details
			if (isset($_POST['update'])) {
				$result = $this->model->validateAccountForm();
				// if no errors then update the customer in the db
				if ($result['ok']) {
					$update = $this->model->updateCustomer();
					// successfully updated
					if ($update == 'success') {
						$html .= "\t\t\t\t".'<h3 class="validation green">your account has been updated</h3>'."\n";
						unset($_POST);
						$html .= $this->model->displayOrders('', $_SESSION['custID'], 'overview of past orders');
						$html .= $this->displayPasswordForm($result);
						$html .= $this->displayAccountForm();
					}
					else {
						$html .= "\t\t\t\t\t".'<h3 class="validation red">sorry, update was not successful</h3>'."\n";
						$html .= $this->displayAccountForm('', $result);
						$html .= $this->model->displayOrders('', $_SESSION['custID'], 'overview of past orders');
						$html .= $this->displayPasswordForm();
					}
				}
				// validation errors
				else {
					$html .= "\t\t\t\t\t".'<h3 class="validation red">please correct the errors below</h3>'."\n";
					$html .= $this->displayAccountForm('', $result);
					$html .= $this->model->displayOrders('', $_SESSION['custID'], 'overview of past orders');
					$html .= $this->displayPasswordForm();
				}	
			}
			// form submitted to change password
			else if (isset($_POST['password'])) {
				$result = $this->model->validatePasswordForm();
				// if no errors then update the customer in the db
				if ($result['ok']) {
					$update = $this->model->updatePassword();
					if ($update == true) {
						$html .= "\t\t\t\t".'<h3 class="validation green">your password has been successfully changed</h3>'."\n";
						unset($_POST);
						$html .= $this->displayAccountForm();
						$html .= $this->model->displayOrders('', $_SESSION['custID'], 'overview of past orders');
						$html .= $this->displayPasswordForm($result);
					}
					// error with update
					else {
						$html .= "\t\t\t\t".'<h3 class="validation red">sorry, your password was not changed</h3>'."\n";
					}
				}
				// validation errors
				else {
					$html .= "\t\t\t\t".'<h3 class="validation red">please correct the errors below</h3>'."\n";
					$html .= $this->displayPasswordForm($result);
					$html .= $this->model->displayOrders('', $_SESSION['custID'], 'overview of past orders');
					$html .= $this->displayAccountForm();
				}	
			}
			else {
				$html .= $this->model->displayOrders('', $_SESSION['custID'], 'overview of past orders');
				$html .= $this->displayAccountForm();
				$html .= $this->displayPasswordForm();
			}
			$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
			return $html;
		}
		// no user is logged in
		else  {
			header("Location: index.php");
		}
	}
		
	// update account details form
	private function displayAccountForm($customer='', $error="") {	
		if ($_SESSION['access'] == 'admin' && $customer) {
    		$first = $customer['custFirstName'];
		    $last = $customer['custLastName'];
		    $company = $customer['custCompany'];
		    $delAddress = $customer['custDelAddress'];
		    $delSuburb = $customer['custDelSuburb'];
		    $delCity = $customer['custDelCity'];
		    $delPostcode = $customer['custDelPostcode'];
		    $island1 = $customer['custDelIsland'];
		    $billAddress = $customer['custBillAddress'];
		    $billSuburb = $customer['custBillSuburb'];
		    $billCity = $customer['custBillCity'];
		    $billPostcode = $customer['custBillPostcode'];
		    $phone = $customer['custPhone'];
		    $email = $customer['custEmail'];
		    $deposit = $customer['custDeposit'];
		    $status = $customer['custStatus'];
		    $access = $customer['custAccess'];
		    $notes = $customer['custNotes'];
		    $credit = $customer['custCreditLimit'];
		}
		// admin/customer submited form with errors
		else if ($error) {
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
		    $credit = $_POST['credit'];
		    $password = $_POST['password'];
		    $island1 = $_POST['island'];
		}
		// customer hasn't submitted form
		else {
			extract($_SESSION);
			$customer['custID'] = $custID;
		}
		$html = "\t\t\t\t\t".'<h3 id="update_account">update '.htmlentities(stripslashes($company)).'\'s account details</h3>'."\n";
		$html .= "\t\t\t\t\t".'<form id="account_form" action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">'."\n";
		$html .= "\t\t\t\t\t\t".'<p id="billing">billing information <i class="icon-circle-arrow-down"></i></p>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="hidden" name="custID" value="'.$customer['custID'].'" />'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="billing_form">'."\n";
		// company
		$html .= "\t\t\t\t\t\t\t".'<label for="company_name">company name</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="company_name" value="'.htmlentities(stripslashes($company)).'" />'."\n";
		$html .= $error['company'];
		// first
		$html .= "\t\t\t\t\t\t\t".'<label for="first_name">contact first name</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="first_name" value="'.htmlentities(stripslashes($first)).'" />'."\n";
		$html .= $error['first'];
		// last
		$html .= "\t\t\t\t\t\t\t".'<label for="last_name">contact last name</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="last_name" value="'.htmlentities(stripslashes($last)).'" />'."\n";
		$html .= $error['last'];
		// phone
		$html .= "\t\t\t\t\t\t\t".'<label for="phone">phone</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="phone" value="'.htmlentities(stripslashes($phone)).'" />'."\n";
		$html .= $error['phone'];
		// phone
		$html .= "\t\t\t\t\t\t\t".'<label for="email">email</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="email" name="email" value="'.htmlentities(stripslashes($email)).'" />'."\n";
		$html .= $error['email'];
		// billing address
		$html .= "\t\t\t\t\t\t\t".'<label for="billing_address">billing address</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="billing_address" value="'.htmlentities(stripslashes($billAddress)).'" />'."\n";
		$html .= $error['billAddress'];
		// billing suburb
		$html .= "\t\t\t\t\t\t\t".'<label for="billing_suburb">billing suburb</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="billing_suburb" value="'.htmlentities(stripslashes($billSuburb)).'" />'."\n";
		$html .= $error['billSuburb'];
		// billing postcode
		$html .= "\t\t\t\t\t\t\t".'<label for="billing_postcode">billing postcode</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="billing_postcode" value="'.htmlentities(stripslashes($billPostcode)).'" />'."\n";
		$html .= $error['billCode'];
		// billing city
		$html .= "\t\t\t\t\t\t\t".'<label for="billing_city">billing city</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="billing_city" value="'.htmlentities(stripslashes($billCity)).'" />'."\n";
		$html .= $error['billCity'];
		// billing country
		$html .= "\t\t\t\t\t\t\t".'<label for="billing_country">billing country</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="billing_country" value="New Zealand" disabled="disabled" />'."\n";
		$html .= "\t\t\t\t\t\t".'</div>'."\n";
		$html .= "\t\t\t\t\t\t".'<p id="shipping">shipping information <i class="icon-circle-arrow-down"></i></p>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="shipping">'."\n";
		// delivery address
		$html .= "\t\t\t\t\t\t\t".'<label for="delivery_address">delivery address</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="delivery_address" value="'.htmlentities(stripslashes($delAddress)).'" />'."\n";
		$html .= $error['delAddress'];
		// delivery suburb
		$html .= "\t\t\t\t\t\t\t".'<label for="delivery_suburb">delivery suburb</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="delivery_suburb" value="'.htmlentities(stripslashes($delSuburb)).'" />'."\n";
		$html .= $error['delSuburb'];
		// delivery postcode
		$html .= "\t\t\t\t\t\t\t".'<label for="delivery_postcode">delivery postcode</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="delivery_postcode" value="'.htmlentities(stripslashes($delPostcode)).'" />'."\n";
		$html .= $error['delCode'];
		// delivery city
		$html .= "\t\t\t\t\t\t\t".'<label for="delivery_city">delivery city</label>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<input type="text" name="delivery_city" value="'.htmlentities(stripslashes($delCity)).'" />'."\n";
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
		$html .= "\t\t\t\t\t\t".'<label for="notes">customer notes</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<textarea rows="5" cols="40" name="notes">'.htmlentities(stripslashes($notes)).'</textarea>'."\n";
		$html .= "\t\t\t\t\t\t".'</div>'."\n";
		if ($_SESSION['access'] == 'admin') {
			// account status
			$html .= "\t\t\t\t\t\t\t".'<label for="status">account status</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<select name="status">'."\n";
			$statuses = array('frozen', 'inactive', 'active');
			foreach ($statuses as $aStatus) {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$aStatus;
				if ($status == $aStatus) {
					$html .= '" selected="selected';
				}
				$html .= '">'.$aStatus.'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
			// deposit required
			$html .= "\t\t\t\t\t\t\t".'<label for="deposit">deposit required (%)</label>'."\n";
			$html .= "\t\t\t\t\t\t\t".'<input type="number" name="deposit" value="'.$deposit.'" />'."\n";
			// access
			$html .= "\t\t\t\t\t\t\t".'<label for="access">account access</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<select name="access">'."\n";
			$accesses= array('admin', 'customer');
			foreach ($accesses as $aAccess) {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$aAccess;
				if ($access == $aAccess) {
					$html .= '" selected="selected';
				}
				$html .= '">'.$aAccess.'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
			// credit limit
			$html .= "\t\t\t\t\t\t".'<label for="limit">credit limit $</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<input type="text" name="credit" value="'.$credit.'" />'."\n";
			$html .= $error['credit'];	
			// reset password
			$html .= "\t\t\t\t\t\t".'<label id="reset_label" for="reset">reset password</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<input id="reset" type="text" name="reset" />'."\n";	
			$html .= $error['reset'];
		// last
		}
		// password
		$html .= "\t\t\t\t\t\t".'<label for="password"> your password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password" />'."\n";
		$html .= $error['password'];
		// last
		$html .= "\t\t\t\t\t\t".'<input type="submit" name="update" value="update account">'."\n";
		$html .= "\t\t\t\t\t".'</form>'."\n";
		return $html;
	}

	// change password form
	private function displayPasswordForm($error='') {
		$html = "\t\t\t\t\t".'<h3>change your password</h3>'."\n";
		$html .= "\t\t\t\t\t".'<form id="password_form" action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">'."\n";
		$html .= "\t\t\t\t\t\t".'<label for="o_password">old password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="o_password" value="'.$_POST['o_password'].'" />'."\n";
		$html .= $error['password'];
		$html .= "\t\t\t\t\t\t".'<label for="password1">new password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password1" />'."\n";
		$html .= $error['password1'];
		$html .= "\t\t\t\t\t\t".'<label for="password2">confirm new password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password2" />'."\n";
		$html .= $error['password2'];
		$html .= "\t\t\t\t\t\t".'<input type="submit" name="password" value="change" />'."\n";
		$html .= "\t\t\t\t\t".'</form>'."\n";
		return $html;
	}
}
?>