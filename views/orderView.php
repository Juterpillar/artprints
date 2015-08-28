<?php

class OrderView extends View
{
	// this view shows order and profile info for the logged in customer
	
	protected function displayContent()  {
		$id = $_GET['id'];
		if ($id == '' || !isset($_SESSION['access'])) {
			header("Location: index.php");
		}

		// redirect user if not logged in as admin or customer who placed the order
		$width = '';
		if ($_SESSION['access'] == 'admin') {
			$html .= $this->model->displaySideBarOptions();
			$width = 'width';
		}
		// check the order belongs to the logged in customer
		else {
			// get all the orders for that customer
			$custID = $_SESSION['custID'];
			$orders = $this->model->getPastOrders($custID);
			$owner = 0;
			foreach ($orders as $order) {
				if ($order['orderID'] == $id) {
					$owner = $owner + 1;
				}
			}
			if ($owner < 1) {
				header("Location: index.php");
			}
		}
		$html .= "\t\t\t\t".'<div id="content" class="'.$width.'">'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">Order #'.$id.'</h2>'."\n";
		if (isset($_POST['update'])) {
			$result = $this->model->checkPassword($_POST['password']);
			if ($result['ok']) {
				$update = $this->model->updatePayment();
				if ($update == true) {
					$html .= "\t\t\t\t\t".'<h3 class="validation green">You have successfully updated this order</h3>'."\n";
				}
			}
			else {
				$html .= "\t\t\t\t\t".'<h3 class="validation red">Please correct your password</h3>'."\n";
				$html .= $this->updatePaymentForm($id);
				$html .= $this->model->reviewOrder($id, $custID);
				$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
				return $html;
			}
		}
		$html .= $this->model->reviewOrder($id, $custID);
		$html .= $this->updatePaymentForm($id);
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}

	// this form allows logged in admin to amend the following details on an order:
	// * amount paid
	// * shipping cost
	// * delivery status
	// * courier number
	// * order notes
	private function updatePaymentForm($id) {
		if ($_SESSION['access'] == 'admin') {
			$order = $this->model->getOrders($id);
			extract($order[0]);
			$html .= "\t\t\t\t\t".'<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">'."\n";
			$html .= "\t\t\t\t\t\t".'<h3>update order details</h3>'."\n";
			$html .= "\t\t\t\t\t\t".'<input type="hidden" name="order_value" value="$'.sprintf("%01.2f", $orderValue).'" />'."\n";
			$html .= "\t\t\t\t\t\t".'<input type="hidden" name="order_id" value="'.$id.'" />'."\n";
			$html .= "\t\t\t\t\t\t".'<label for="payment">total payment</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<input type="text" name="payment" value="$'.sprintf("%01.2f", $orderPaymentRec).'"/>'."\n";
			$html .= "\t\t\t\t\t\t".'<label for="shipping">shipping</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<input type="text" name="shipping" value="$'.sprintf("%01.2f", $orderShipValue).'"/>'."\n";
			$html .= "\t\t\t\t\t\t".'<label for="delivery_status">delivery status</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<select name="delivery_status">'."\n";
			$html .= "\t\t\t\t\t\t\t".'<option value="0">Please select...</option>'."\n";
			$dispatchStatus = array('dispatched', 'pending', 'cancelled', 'delayed');
			foreach ($dispatchStatus as $status) {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$status.'"';
				if ($status == $orderDelStatus) {
					$html .= ' selected="selected"';
				}
				$html .= '>'.$status.'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
			$html .= "\t\t\t\t\t\t".'<label for="courier">courier number</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<input type="text" name="courier" value="'.htmlentities(stripslashes($orderCourierNum)).'"/>'."\n";
			$html .= "\t\t\t\t\t\t".'<label for="notes">order notes</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<textarea rows="6" cols="40" name="notes">'.htmlentities(stripslashes($orderNotes)).'</textarea>'."\n";
			$html .= "\t\t\t\t\t\t".'<label for="password">password</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<input type="password" name="password" />'."\n";
			$html .= $error['password'];
			$html .= "\t\t\t\t\t\t".'<input type="submit" name="update" value="update" />'."\n";
			$html .= "\t\t\t\t\t".'</form>'."\n";
			return $html;
		}
	}

}
?>