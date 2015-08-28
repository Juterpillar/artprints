<?php

class SitemapView extends View
{
	// this class displays the sitemap

	protected function displayContent() {	
		$html .= "\t\t\t\t".'<div id="content">'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageHeading'].'</h2>'."\n";
		// if not viewed on mobile show navigation (already shows if on mobile)
		$html .= "\t\t\t\t".'<div class="fineprint shadow">'."\n";
		if ($this->device['type'] != 'mobile') {
			$links = array('home', 'framed', 'prints', 'canvas', 'about', 'contact', 'faq', 'shipping');
			$links1 = array('register', 'login', 'cart', 'checkout', 'policy', 'terms', 'sitemap');
			$html .= "\t\t\t\t".'<ul>'."\n";
			// add href and name for each link
			foreach($links as $link) {
				$html .= "\t\t\t\t\t".'<li><a href="index.php?page='.$link.'">'.$link.'</a>';
			}	
			$html .= "\t\t\t\t".'</ul>'."\n";
			$html .= "\t\t\t\t".'<ul>'."\n";
			// add href and name for each link
			foreach($links1 as $link) {
				$html .= "\t\t\t\t\t".'<li><a href="index.php?page='.$link.'">'.$link.'</a>';
			}	
			$html .= "\t\t\t\t".'</ul>'."\n";
			$html .= "\t\t\t\t".'</div><!-- fineprint -->'."\n";
			$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		} 
		// viewing on mobile so already has all links showing in the footer
		$html .= "\t\t\t\t".'</div>'."\n";
		return $html;
	}
}
?>