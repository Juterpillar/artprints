<?php

class ResultsView extends View
{
	private $prodPerPage = 12;  	// specifies how many products to display per page
	private $catNum;				// identifies the catalogue page number

	// seperate construct so can determine what page number to display
	public function __construct($info, $model, $device) {
		parent::__construct($info, $model, $device);
			if ($_GET['catNum']) {	// has the page number been passed in the url?
				$this->catNum = $_GET['catNum'];
			}
			else {
				$this->catNum = 1;
			}
	}

	protected function displayContent()
	{
		// if no search has been sent redirect to home page
		if(!$_GET['search']) {
			header("Location: index.php");
		}

		$html .= "\t\t\t\t".'<div id="content">'."\n";
		// get the products by batch
		$allProducts = $this->model->getProductsByBatch($this->catNum, $this->prodPerPage, '', '', $_GET['search']);
		$products = array();
		$count = '';
		if(is_array($allProducts)) {
			foreach ($allProducts as $allProduct) {
				$products[] = $this->model->checkArtHasSize($allProduct);
			}
			$count = '';
			foreach ($products as $product) {
				if(is_array($product) && $product['artHiddenDate'] == '' && $product['artistHidden'] == '') {
					$count = $count + 1;
				}
			}
			$url = 'results&search='.$_GET['search'];
			// if there are matches
			if (is_array($products) && $count > 0) {
				$html .= "\t\t\t\t\t".'<h2 class="title">search results for "'.$_GET['search'].'"</h2>'."\n";
				// show top links
				$html .= $this->model->displayPageLinks($count, $this->prodPerPage, $this->catNum, $url, '', $this->device['type']);
				// display products
				$html .= $this->model->displayProducts($products);
				// display bottom links
				$html .= $this->model->displayPageLinks($count, $this->prodPerPage, $this->catNum, $url, 'bottom', $this->device['type']);	
			}
			// no matches for search
			else {
				$html .= "\t\t\t\t\t".'<h2 class="title">sorry, there were no matches for "'.$_GET['search'].'"</h2>'."\n";
			}
		}
		// no matches for search
		else {
				$html .= "\t\t\t\t\t".'<h2 class="title">sorry, there were no matches for "'.$_GET['search'].'"</h2>'."\n";
			}
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}
}

?>