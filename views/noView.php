<?php

class NoView extends View
{
	// this page displays by default if the page requested in the URL does not match any from the database

	protected function displayContent() {
		$html .= "\t\t\t\t".'<div id="content" class="no_view">'."\n";	
		$html .= "\t\t\t\t\t".'<h2 class="title">Whoops!  The page you are looking for does not exist.</h2>'."\n";	
		if ($this->device['type'] != 'mobile') {	
			$html .= "\t\t\t\t".'<p class="shop_link">If you were expecting to see a print, canvas or framed print, that product may no longer be available.<br /><a href="index.php?page=contact">Contact us</a> for further information or click an image below to continue shopping.</p>'."\n";	
			$html .= "\t\t\t\t".'<div class="icon_nav">'."\n";
			$html .= "\t\t\t\t\t".'<a href="index.php?page=framed"><figure class="icon_set">'."\n";
			$html .= "\t\t\t\t\t\t".'<img src="images/framed_icon.jpg" title="view our selection of framed art" alt="example of a framed art" width="240" height="180" />'."\n";
			$html .= "\t\t\t\t\t\t".'<figcaption>framed art</figcaption>'."\n";
			$html .= "\t\t\t\t\t".'</figure></a> <!-- icon_set -->'."\n";
			$html .= "\t\t\t\t\t".'<a href="index.php?page=canvas"><figure class="icon_set">'."\n";
			$html .= "\t\t\t\t\t\t".'<img src="images/canvas_icon.jpg" title="view our selection of canvas prints" alt="example of a canvas print" width="240" height="180" />'."\n";
			$html .= "\t\t\t\t\t\t".'<figcaption>canvas</figcaption>'."\n";
			$html .= "\t\t\t\t\t".'</figure></a> <!-- icon_set -->'."\n";
			$html .= "\t\t\t\t\t".'<a href="index.php?page=prints"><figure class="icon_set">'."\n";
			$html .= "\t\t\t\t\t\t".'<img src="images/print_icon.jpg" title="view our selection of art prints" alt="example of an art print" width="240" height="180" />'."\n";
			$html .= "\t\t\t\t\t\t".'<figcaption>prints</figcaption>'."\n";
			$html .= "\t\t\t\t\t".'</figure></a> <!-- icon_set -->'."\n";
			$html .= "\t\t\t\t".'</div> <!-- icon_nav -->'."\n";
		}
		else {
			$html .= "\t\t\t\t".'<p class="shop_link">Click a link below to continue shopping.</p>'."\n";
		}
		$html .= "\t\t".'</div><!-- content -->'."\n";
		return $html;
	}
}
?>