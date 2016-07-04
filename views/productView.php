<?php

class ProductView extends View
{
//	private $noSizes = '';  // holds value 'no matches' if a format doesn't have sizes so it doesn't show as an option.
	protected function displayContent() {
		if($_GET['id']) {
			// user has clicked from art page and artid saved in url
			$product = $this->model->getProductByID($_GET['id']);			
			if (is_array($product)) {
				$html = $this->displayProductAside($product);
				$html .= $this->displayProduct($product);
			} else {
					// product query failed
				header('location: index.php?page=noView'); 
			}
		}
		// no artwork selected so go redirect
		else {
			header('location: index.php?page=prints'); 
		}
		return $html;
	}

	// displays the side bar with the format/size options
	private function displayProductAside($product) {
		if ($this->device['type'] == 'mobile') {
			$mobile = true;
		}
		extract($product[0]);
		$types = $this->model->getTypesForProduct($artistID, $artName);
		$type['sizes'] = $this->model->getSizePrices($artID);
		// if sizes are availble, get the prices
		if (is_array($type['sizes'])) {
			foreach ($type['sizes'] as $size) {
				if (isset($_SESSION['custID'])) {
					$prices['size'][] = $size['sizeWSP'];
				}
				else {
					$prices['size'][] = $size['sizeRRP'];
				}
			}
		}
		// calculations for following display
		$typeNum = count($types) - 1;				// the number of formats available minus the one being viewed
		$typePlural = ($typeNum > 1) ? 's' : '';	// add a plural if required
		$sizeNum = count($type['sizes']);			// number of sizes available in this format
		$sizePlural = ($sizeNum > 1) ? 's' : '';	// add a plural if required
		$fromPrice = min($prices['size']);			// calculates cheapest price for this format
		$artLabel = $artType == 'matted_print' ? 'matted print' : $artType;
		$html = "\t\t\t\t".'<aside>'."\n";
		$html .= "\t\t\t\t\t".'<h2 id="pricing_heading" class="mobile">size/format options <i class="icon-circle-arrow-down"></i></h2>'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">'.$artLabel.' from $'.$fromPrice.'<br />';
		if(!isset($_SESSION['custID'])) {
			$html .= '<span> (retail price)</span>';
		}
		else {
			$html .= '<span> (wholesale price)</span>';
		}
		$html .= '</h2>'."\n";
		$html .= "\t\t\t\t\t".'<div id="options">'."\n";
		$html .= "\t\t\t\t\t".'<form method="post" action="'.$_SERVER['REQUEST_URI'].'" id="add_to_cart_form">'."\n";
		$html .= "\t\t\t\t\t".'<input type="hidden" name="id" value="'.$artID.'">'."\n";
		$html .= "\t\t\t\t\t".'<input type="hidden" name="artistID" value="'.$artistID.'">'."\n";

		// sizes
		$html .= "\t\t\t\t\t\t".'<h4 class="no_mobile clear">'.$sizeNum.' '.$artLabel.' size'.$sizePlural.'</h4>'."\n";
		$html .= "\t\t\t\t\t\t".'<div class="select">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<i class="icon-circle-arrow-down"></i>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<select name="sizeID">'."\n";
		if (isset($mobile)) {
			$html .= "\t\t\t\t\t\t\t\t".'<option value="" class="mobile">avaliable in '.$sizeNum.' '.$artLabel.' size'.$sizePlural.'</option>'."\n";
		}
		foreach ($types as $type) {
			if ($type['artType'] == $artType) {
				// get the sizes availble for this type
				$type['sizes'] = $this->model->getSizePrices($type['artID']);
				// if sizes are availble, get the prices
				if (is_array($type['sizes'])) {
					foreach ($type['sizes'] as $size) {
						// wholesaler so show WSP
						if (isset($_SESSION['custID'])) {
							$price = sprintf('%.2f', $size['sizeWSP']);
						}
						// general public so show RRP
						else {
							$price = sprintf('%.2f', $size['sizeRRP']);
						}
						$html .= "\t\t\t\t\t\t\t\t".'<option value="'.$size['sizeId'].':'.$price.'"';
						if ($size['sizeRRP'] == $fromPrice) {
							$html .= ' selected="selected"';
						}
						$html .= '>'.$size['sizeWidth'].' x '.$size['sizeHeight'].' - $'.$price.'&nbsp;&nbsp;&nbsp;</option>'."\n";
					}
				}
				else {
					$this->noSizes = 'no matches';
				}	
			}
		}
		$html .= "\t\t\t\t\t\t\t".'</select>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- select -->'."\n";
		// quantity				
		$html .= "\t\t\t\t\t\t".'<h4 class="no_mobile clear">quantity</h4>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="quantity_select" class="select">'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<i class="icon-circle-arrow-down"></i>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<select name="quantity">'."\n";
		if (isset($mobile)) {
			$html .= "\t\t\t\t\t\t\t\t".'<option class="mobile" value="1">quantity</option>'."\n";		}
		$html .= "\t\t\t\t\t\t\t\t".'<option value="1">1</option>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<option value="2">2</option>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<option value="3">3</option>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<option value="4">4</option>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<option value="5">5</option>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<option value="6">6</option>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<option value="7">7</option>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<option value="8">8</option>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<option value="9">9</option>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<option value="10">10</option>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</select>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- select -->'."\n";
		$html .= "\t\t\t\t\t\t".'<input value="'.$artType.'" name="format" type="hidden" />'."\n";		
		// disable add to cart button if over credit limit
		if ($_SESSION['access'] == 'customer' || $_SESSION['access'] == 'admin') {
			$credit = $this->model->calcCreditDets($_SESSION['credit'], $_SESSION['custID']);
			if ($credit == 0) {
				$html .= "\t\t\t\t\t\t".'<input type="submit" class="clear disabled checkout_button" name="addToCart" value="add to cart" disabled="disabled" />'."\n";
			}
			else if (isset($_SESSION['custID'])) {
				$html .= "\t\t\t\t\t\t".'<input type="submit" class="clear checkout_button" name="addToCart" value="add to cart" />'."\n";
			}
		}
		else {
			$html .= "\t\t\t\t\t\t".'<a id="redirect" href="http://www.afas.co.nz/SearchResults.aspx?st='.$artistName.'" target="_blank" class="clear checkout_button button"><i class="icon-shopping-cart"></i> buy now</a>'."\n";
			$html .= "\t\t\t\t\t\t".'<h6>You will be redirected to afas.co.nz.<br />Please <a href="index.php?page=login">login</a> to purchase from this website.</h6>'."\n";
		}
		// formats
		$typeNum = $typeNum - 1;
		$typePlural = ($typeNum > 1) ? 's' : '';
		if ($typeNum > 0) {
			$html .= "\t\t\t\t\t\t".'<h4 id="format_heading" class="no_mobile">'.$typeNum.' other format'.$typePlural.' available</h4>'."\n";
		}
		foreach ($types as $type) {
			$type['sizes'] = $this->model->getSizePrices($type['artID']);
			// if sizes are availble, get the prices
			if ($type['artType'] != $artType && is_array($type['sizes'])) {
				$label = $type['artType'] == 'matted_print' ? 'matted print' : $type['artType'];
				$html .= "\t\t\t\t\t\t\t\t".'<a href="index.php?page=product&id='.$type['artID'].'" class="button">view '.$label.'</a>';
			}
		}
		$html .= "\t\t\t\t\t".'</form>'."\n";
		$html .= "\t\t\t\t\t".'</div><!-- options -->'."\n";
		$html .= "\t\t\t\t".'</aside>'."\n";
		return $html;
	}


	// this function displays the product in the main content div
	private function displayProduct($product) {
		extract($product[0]);
		$origArtID = $artID;
		if ($artType == 'print') {
			$artType = 'prints';
		}
		if ($artType == 'prints') {
			$typeLabel = 'print';
		}
		if ($artType == 'matted_print') {
			$typeLabel = 'matted print';
		}
		else {
			$typeLabel = $artType;
		}
		if ($artType != 'canvas') {
			$shadow = "image_shadow ";
		}
		$html = "\t\t\t\t".'<div id="content">'."\n";
		// if over credit limit
		if ($_SESSION['access'] == 'customer') {
			$credit = $this->model->calcCreditDets($_SESSION['credit'], $_SESSION['custID']);
			if ($credit == 0) {
				$html .= "\t\t\t\t\t".'<div class="success'; 
				if ($this->device['type'] == 'desktop') {
					$html .= ' width';
				}
				$html .= "\t\t\t\t\t\t".' brown"><p>sorry, you cannot place a new order as you are at your credit limit</p>'."\n";
				$html .= "\t\t\t\t\t".'</div>'."\n";
			}
		}
		// if the user has clicked to add this product to their cart
		if(isset($_POST['addToCart'])) {
			// if item added to cart
			$html .= "\t\t\t\t\t".'<div class="success'; 
			if ($this->device['type'] == 'desktop') {
				$html .= ' width';
			}
			extract($_POST);
			if ($sizeID == '') {
				$html .= "\t\t\t\t\t\t".' brown"><p>not added! select a size and quantity to add this to your cart</p>'."\n";
			}
			// Added to the cart
			else {
				$html .= "\t\t\t\t\t\t".'"><p>this '.$typeLabel.' is in your <a href="index.php?page=cart">cart</a></p>'."\n";
			}
			$html .= "\t\t\t\t\t".'</div>'."\n";
		}

		// check if this item is in the cart and display a message if so
		else {
			// check all sizes to see if in $_SESSION['cart']
			$cartConfirm = '';
			$sizesForCart = $this->model->getSizePrices($artID);
			$this->cartObj = new Cart($this->model);
			foreach ($sizesForCart as $sizeID) {
				$inCart = $this->cartObj->inCart($sizeID['sizeId']);
				if ($inCart == true) {
					$cartConfirm = $cartConfirm + 1;
				}
			} 
			if ($cartConfirm > 0) {
				$html .= "\t\t\t\t\t".'<div class="success'; 
				if ($this->device['type'] == 'desktop') {
					$html .= ' width';
				}
				$html .= "\t\t\t\t\t\t".'"><p>this '.$typeLabel.' is in your <a href="index.php?page=cart">cart</a></p>'."\n";
				$html .= "\t\t\t\t\t".'</div>'."\n";
			}
		}

		$html .= "\t\t\t\t\t".'<h2>'.$artName.' ('.$typeLabel.')';
		if ($_SESSION['access'] == 'admin') {
			$html .= '<span class="admin_span"><a href="index.php?page=adminartwork&artID='.$_GET['id'].'">&nbsp;&nbsp;edit this product</a></span>';
		}
		$html .= '</h2>'."\n";
		$html .= "\t\t\t\t\t".'<h4>by '.$artistName.'</h4>'."\n";
		if ($this->device['type'] == 'desktop') {
			$size = 'lightbox';
		}
		if ($this->device['type'] == 'tablet') {
			$size = 'desktop';
		}
		if ($this->device['type'] == 'mobile') {
			$size = 'mobile';
		}
		if ($artType == 'canvas') {
			$html .= "\t\t\t\t\t\t".'<a href="images/'.$size.'/canvas_info.jpg" id="canvas_info" class="fancybox" title="canvas information"><h6><i class="icon-question-sign"></i> click here for information about canvas</h6></a>'."\n";
		}
		$artImage = $artImage == '' ? 'no-image.png' : $artImage;
		$html .= "\t\t\t\t\t".'<h6><a href="index.php?page='.$artType.'"><i class="icon-circle-arrow-left"></i> back to '.$artType.' catalogue</a></h6>'."\n";
		$html .= "\t\t\t\t\t".'<div class="icon_nav">'."\n";
		$html .= "\t\t\t\t\t\t".'<a href="images/'.$size.'/'.$artImage.'" class="fancybox" title="\''.$artName.'\' by '.$artistName.'"><img class="'.$shadow.'product_image" src="images/'.$this->device['type'].'/'.$artImage.'" title="gallery view" alt="'.$artName.' by '.$artistName.'" />'."\n";
		$html .= "\t\t\t\t\t\t".'<h6>click for gallery view <i class="icon-zoom-in"></i></h6></a>'."\n";
		$html .= "\t\t\t\t\t".'</div> <!-- icon_nav -->'."\n";
		if ($artDescription != '') {
			$html .= $this->model->textTo2Cols($artDescription, 'artwork info', 'product_text');
		}
		if ($artistBio != '') {
			$html .= $this->model->textTo2Cols($artistBio, 'artist bio', 'product_text');
		}
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";

		// check if there is other artwork by this artist
		if ($artType == 'prints') {
			$artType = 'print';
		}
		$artByArtist = $this->model->getProductsByBatch(1, 15, $artType, '', '', 'artist', $artistID);
		if (is_array($artByArtist)) {
			foreach ($artByArtist as $aProduct) {
				extract($aProduct);
				$id = $aProduct['artID'];
				if ($aProduct['artID'] != $_GET['id']) {
					$aProducts[$id]['artID'] = $aProduct['artID'];
					$aProducts[$id]['artistID'] = $aProduct['artistID'];
					$aProducts[$id]['artName'] = $aProduct['artName'];
					$aProducts[$id]['artDescription'] = $aProduct['artDescription'];
					$aProducts[$id]['artImage'] = $aProduct['artImage'];
					$aProducts[$id]['artistName'] = $aProduct['artistName'];
				}
			}
			$count = count($aProducts);
			if ($count > 0) {
				$width = $count * 210;
				$html .= $this->displayArtistSuggestions($aProducts, $artistName, $width, $artType);
			}
		}
		// check if there is other artwork in this subject
		$subjects = '';
		$subjects = $this->model->getSubjectsByArtID($origArtID);
		$products = array();
		if (is_array($subjects)) {
			foreach ($subjects as $subject) {
				extract($subject);
			//	echo $subjectID.' '.$subjectName.'<br />';
				$subjectArt[$subjectID] = $artSubject = $this->model->getProductsByBatch(1, 15, $artType, '', '', 'subject', $subjectID);
				foreach ($subjectArt[$subjectID] as $sProduct) {
					$id = $sProduct['artID'];
					if ($artistID != $sProduct['artistID'] && !in_array($id, $products) ) {
						$products[$id]['artID'] = $sProduct['artID'];
						$products[$id]['artistID'] = $sProduct['artistID'];
						$products[$id]['artName'] = $sProduct['artName'];
						$products[$id]['artDescription'] = $sProduct['artDescription'];
						$products[$id]['artImage'] = $sProduct['artImage'];
						$products[$id]['artistName'] = $sProduct['artistName'];
					}
				}
			}
		}
		$count = count($products);
		if ($count > 0) {
			$width = $count * 225;
			$html .= $this->displaySubjectSuggestions($products, $width, $artType);
		}		
		return $html;
	}

	// displays other artwork by the same artist
	private function displayArtistSuggestions($products, $artist, $width, $canvas='') {
		// display other artwork by this artist
		$html .= '				<h4 class="suggestion_heading">more by '.$artist.'...</h4>'."\n";
		$html .= '				<div id="artist_suggestions">'."\n";
		if ($canvas == 'canvas') {
			$html .= $this->model->displayProducts($products, true, $width);
		}
		else {
			$html .= $this->model->displayProducts($products, '', $width);
		}
		$html .= '				</div><!-- artist_suggestions -->'."\n";
		return $html;
	}

	// displays other artwork that' is the same subject matter
	private function displaySubjectSuggestions($products, $width, $canvas='') {
		// display other artwork with in this subject category
		$html .= '				<h4 class="suggestion_heading">you might also like...</h4>'."\n";
		$html .= '				<div id="subject_suggestions">'."\n";
		if ($canvas == 'canvas') {
			$html .= $this->model->displayProducts($products, true, $width);
		}
		else {
			$html .= $this->model->displayProducts($products, '', $width);
		}
		$html .= '				</div><!-- subject_suggestions -->'."\n";
		return $html;
	}
}
?>