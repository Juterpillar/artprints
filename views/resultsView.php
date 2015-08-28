<?php

class ResultsView extends View
{
	private $prodPerPage = 20;  	// specifies how many products to display per page
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
		$countSearch = $this->model->getProductsByBatch(1, 2000, '', '', $_GET['search']);
		$countS='';
		if(is_array($countSearch)) {
			foreach ($countSearch as $countResult) {
				$searchResult[] = $this->model->checkArtHasSize($countResult);
			}
			foreach ($searchResult as $result) {
				if(is_array($result) && $result['artHiddenDate'] == '' && $result['artistHidden'] == '') {
					$countS = $countS + 1;
				}
			}
		}
		if ($countS > 0) {
			$allProducts = $this->model->getProductsByBatch($this->catNum, $this->prodPerPage, '', '', $_GET['search']);
			$products = array();
			if(is_array($allProducts)) {
				foreach ($allProducts as $allProduct) {
					$products[] = $this->model->checkArtHasSize($allProduct);
				}
				$url = 'results&search='.$_GET['search'];
				// if there are matches
				if (is_array($products)) {
					$html .= "\t\t\t\t\t".'<h2 class="title">search results for "'.$_GET['search'].'"</h2>'."\n";
					// show top links
					$html .= $this->model->displayPageLinks($countS, $this->prodPerPage, $this->catNum, $url, '', $this->device['type']);
					// display products
					$html .= $this->model->displayProducts($products);
					// display bottom links
					$html .= $this->model->displayPageLinks($countS, $this->prodPerPage, $this->catNum, $url, 'bottom', $this->device['type']);	
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