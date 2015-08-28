<?php

class ordersView extends View
{
	// this view shows order and profile info for the logged in customer
	
	protected function displayContent()  {
		// redirect user if not logged in as admin
		if ($_SESSION['access'] != 'admin') {
			header("Location: index.php");
		}
		$html = '';
		$html .= $this->model->displaySideBarOptions();
		$html .= "\t\t\t\t".'<div id="content">'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">orders</h2>'."\n";
		

		switch ($_GET['o']) {
	    // months
	    case 'm':
	        if (isset($_POST['date_select'])) {
	        	$month = $_POST['month'];
	        	$year = $_POST['year'];
	    		$orders = $this->model->getOrders('month');
	    		$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	    		if (is_array($orders)) {
	    			$title = 'orders for '.$months[$month].' '.$year;
	    			$html .= $this->model->displayOrders($orders, '', $title);
	    		}
	    		else {
	    			$html .= "\t\t\t\t\t".'<h3>there are no orders for '.$months[$month].' '.$year.'</h3>'."\n";
	    			$html .= $this->model->displaySelect('months', 'choose a month and year from the select boxes below');
	    		}
	    		
	    	}
	    	// display the form to select a month
	    	else {
	        	$html .= $this->model->displaySelect('months', 'choose a month and year from the select boxes below');
	        }break;
	    // customer
	    case 'c':
	    	// a customer has been selected to view their items
	    	if (isset($_POST['customer_select'])) {
	    		$customer = explode(':', $_POST['customers']);
	    		$id = $customer[0];
				$company = $customer[1];
	    		$orders = $this->model->getPastOrders($id);
	    		if (is_array($orders)) {
	    			$title = 'orders by '.htmlentities(stripslashes($company));
	    			$html .= $this->model->displayOrders($orders, '', $title);
	    		}
	    		else {
	    			$html .= "\t\t\t\t\t".'<h3>'.htmlentities(stripslashes($company)).' has not placed any orders</h3>'."\n";
	    			$html .= $this->model->displaySelect('customers', 'choose a customer from the select box below to view their orders');
	    		}
	    		
	    	}
	    	// display form to select a customer from
	    	else {
	        	$html .= $this->model->displaySelect('customers', 'choose a customer from the select box below to view their orders');
	        }
	        break;
	    
	    // awaiting dispatch
	    case 'd':
	        $orders = $this->model->getOrders('pending');
	        if (is_array($orders)) {
				$html .= $this->model->displayOrders($orders, '', 'awaiting dispatch');
			}
			else {
	    		$html .= "\t\t\t\t\t".'<h3>there are no orders awaiting dispatch</h3>'."\n";
	    	}	    		
	        break;
	    
	    // money outstanding
	    case 'o':
	    	$orders = $this->model->getOrders('money');
	        if (is_array($orders)) {
				$html .= $this->model->displayOrders($orders, '', 'money outstanding');
			}
			else {
	    		$html .= "\t\t\t\t\t".'<h3>there are no orders with money outstanding</h3>'."\n";
	    	}	
	        break;
		}
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}
		
}
?>