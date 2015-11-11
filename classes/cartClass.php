<?php

// This class contains all the methods for the cart

		
class Cart
{
	public $cartSummary;		//	contains a summary of items in the cart
	private $model;
	
	public function __construct($model) {
		$this->model = $model;

		//check if the user has added something to the cart
		if (isset($_POST['addToCart'])) {
			$this->addToCart();
		}

		// check if clicked to increase an item quantity
		if (isset($_POST['add']) && isset($_POST['sizeID'])) {
			$sizeID = $_POST['sizeID'];
			$savedQuantity = $_SESSION['cart'][$sizeID]['orderProdQty'];
			$_SESSION['cart'][$sizeID]['orderProdQty'] = $savedQuantity + 1;
			unset($_POST['add']);
		}
		// check if clicked to reduce an item quantity
		if (isset($_POST['minus'])) {
			$sizeID = $_POST['sizeID'];
			$savedQuantity = $_SESSION['cart'][$sizeID]['orderProdQty'];
			$_SESSION['cart'][$sizeID]['orderProdQty'] = $savedQuantity - 1;
			if ($_SESSION['cart'][$sizeID]['orderProdQty'] < 1) {
				unset($_SESSION['cart'][$sizeID]);
			}
			unset($_POST['minus']);
		}
		// check if clicked to delete item out of the cart
		if (isset($_POST['delete'])) {
			$sizeID = $_POST['sizeID'];
			unset($_SESSION['cart'][$sizeID]);
		}
	}
		
	
	// this function creates a summary of the cart contents to be displayed on each page
	public function createSummary() {
		if ($_SESSION['cart']) {
			//	we will compute total value of items in the cart
			$itemCount = count($_SESSION['cart']);
			$itemStr = ($itemCount > 1) ? 'items' : 'item';
			$cartStr = 'Your cart contains '.$itemCount.' '.$itemStr.'<br />';
			$cartStr .= 'Total Value: $ '.sprintf('%.2f',$this->getTotalCartAmount()).'<br />';
			if ($_GET['page'] == 'cart' || isset($_SESSION['cart'])) {
				$cartStr .= '<a href="index.php?page=checkout">Checkout ||</a>';
			}
			$cartStr .= '<a href="index.php?page=cart"> View Cart</a>';
		}
		else {
			$cartStr = 'Your cart is empty!';
		}
		return $cartStr;
	}


	// this function takes the item added to cart from the  displayAddCartForm (productView)
	private function addToCart() {
		$split = explode(':', $_POST['sizeID']);
		$sizeID = $split[0];
		$sizePrice = $split[1];
		$artID = $_POST['id'];
		$quantity = $_POST['quantity'];
		$royalty = $_POST['royalty'];
		$artistID = $_POST['artistID'];
		$format = $_POST['format'];
		if ($artID == '' || $sizeID == '' || $quantity == '') {
			return false;
		}
		
		$_SESSION['cart'][$sizeID] = array(
			'sizeID'		=> $sizeID,
			'artistID'		=> $artistID,
			'orderProdQty'	=> $quantity,
			'artID'			=> $artID,
			'sizePrice'		=> $sizePrice,
			'format'		=> $format,
			'royalty'		=> $royalty
		);	
	}

	//	check to see if the item in the cart 
	//	(i.e. $_SESSION['cart'] has no element with subscript equal to the productID
	public function inCart($sizeID) {
		if (isset($_SESSION['cart'][$sizeID])) {
			return true;
		}
		else {
			return false;
		}
	}

	// calculates the prices of all items in the shopping cart
	public function getTotalCartAmount() {		
		//	compute the total value of items in the cart
		$total = 0;
		$cart = $_SESSION['cart'];
		foreach ($cart as $id => $item) {
			$subtotal = $item['sizePrice'] * $item['orderProdQty'];
			$total += $subtotal;
		}
		return $total;
	}

	// counts the number of items in the cart
	public function getCartQuantity() {		
		$count = 0;
		$items = $_SESSION['cart'];
		if (is_array($items)) {
			foreach ($items as $item) {
				$count = $count + $item['orderProdQty'];
			}
		}
		return $count;
	}

	
	// gets all the info from the database about each item in the cart
	public function getCartContents() {			
		$cartContents = array();
		if ($_SESSION['cart']) {
			foreach($_SESSION['cart'] as $id=>$item) {
				$sizeID = $item['sizeID'];
				$artInfo = $this->model->getProductByID($item['artID']);
				$sizeInfo = $this->model->getSizePrices($item['artID'], $sizeID);
				$cartContents[$sizeID]['artInfo'] = $artInfo[0];
				$cartContents[$sizeID]['sizeInfo'] = $sizeInfo[0];
			}
			return $cartContents;
		}
		else {
			return false;
		}
	}

	
	// calculates shipping based on
	// * the island being shipping to  (north or south)
	// * $10 for 1-10 prints, $20 for 11-20 prints etc
	// * $30 for each framed/canvas item (south island)
	// * $20 for every 2 framed/canvas items (north island)
	public function calculateShipping() {
		// calculate tubes and cost
		$printsShip = 0;
		if (isset($_SESSION['cart'])) {
			foreach ($_SESSION['cart'] as $item) {
				if ($item['format'] == 'print') {
					$printsShip = $printsShip + $item['orderProdQty'];
				}
			}
			$tubes = ceil($printsShip / 10);
			$printsShip = $tubes * 10;
			// calculate packages and cost
			$island = $_SESSION['island1'];
			$otherShip = 0;
			foreach ($_SESSION['cart'] as $item) {
				if ($item['format'] != 'print') {
					$otherShip = $otherShip + $item['orderProdQty'];
				}
			}
			if ($island == 'north') {
				$packages = ceil($otherShip / 2);
				$otherShip = $packages * 20;
			}
			else {
				$otherShip = $otherShip * 30;
			}
			$shipping = $printsShip + $otherShip;
		}
		return $shipping;
	}	
}
?>