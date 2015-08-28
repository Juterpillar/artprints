<?php

//	This script is the 'controller' and loads automatically then directing to the appropriate page using the switch

session_start();
include 'views/viewClass.php';
include 'classes/modelClass.php';
include 'classes/Mobile_Detect.php';
include 'classes/cartClass.php';

class PageSelector
{
	public function run()
	{  
//		error_reporting(E_ALL);
		if (!isset($_GET['page'])) {			// if page is not available from the url then..
			$_GET['page'] = 'home';		// set it to home page
		}
		
		// instantiating dbase class through model class that extends dbase class
		$model = new Model;
		$pageInfo = $model->getPageInfo($_GET['page']);
		
		// instatiating the mobile detect class
		$detector = new Mobile_Detect();
		if($detector->isTablet()) {
			$device['type'] = 'tablet';
			$device['detail'] = 400;
			$device['banner'] = 735;
		} 
		else if($detector->isMobile()) {
			$device['type'] = 'mobile';
			$device['detail'] = 300;
			$device['banner'] = 320;
		}
		else {
			$device['type'] = 'desktop';
			$device['detail'] = 600;
			$device['banner'] = 960;
		}

		switch ($_GET['page']) {
			case 'home': include 'views/homeView.php';
			$view = new HomeView($pageInfo, $model, $device);
			break;
			case 'about': include 'views/aboutView.php';
			$view = new AboutView($pageInfo, $model, $device);
			break;
			case 'contact': include 'views/contactView.php';
			$view = new ContactView($pageInfo, $model, $device);
			break;
			case 'faq': include 'views/faqView.php';
			$view = new FaqView($pageInfo, $model, $device);
			break;
			case 'shipping': include 'views/shippingView.php';
			$view = new ShippingView($pageInfo, $model, $device);
			break;
			case 'register': include 'views/registerView.php';
			$view = new RegisterView($pageInfo, $model, $device);
			break;
			case 'login': include 'views/loginView.php';
			$view = new LoginView($pageInfo, $model, $device);
			break;			
			case 'framed': include 'views/framedView.php';
			$view = new FramedView($pageInfo, $model, $device);
			break;
			case 'prints': include 'views/printsView.php';
			$view = new PrintsView($pageInfo, $model, $device);
			break;
			case 'canvas': include 'views/canvasView.php';
			$view = new CanvasView($pageInfo, $model, $device);
			break;
			case 'cards': include 'views/cardsView.php';
			$view = new CardsView($pageInfo, $model, $device);
			break;
			case 'product': include 'views/productView.php';
			$view = new ProductView($pageInfo, $model, $device);
			break;
			case 'account': include 'views/accountView.php';
			$view = new AccountView($pageInfo, $model, $device);
			break;
			case 'dashboard': include 'views/dashboardView.php';
			$view = new DashboardView($pageInfo, $model, $device);
			break;
			case 'logout': include 'views/logoutView.php';
			$view = new LogoutView($pageInfo, $model, $device);
			break;
			case 'dashboard': include 'views/dashboardView.php';
			$view = new DashboardView($pageInfo, $model, $device);
			break;
			case 'order': include 'views/orderView.php';
			$view = new OrderView($pageInfo, $model, $device);
			break;
			case 'adminartist': include 'views/artistView.php';
			$view = new ArtistView($pageInfo, $model, $device);
			break;
			case 'adminartwork': include 'views/artworkView.php';
			$view = new ArtworkView($pageInfo, $model, $device);
			break;
			case 'orders': include 'views/ordersView.php';
			$view = new OrdersView($pageInfo, $model, $device);
			break;
			case 'results': include 'views/resultsView.php';
			$view = new ResultsView($pageInfo, $model, $device);
			break;
			case 'cart': include 'views/cartView.php';
			$view = new CartView($pageInfo, $model, $device);
			break;
			case 'checkout': include 'views/checkoutView.php';
			$view = new CheckoutView($pageInfo, $model, $device);
			break;	
			case 'policy': include 'views/policyView.php';
			$view = new PolicyView($pageInfo, $model, $device);
			break;		
			case 'terms': include 'views/termsView.php';
			$view = new TermsView($pageInfo, $model, $device);
			break;	
			case 'sitemap': include 'views/sitemapView.php';
			$view = new SitemapView($pageInfo, $model, $device);
			break;	
			case 'royalty': include 'views/royaltyView.php';
			$view = new RoyaltyView($pageInfo, $model, $device);
			break;	
			case 'banner': include 'views/bannerView.php';
			$view = new BannerView($pageInfo, $model, $device);
			break;		
			default: include 'views/noView.php';
			$view = new NoView($pageInfo, $model, $device);
			break;
		}
		echo $view->displayPage();
	}
}

$pageSelect = new PageSelector;
$pageSelect->run();
?>