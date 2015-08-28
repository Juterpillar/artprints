<?php

class FramedView extends View
{
	private $prodPerPage = 18;  	// specifies how many products to display per page
	private $catNum;				// identifies the shop page number

	// seperate construct so can determine what page number to display
	public function __construct($info, $model, $device) {
		parent::__construct($info, $model, $device);
		if (isset($_GET['catNum'])) {	// has the page number been passed in the url?
			$this->catNum = $_GET['catNum'];
		}
		else {
			$this->catNum = 1;
		}
	}

	protected function displayContent()
	{
		// display filter sidebar
		$html = $this->model->filterAside('framed');
		$html .= "\t\t\t\t".'<div id="content">'."\n";	
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
		// check if a filter has been selected
		if (isset($_GET['f'])) {
			switch ($_GET['f']) {
		    // subject
		    case 's':
		        $url = 'framed&f=s&s='.$_GET['s'];
		        $count = $this->model->countArtworks('subject', $_GET['s'], 'framed');
		        $products = $this->model->getProductsByBatch($this->catNum, $this->prodPerPage, 'framed', '', '', 'subject', $_GET['s']);
		    	if (is_array($products)) {
		    		$html .= "\t\t\t\t\t".'<h2 class="title">'.$products[0]['subjectName'].' framed</h2>'."\n";
		    	}
		    	else {
		    		$html .= "\t\t\t\t\t".'<h3 class="red no_matches">sorry, no framed prints match the subject you selected</h3>'."\n";
		    		$html .= $this->getAllPrints();
		    	}
		        break;
		    // artist
		    case 'a':
		    	$url = 'framed&f=a&a='.$_GET['a'];
		    	$count = $this->model->countArtworks('artist', $_GET['a'], 'framed');
		    	$products = $this->model->getProductsByBatch($this->catNum, $this->prodPerPage, 'framed', '', '', 'artist', $_GET['a']);
		    	if ($count > 0 && is_array($products)) {
		    		$html .= "\t\t\t\t\t".'<h2 class="title">framed art by '.$products[0]['artistName'].'</h2>'."\n";
		    	}
		    	else {
		    		$html .= "\t\t\t\t\t".'<h3 class="red no_matches">sorry, there are no framed prints for that artist</h3>'."\n";
		    		$html .= $this->getAllPrints();
		    		$products = 0;
		    		$count = 0;
		    	}
		    	break;
		    // size
		    case 'z':
		    	$sizeName = $_GET['z'];
		    	$url = 'framed&f=z&z='.$sizeName;
		    	$count = $this->model->countArtworks('size', $sizeName, 'framed');
		    	$products = $this->model->getProductsByBatch($this->catNum, $this->prodPerPage, 'framed', '', '', 'size', $sizeName);
		    	if (is_array($products)) {
		    		$html .= "\t\t\t\t\t".'<h2 class="title">framed prints available in '.$sizeName.'</h2>'."\n";
		    	}
		    	else {
		    		$html .= "\t\t\t\t\t".'<h3 class="red no_matches">sorry, there are no framed prints available in '.$sizeName.'</h3>'."\n";
		    		$html .= $this->getAllPrints();
		    	}
		    	break;
		    // price
		    case 'p':
		    	$url = 'framed&f=p&p='.$_GET['p'];
		    	$count = $this->model->countArtworks('price', $_GET['p'], 'framed');
		    	$products = $this->model->getProductsByBatch($this->catNum, $this->prodPerPage, 'framed', '', '', 'price', $_GET['p']);
		    	$price = $this->model->getMinMax($_GET['p']);
				$min = $price['min'] + 1;
				$max = $price['max'] - 1;
				if ($_GET['p'] == '20') {
					$min = 0;
				}
		    	if (is_array($products)) {
		    		$html .= "\t\t\t\t\t".'<h2 class="title">framed prints between $'.$min.' and $'.$max.'</h2>'."\n";
		    		
		    	}
		    	else {
		    		$html .= "\t\t\t\t\t".'<h3 class="red no_matches">sorry, there are no framed prints between $'.$min.' and $'.$max.'</h3>'."\n";
		    		$html .= $this->getAllPrints();
		    	}
				break;
		    // orientation
		    case 'o':
		    	$url = 'framed&f=o&o='.$_GET['o'];
		    	$allProducts = $this->model->getProductsByBatch($this->catNum, $this->prodPerPage, 'framed', '', '', 'orientation', $_GET['o']);
		    	if (is_array($allProducts)) {
		    		$singleProducts = array();
		    		foreach ($allProducts as $product) {
		    			$id = $product['artID'];
		    			if (!in_array($id, $singleProducts)) {
		    				$singleProducts[$id]['height'] = $product['sizeHeight'];
		    				$singleProducts[$id]['width'] = $product['sizeWidth'];
		   					$singleProducts[$id]['artistName'] = $product['artistName'];
		   					$singleProducts[$id]['artName'] = $product['artName'];
		   					$singleProducts[$id]['artID'] = $product['artID'];
		   					$singleProducts[$id]['artistID'] = $product['artistID'];
		   					$singleProducts[$id]['artImage'] = $product['artImage'];
		   					$singleProducts[$id]['artistHidden'] = $product['artistHidden'];
		   					$singleProducts[$id]['artHiddenDate'] = $product['artHiddenDate'];
		    			}
		    		}
		    		$products = array();
		    		if ($_GET['o'] == 'wide') {
		    			foreach ($singleProducts as $single) {
		    				extract($single);
		    				if ($height < $width) {
		    					$id = $single['artID'];
			   					$products[$id]['artistName'] = $single['artistName'];
			   					$products[$id]['artName'] = $single['artName'];
			   					$products[$id]['artID'] = $single['artID'];
			   					$products[$id]['artistID'] = $single['artistID'];
			   					$products[$id]['artImage'] = $single['artImage'];
			   					$products[$id]['height'] = $single['height'];
		    					$products[$id]['width'] = $single['width'];
		    					$products[$id]['artistHidden'] = $single['artistHidden'];
		   						$products[$id]['artHiddenDate'] = $single['artHiddenDate'];
		    				}
		    			}
		    		}
		    		else if ($_GET['o'] == 'tall') {
		    			foreach ($singleProducts as $single) {
		    				extract($single);
		    				if ($height > $width) {
		    					$id = $single['artID'];
			   					$products[$id]['artistName'] = $single['artistName'];
			   					$products[$id]['artName'] = $single['artName'];
			   					$products[$id]['artID'] = $single['artID'];
			   					$products[$id]['artistID'] = $single['artistID'];
			   					$products[$id]['artImage'] = $single['artImage'];
			   					$products[$id]['height'] = $single['height'];
		    					$products[$id]['width'] = $single['width'];
		    					$products[$id]['artistHidden'] = $single['artistHidden'];
		   						$products[$id]['artHiddenDate'] = $single['artHiddenDate'];
		    				}
		    			}
		    		}
		    		else if ($_GET['o'] == 'square') {
		    			foreach ($singleProducts as $single) {
		    				extract($single);
		    				if ($height == $width) {
		    					$id = $single['artID'];
			   					$products[$id]['artistName'] = $single['artistName'];
			   					$products[$id]['artName'] = $single['artName'];
			   					$products[$id]['artID'] = $single['artID'];
			   					$products[$id]['artistID'] = $single['artistID'];
			   					$products[$id]['artImage'] = $single['artImage'];
			   					$products[$id]['height'] = $single['height'];
		    					$products[$id]['width'] = $single['width'];
		    					$products[$id]['artistHidden'] = $single['artistHidden'];
		   						$products[$id]['artHiddenDate'] = $single['artHiddenDate'];
		    				}
		    			}
		    		}
		    		$count = count($products);
		    		$chunk = array_chunk($products, $this->prodPerPage, true);
		    		$chunkNum = $this->catNum - 1;
		    		$products = $chunk[$chunkNum];
		    		if ($count > 0) {
		    			$html .= "\t\t\t\t\t".'<h2 class="title">framed art of '.$_GET['o'].' orientation</h2>'."\n";
		    		}
		    		else {
			    		$html .= "\t\t\t\t\t".'<h3 class="red no_matches">sorry, there are no framed prints of '.$_GET['o'].' orientation</h3>'."\n";
			    		$count = 0;
			    		$products = 0;
			    		$html .= $this->getAllPrints();
		    		}
		    	}
		    	else {
		    		$html .= "\t\t\t\t\t".'<h3 class="red no_matches">sorry, there are no framed prints of '.$_GET['o'].' orientation</h3>'."\n";
		    		$html .= $this->getAllPrints();
		    	}
		    	break;
		    default:
		    	$html .= $this->getAllPrints();
			}	
		}
		else {
			$html .= $this->getAllPrints();
		}
		if (isset($products) && is_array($products)) {
			// show top links
			$html .= $this->model->displayPageLinks($count, $this->prodPerPage, $this->catNum, $url, '', $this->device['type']);
			// display products
			$html .= $this->model->displayProducts($products);
			// display bottom links
			$html .= $this->model->displayPageLinks($count, $this->prodPerPage, $this->catNum, $url, 'bottom', $this->device['type']);	
		}
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}

	// this function displays all prints if there are no matches for the selected filter
	private function getAllPrints() {
		$html = "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageHeading'].'</h2>'."\n";
	    $count = $this->model->countArtworks('catalogue', 'framed');
	    $products = $this->model->getProductsByBatch($this->catNum, $this->prodPerPage, 'framed');
	    $url = 'framed';
		if (is_array($products)) {
			// show top links
			$html .= $this->model->displayPageLinks($count, $this->prodPerPage, $this->catNum, $url);
			// display products
			$html .= $this->model->displayProducts($products);
			// display bottom links
			$html .= $this->model->displayPageLinks($count, $this->prodPerPage, $this->catNum, $url, 'bottom');	
		}
		return $html;
	}
}
?>