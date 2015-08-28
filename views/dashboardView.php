<?php

class DashboardView extends View
{
	// this view shows order and profile info for logged in admin
	
	protected function displayContent()  {
		// redirect user if not logged in as admin
		if ($_SESSION['access'] != 'admin') {
			header("Location: index.php");
		}
		$html = $this->model->displaySideBarOptions();
		$html .= "\t\t\t\t".'<div id="content">'."\n";
		$html .= "\t\t\t\t\t".'<h6><a href="#account_form">update your account information</a> | <a href="#password_form">change your password</a></h6>'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageHeading'].'</h2>'."\n";
		$section = '';
		$section .= $this->displayIncompleteOrders();
		$section .= $this->displayAccountRequests();
		$section .= $this->displayOutstandingBalance();
		if ($section == '') {
			$html .= "\t\t\t\t\t".'<h3>you have no new orders or account requests</h3>'."\n";
			$html .= "\t\t\t\t\t".'<p class="validation"><a href="http://www.artprints.kiwi.nz/index.php?page=adminartist&add=1">add an artist</a> | <a href="http://www.artprints.kiwi.nz/index.php?page=adminartwork&add=1">add an artwork</a> | <a href="http://www.artprints.kiwi.nz/index.php?page=banner&a=a">add a banner</a></p>'."\n";
		}
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}
			
		
	// displays orders that are awaiting dispatch
	private function displayIncompleteOrders() {	
		$incompleteOrders = $this->model->getOrders('pending');
		if (is_array($incompleteOrders)) {
			$html = $this->model->displayOrders($incompleteOrders, '', 'awaiting dispatch');
		}
		return $html;
	}


	// displays all orders that have an outstanding balance
	private function displayOutstandingBalance() {
		$outstandingBalanceOrders = $this->model->getOrders('money');
		if (is_array($outstandingBalanceOrders)) {
			$html = $this->model->displayOrders($outstandingBalanceOrders, '', 'money outstanding');
		}
		return $html;
	}


	// displays new customer account requests for confirmation/declination
	private function displayAccountRequests() {	
		if (isset($_POST['custID'])) {
			$this->model->updateAccountStatus();
		}
		$newAccounts = $this->model->getNewAccounts();
		$html = '';
		if (is_array($newAccounts)) {
			$html .= "\t\t\t\t".'<div id="new_account_requests">'."\n";
			$html .= "\t\t\t\t\t".'<h3>new account requests</h3>'."\n";
			foreach ($newAccounts as $account) {
				extract($account);
				$html .= "\t\t\t\t\t".'<form method="post" class="form" action="'.htmlentities($_SERVER['REQUEST_URI']).'">'."\n";
				$html .= "\t\t\t\t\t\t".'<input type="hidden" name="custID" value="'.$custID.'" />'."\n"; 
				$html .= "\t\t\t\t\t\t".'<input type="hidden" name="email" value="'.$custEmail.'" />'."\n"; 
				$html .= "\t\t\t\t\t\t".'<input type="hidden" name="phone" value="'.$custPhone.'" />'."\n"; 
				$html .= "\t\t\t\t\t\t".'<input type="hidden" name="first_name" value="'.$custFirstName.'" />'."\n";
				$html .= "\t\t\t\t\t\t".'<input type="hidden" name="last_name" value="'.$custLastName.'" />'."\n";
				$html .= "\t\t\t\t\t\t".'<input type="hidden" name="company" value="'.$custCompany.'" />'."\n"; 
				$html .= "\t\t\t\t\t\t".'<p>'.$custCompany.'</p>';
				$html .= "\t\t\t\t\t\t".'<p><strong>contact details</strong><br />'."\n";
				$html .= "\t\t\t\t\t\t".$custFirstName.' '.$custLastName.', '.$custPhone.', '.$custEmail.'</p>';
				$html .= "\t\t\t\t\t\t".'<p><strong>delivery address</strong><br />'."\n";
				$html .= "\t\t\t\t\t\t".$custDelAddress.', '.$custDelSuburb.', '.$custDelCity.', '.$custDelPostcode.', '.$custDelCountry.'</p>';
				$html .= "\t\t\t\t\t\t".'<p><strong>billing address</strong><br />'."\n";
				$html .= "\t\t\t\t\t\t".$custBillAddress.', '.$custBillSuburb.', '.$custBillCity.', '.$custBillPostcode.', '.$custBillCountry.'</p>';
				$html .= "\t\t\t\t\t\t".'<input type="submit" class="button" name="approve_cust" value="Approve" />'."\n";
				$html .= "\t\t\t\t\t\t".'<input type="submit" class="button" name="decline_cust" value="Decline" />'."\n";
				$html .= "\t\t\t\t\t".'</form>'."\n";
			}
			$html .= "\t\t\t\t".'</div><!-- incomplete_orders -->'."\n";
		}
		return $html;
	}


	// displays change password form
	private function displayPasswordForm() {
		$html .= "\t\t\t\t\t".'<h3>change your password</h3>'."\n";
		$html .= "\t\t\t\t\t".'<form id="password_form" action="account.html#password_form" method="post" class="form">'."\n";
		$html .= "\t\t\t\t\t\t".'<label for="password">old password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password" />'."\n";
		$html .= "\t\t\t\t\t\t".'<label for="password1">new password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password1" />'."\n";
		$html .= "\t\t\t\t\t\t".'<label for="password2">confirm new password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password2" />'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="submit" name="password" value="change" />'."\n";
		$html .= "\t\t\t\t\t".'</form>'."\n";
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}
}
?>