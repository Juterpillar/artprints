<?php

abstract class View
{
	protected $pageInfo;	//	contains data from the pages table obtained through construct
	protected $model;		//	object for the dbase class "protected" = available in classes that extend view
	protected $device;		//  object containing the type of device; enum 'mobile', 'tablet', or 'desktop'.
	protected $cartObj;		//	object for the cart class
	

	// saves into variables (above) info about page from DB and connection to DB class
	public function __construct($info, $model, $device) {
		$this->pageInfo = $info;
		$this->model = $model;
		$this->device = $device;
		
		$cartPages = array('checkout', 'adminartist', 'cart', 'order', 'contact', 'account', 'royalty');
		if (in_array($_GET['page'], $cartPages)) {
			$this->cartObj = new Cart($model);
		}
		// if the page is an occasion page overwrite some of $pageInfo with info from the occasion table
	}
		
	// calls 3 private functions to display every part of the page
	public function displayPage() {	
		$html = $this->displayHeader();
		$html .= $this->displayContent();
		$html .= $this->displayFooter();
		return $html;
	}
	
	// abstract as not written in each page view
	abstract protected function displayContent();
	
	
	// displays header content
	private function displayHeader() {	
		error_reporting(0);
		$html = '<!DOCTYPE html>'."\n";
		$html .= '<html lang="en-nz">'."\n";
		$html .= '<head>'."\n";
		// meta info
		$html .= "\t".'<meta charset="utf-8" />'."\n";
		$html .= "\t".'<meta name="description" content="'.$this->pageInfo['pageDescription'].'" />'."\n";
		$html .= "\t".'<meta name="keywords" content="'.$this->pageInfo['pageKeywords'].'" />'."\n";
		// title and favicon
		// if noView, has no page title as not in database pages table
		if (!$this->pageInfo['pageTitle']) {
			$this->pageInfo['pageTitle'] = 'Artprints | Page Not Found';
			$this->pageInfo['pageName'] = 'no_view';
		} 
		$html .= "\t".'<title>'.$this->pageInfo['pageTitle'].'</title>'."\n";
		$html .= "\t".'<link rel="icon" type="image/png" href="images/favicon.png" />'."\n";		
		// links to stylesheets
		$html .= "\t".'<meta name="viewport" content="width=device-width,initial-scale=1.0, maximum-scale=1.0" />'."\n";
		$html .= "\t".'<link rel="stylesheet" href="css/default_and_mobile.css" />'."\n";	
		$html .= "\t".'<link rel="stylesheet" href="css/font-awesome.min.css">'."\n";
		$html .= "\t".'<link rel="stylesheet" href="css/tablet_and_desktop.css" media="only all and (min-width: 569px)" />'."\n";

		// links to stylesheets for less than I.E9 as does not understand media queries
		$html .= "\t".'<!--[if lt IE 9 & !IEMobile]>'."\n";
		$html .= "\t\t".'<link rel="stylesheet" href="css/default.css"/>'."\n";
		$html .= "\t\t".'<link rel="stylesheet" href="css/tablet.css"/>'."\n";
		$html .= "\t\t".'<link rel="stylesheet" href="css/desktop.css"/>'."\n";
		$html .= "\t\t".'<link rel="stylesheet" href="css/ie.css"/>'."\n";
		$html .= "\t".'<![endif]-->'."\n";
		// allows HTML5 elemets for less than IE 9
		$html .= "\t".'<!--[if lt IE 9]>'."\n";
		$html .= "\t\t".'<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>'."\n";
		$html .= "\t".'<![endif]-->'."\n";

		// google analytics
		if ($this->pageInfo['pageName'] != 'account' 
			&& $this->pageInfo['pageName'] != 'artist'
			&& $this->pageInfo['pageName'] != 'artwork'
			&& $this->pageInfo['pageName'] != 'banner'
			&& $this->pageInfo['pageName'] != 'cart'
			&& $this->pageInfo['pageName'] != 'checkout'
			&& $this->pageInfo['pageName'] != 'dashboard'
			&& $this->pageInfo['pageName'] != 'orders'
			&& $this->pageInfo['pageName'] != 'order'
			&& $this->pageInfo['pageName'] != 'login'
			&& $this->pageInfo['pageName'] != 'product' 
			&& $this->pageInfo['pageName'] != 'royalty' 
			&& $this->pageInfo['pageName'] != 'results') {
			include_once("analyticstracking.php");
		}

		// link to google API for contact page only
		if ($this->pageInfo['pageName'] == 'contact') {
			$html .= "\t".'<script src="http://maps.google.com/maps/api/js?sensor=false&amp;region=NZ"></script> <!-- Google API script for map -->'."\n";
		}
		// link to jquery library and flexslider for photo gallery
		$html .= "\t".'<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>'."\n";
		$html .= "\t".'<script src="js/jquery.flexslider-min.js"></script>'."\n";
		if ($this->pageInfo['pageName'] == 'product' || $this->pageInfo['pageName'] == 'canvas') {
			$html .= "\t".'<link rel="stylesheet" href="css/jquery.fancybox.css" type="text/css" media="screen" />'."\n";
			$html .= "\t".'<script type="text/javascript" src="js/jquery.fancybox.pack.js"></script>'."\n";
		}
		$html .= "\t".'<script type="text/javascript" src="js/default.js" charset="utf-8"></script>'."\n";
		$html .= '</head>'."\n";
		if ($this->pageInfo['pageName'] == 'framed'
		|| $this->pageInfo['pageName'] == 'canvas'
		|| $this->pageInfo['pageName'] == 'matted_prints'
		|| $this->pageInfo['pageName'] == 'prints'
		|| $this->pageInfo['pageName'] == 'cards') {
			$html .= '<body id="catalogue">'."\n";
		}
		else {
			$html .= '<body id="'.$this->pageInfo['pageName'].'">'."\n";
		}
		// container
		$html .= "\t\t".'<div id="container">'."\n";
		// header 
		$html .= "\t\t\t".'<header>'."\n";
		// browser less than IE 9 warning
		$html .= "\t\t\t".'<!--[if lt IE 9]>'."\n";
		$html .= "\t\t\t\t".'<div class="noscript">'."\n";
		$html .= "\t\t\t\t\t".'<p>Please upgrade your browser for a better viewing experience!</p>'."\n";
		$html .= "\t\t\t\t".'</div>'."\n";
		$html .= "\t\t\t".'<![endif]-->'."\n";
		// javascript no script warning
		$html .= "\t\t\t".'<noscript>'."\n";
		$html .= "\t\t\t\t".'<p>***this website works best with javascript enabled***</p>'."\n";
		$html .= "\t\t\t".'</noscript>'."\n";
		// pre_nav
		$html .= $this->displayPreNav();

		$html .= "\t\t\t".'<a href="index.php"><h1>artprints, Publishers &amp; Distributors</h1></a>'."\n";
		$html .= "\t\t\t".'<form action="#" method="get">'."\n";
		$html .= "\t\t\t".'<input type="hidden" name="page" value="results" />'."\n";
		$html .= "\t\t\t\t".'<input type="text" id="search_text" name="search" placeholder="search art by keyword..." />'."\n";
		
		$html .= "\t\t\t\t".'<button type="submit"><i class="icon-search icon-large"></i> search</button>'."\n";
		$html .= "\t\t\t".'</form>'."\n";
		$html .= "\t\t\t".'<p id="phone_email"><i class="icon-phone"></i> 0800 101015 &nbsp;&nbsp;</p>'."\n";

		// nav
		$html .= $this->displayNav();
		$html .= "\t\t\t".'</header>'."\n";
		$html .= "\t\t\t".'<div id="wrapper">'."\n";
		return $html;
	}

	private function displayPreNav() {
		// links available to non-logged in guests
		if (!isset($_SESSION['name']) || $this->pageInfo['pageName'] == 'logout') {
			$links = array('login', 'register', 'shipping');
		}

		// links available to logged in admin
		else if (isset($_SESSION['name']) && $_SESSION['access'] == 'admin') {
			$links = array('account', 'logout', 'dashboard', 'shipping', 'cart', 'checkout');
		}

		// links available to logged in guests
		else if (isset($_SESSION['name'])) {
			$links = array('account', 'logout', 'shipping', 'cart', 'checkout');
		}

		// links for no-mobile class
		$nomobile = array('shipping', 'register', 'checkout');

		$html = "\t\t\t".'<nav id="pre_nav">'."\n";
		$html .= "\t\t\t\t".'<ul>'."\n";

		foreach($links as $link) {
			$html .= "\t\t\t\t\t".'<li><a';
			// test if in no-mobile array  and current then add current and no-moblie link
			if ($this->pageInfo['pageName'] == $link && in_array($link, $nomobile)) {
				$html .= ' class="pre_current no_mobile"';
			} 
			// test if no-mobile link but not current then just add mobile link
			else if (in_array($link, $nomobile)) {
				$html .= ' class="no_mobile"';
			}
			
			// test if the current page, then add current class
			else if ($this->pageInfo['pageName'] == $link) {
				$html .= ' class="pre_current"';
			}
			$html .= ' href="index.php?page='.$link.'">';
			if ($link == 'logout') {
				$html .= 'logout';
			}
			else if ($link == 'shipping') {
				$html .= '<i class="icon-truck"></i>shipping';
			}
			else if ($link == 'register') {
				$html .= 'register';
			}
			else if ($link == 'login') {
				$html .= '<i class="icon-user"></i>login';
			}
			else if ($link == 'cart') {
				$html .= '<i class="icon-shopping-cart"></i>shopping cart<span> ';
				// show # of items in the cart
				$this->cartObj = new Cart($this->model);
				if(isset($_SESSION['cart'])) {
					$cartQty = $this->cartObj->getCartQuantity();
					if ($cartQty > 0) {
						$html .= '('.$cartQty.')';
					}
				}
				$html .= '</span>';
			}
			else if ($link == 'checkout') {
				$html .= '<i class=" icon-credit-card"></i>checkout';
			}
			else if ($link == 'account') {
				$html .= 'your account';
			}
			else if ($link == 'dashboard') {
				$html .= '<i class="icon-dashboard"></i>dashboard';
			}
			$html .= '</a></li>'."\n";
		}
		if ($device['type'] = 'mobile') {
				$html .= "\t\t\t\t\t".'<li><a class="mobile" href="'.$_SERVER['REQUEST_URI'].'#footer"><i class="icon-reorder"></i>more</a></li>'."\n";
		}
		$html .= "\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t".'</nav>'."\n";
		return $html;
	}


	// displays the mainnav and uses foreach to determine the current page so link displayed differently
	private function displayNav() {
		// all links
		$links = array('framed', 'prints', 'matted_prints', 'canvas', 'cards', 'about');

		// links for no-mobile class
		$nomobile = array('home', 'about');

		$html = "\t\t\t".'<nav id="main_nav">'."\n";
		$html .= "\t\t\t\t".'<ul>'."\n";

		foreach($links as $link) {
			$html .= "\t\t\t\t\t".'<li';
			// test if in no-mobile array  and current then add current and no-moblie link
			if ($this->pageInfo['pageName'] == $link && in_array($link, $nomobile)) {
				$html .= ' class="current no_mobile"';
			} 
			// test if no-mobile link but not current then just add mobile link
			else if (in_array($link, $nomobile)) {
				$html .= ' class="no_mobile"';
			}
			// test if the current page, then add current class
			else if ($this->pageInfo['pageName'] == $link) {
				$html .= ' class="current"';
			}
			$html .= '><a href="index.php?page='.$link.'">';
			if ($link == 'about') {
				$link = 'about us';
			}
			else if ($link == 'matted_prints') {
				$link = 'matted prints';
			}
			$html .= $link.'</a></li>'."\n";
		}
		$html .= "\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t".'</nav>'."\n";
		return $html;
	}

	// displays the footer
	private function displayFooter() {
		$no_mobile = array('policy', 'terms', 'sitemap', 'top');
		$mobile = array('home', 'framed', 'prints', 'canvas', 'cards', 'about', 'contact', 'faq', 'shipping', 'register', 'login', 'cart', 'checkout', 'policy', 'terms', 'sitemap', 'top');

		$html = "\t\t\t".'<footer id="footer">'."\n";
		$html .= "\t\t\t\t".'<ul>'."\n";
		// add href and name for each link
		
		foreach($mobile as $link) {
			$html .= "\t\t\t\t\t".'<li><a';
			// test if in no-mobile array  and current then add current and no-moblie link
			if ($this->pageInfo['pageName'] == $link && !in_array($link, $no_mobile)) {
				$html .= ' class="current mobile"';
			} 
			// test if no-mobile link but not current then just add mobile link
			else if (!in_array($link, $no_mobile)) {
				$html .= ' class="mobile"';
			}
			// test if the current page, then add current class
			else if ($this->pageInfo['pageName'] == $link) {
				$html .= ' class="current"';
			}
			else if ($link == 'top') {
				$link = $this->pageInfo['pageName'].'#';
			}
			$html .= ' href="index.php?page='.$link.'">';
			if ($link == 'shipping') {
				$html .= '<i class="icon-truck"></i> ';
			}
			else if ($link == 'login') {
				$html .= '<i class="icon-user"></i> ';
			}
		
			else if ($link == 'cart') {
				$html .= '<i class="icon-shopping-cart"></i>';
				$link = 'shopping cart ';
				$this->cartObj = new Cart($this->model);
				if (isset($_SESSION['cart'])) {
					$cartQty = $this->cartObj->getCartQuantity();
					if ($cartQty > 0) {
						$html .= '<span>('.$this->cartObj->getCartQuantity().')</span>';
					}
				}
			}
			else if ($link == 'checkout') {
				$html .= '<i class=" icon-credit-card"></i> ';
			}
			else if ($link == 'account') {
				$link = 'your account';
			}
			else if ($link == 'about') {
				$link = 'about us';
			}
			else if ($link == 'matted_prints') {
				$link = 'matted prints';
			}
			else if ($link == 'catalogue?f=framed' || $link == 'catalogue?f=canvas' || $link == 'catalogue?f=prints') {
				$link = explode('=', $link);
				$link = $link[1];
			}
			else if ($link == 'home') {
				$html .= '<i class="icon-home"></i> ';
			}
			else if ($link == 'sitemap') {
				$html .= '<i class="icon-sitemap"></i> ';
			}
			else if ($link == 'contact') {
				$html .= '<i class="icon-phone"></i> ';
			}
			else if ($link == 'faq') {
				$html .= '<i class="icon-question-sign"></i> ';
			} 
			else if ($link == $this->pageInfo['pageName'].'#') {
				$html .= '<i class="icon-circle-arrow-up"></i> ';
				$link = 'top';
			} 
			else if ($link == 'policy') {
				$link = 'private policy';
			} 
			else if ($link == 'terms') {
				$link = 'terms &amp; conditions';
			} 
			$html .= $link.'</a></li>'."\n";
		}
		$html .= "\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t".'<p>&copy; Artprints Ltd. 2013</p>'."\n";
		$html .= "\t\t\t\t".'<p>web design &amp; development by <a href="http://www.linkedin.com/profile/view?id=195939285&amp;trk=tab_pro" target="_blank">Julia Larsen</a></p>'."\n";
		$html .= "\t\t\t\t".'<p class="no_mobile">All rights reserved.</p>'."\n";
		$html .= "\t\t\t".'</footer>'."\n";
		$html .= "\t\t".'</div> <!-- wrapper -->'."\n";
		$html .= "\t".'</div> <!-- container -->'."\n";
		$html .= '</body>'."\n";
		$html .= '</html>'."\n";
		return $html;	
	}	
}
?>