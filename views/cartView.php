<?php
class CartView extends View
{
	// this class displays the items in the cart
	// it allows users to delete items from cart or alter the size/message for an item

	protected function displayContent() {
		if (!isset($_SESSION['custID'])) {
			header("Location: index.php");
		}
		unset($_POST);
		$html = "\t\t\t\t".'<div id="content">'."\n";
		// if over credit limit
		$credit = $this->model->calcCreditDets($_SESSION['credit'], $_SESSION['custID']);
		if ($credit == 0) {
			$html .= "\t\t\t\t\t".'<div class="success brown" id="account_limit"><p>sorry, you cannot place a new order as you are at your credit limit</p>'."\n";
			$html .= "\t\t\t\t\t".'</div>'."\n";
		}
		$html .= "\t\t\t\t\t".'<a id="print" class="no_mobile" href="#" onclick="window.print();return false;"><i class="icon-print"></i> print</a>'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageHeading'].'</h2>'."\n";
	 	// get the items in the cart and show value them
	 	$cartContents = $this->cartObj->getCartContents();
	 	if (is_array($cartContents)) {
			$count = $this->cartObj->getCartQuantity();
			$subtotal = $this->cartObj->getTotalCartAmount();
			$shipping = $this->cartObj->calculateShipping();
			$total = $shipping + $subtotal;
			$html .= "\t\t\t\t\t\t".'<h4 id="value">$'.sprintf('%.2f', $subtotal).' for '.$count.' items + shipping $'.sprintf('%.2f', $shipping).' Total spend $'.sprintf('%.2f', $total).' (excl gst)</h4>'."\n";
			$html .= "\t\t\t\t\t\t".'<a href="index.php?page=prints"><button><i class="icon-shopping-cart"></i> continue shopping</button></a>'."\n";
			if ($credit == 0) {
				$html .= "\t\t\t\t\t\t".'<a href="index.php?page=checkout"><button class="disabled checkout_button"><i class="icon-credit-card"></i> proceed to checkout</button></a>'."\n";
			}
			else {
				$html .= "\t\t\t\t\t\t".'<a href="index.php?page=checkout"><button class="checkout_button"><i class="icon-credit-card"></i> proceed to checkout</button></a>'."\n";
			}
			// if over 50 show warning
			if ($shipping > 50) {
				$html .= "\t\t\t\t\t\t".'<div id="excess_ship"><p>You\'re spending more than $50 on shipping.  You may proceed with your order now and we will contact you and adjust your shipping charges before your order is dispatched if we can find a more cost effective option.  Or, call us now to discuss your options.</p></div>'."\n";
			}
		}
		// there are no items in cart
		else {
			$html .= "\t\t\t\t".'<p class="shop_link" id="start_shopping">Your cart is empty!<br /> <a href="index.php?page=prints">Start Shopping</a></p>'."\n";
		}	
		//display the items in the cart
		if (is_array($cartContents)) {
			$html .= $this->displayCartContents($cartContents);
			if (count($cartContents > 4 || $this->device['type'] == 'mobile')) {
				$html .= $this->displayButtonLinks();
				$html .= "\t\t\t\t\t\t".'<h4 id="value">$'.sprintf('%.2f', $subtotal).' for '.$count.' items + shipping $'.sprintf('%.2f', $shipping).' Total spend $'.sprintf('%.2f', $total).' (excl gst)</h4>'."\n";

			}
		}
		$html .= "\t\t\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}


	// this function displays each item in the cart using the $cartContents array
	private function displayCartContents($cartContents) {
		$html .= "\t\t\t\t".'<div class="icon_nav">'."\n";
		foreach($cartContents as $product) {
			// something has gone wrong with the query or that price does not exist for the product id (unlikely except in development or if a product is updated)
			if ($product == 'No matches') {
				unset($_SESSION['cart']);
				$html .= "\t\t\t\t".'<h3>Sorry, there was a problem with one of the items in your cart.  Unfortunately we had to empty your cart to remedy this problem. We are very sorry for the inconvenience caused.</h3>'."\n";
				$html .= "\t\t\t\t".'<p class="shop_link" id="start_shopping"><a id="start_shopping" href="index.php?page=shop">Start Shopping</a></p>'."\n";
			}
			// the product details have been colleted in cartContents properly
			else {
				extract($product);
				// calculations for display		
				$sizeID = $sizeInfo['sizeId'];
				$quantity = $_SESSION['cart'][$sizeID]['orderProdQty'];
				$price = sprintf("%01.2f", $sizeInfo['sizeWSP']);
				$subtotal = $quantity * $price;
				$shadow = '';
				if ($artInfo['artType'] != 'canvas') {
					$shadow = ' class="image_shadow"';
				}
				// display
				$html .= "\t\t\t\t\t".'<figure class="product_set">'."\n";
				$html .= "\t\t\t\t\t\t\t".'<form action="'.$_SERVER['REQUEST_URI'].'" method="post">'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<input type="hidden" value="'.$sizeID.'" name="sizeID" />'."\n";
				$html .= "\t\t\t\t\t\t\t".'<a href="index.php?page=product&id='.$artInfo['artID'].'">'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<img'.$shadow.' src="images/thumbs/'.$artInfo['artImage'].'" title="view details" alt="'.$artInfo['artName'].'" /> </a>'."\n";
				$html .= "\t\t\t\t\t\t\t".'<figcaption>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<h4>'.$artInfo['artName'].'</h4>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<h6>'.$artInfo['artistName'].'</h6>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<p>'.$artInfo['artType'].'</p>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<p>'.$sizeInfo['sizeWidth'].'mm x '.$sizeInfo['sizeHeight'].'mm</p>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<p>'.$quantity.' @ $'.sprintf("%01.2f", $price).'</p>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<h4>subtotal $'.sprintf("%01.2f", $subtotal).'</h4>'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<input type="submit" id="delete" class="quantity_adjust" name="delete" value="" />'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<input type="submit" id="minus" class="quantity_adjust" name="minus" value="" />'."\n";
				$html .= "\t\t\t\t\t\t\t\t".'<input type="submit" id="add" class="quantity_adjust" name="add" value="" />'."\n";
				$html .= "\t\t\t\t\t\t\t".'</figcaption>'."\n";
				$html .= "\t\t\t\t\t\t".'</form>'."\n";
				$html .= "\t\t\t\t\t".'</figure> <!-- product_set -->'."\n";
			}
		}
		$html .= "\t\t\t\t".'</div> <!-- icon_nav -->'."\n";
		return $html;
	}	

	private function displayButtonLinks() {
		$html .= "\t\t\t\t\t".'<a href="index.php?page=prints"><button><i class="icon-shopping-cart"></i> continue shopping</button></a>'."\n";
		$html .= "\t\t\t\t\t".'<a href="index.php?page=checkout"><button class="checkout_button"><i class="icon-credit-card"></i> proceed to checkout</button></a>'."\n";
		return $html;

	}

}
				
?>