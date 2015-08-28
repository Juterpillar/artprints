<?php
class ShippingView extends View
{
	protected function displayContent()
	{
		$html .= "\t\t\t\t".'<div id="flexslider">'."\n";
		$html .= "\t\t\t\t\t".'<ul id="slides">'."\n";
		$html .= "\t\t\t\t\t\t".'<li>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a class="banner" href="#"><img class="first_banner" src="images/banners/'.$this->device['type'].'_delivery.jpg" alt="image of a delivery truck" width="'.$this->device['banner'].'" style="display: block"/></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</li>'."\n";
		$html .= "\t\t\t\t\t\t".'<li>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<a class="banner" href="#"><img class="more_banners" src="images/banners/'.$this->device['type'].'_delivery.jpg" alt="image of a delivery truck" width="'.$this->device['banner'].'" style="display:none" /></a>'."\n";
		$html .= "\t\t\t\t\t\t".'</li>'."\n";
		$html .= "\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t".'</div> <!-- flexslider -->'."\n";
		$html .= "\t\t\t\t".'<div class="icon_nav">'."\n";
		$html .= "\t\t\t\t\t".'<figure class="icon_set">'."\n";
		$html .= "\t\t\t\t\t\t".'<img class="no_mobile" src="images/bubblewrap.jpg" title="view our selection of framed art" alt="example of a framed art" width="220" height="220" />'."\n";
		$html .= "\t\t\t\t\t\t".'<figcaption>packaging info</figcaption>'."\n";
		$html .= "\t\t\t\t\t\t".'<p>We\'ve been doing this for over 10 years so we know the best ways to pack your items.  Loose prints are rolled in a sturdy tube. Framed art items are packed with sellotape on the glass, bubblewrap, protective cardboard corners, and wrapped in corrugated cardboard.  Canvas items are wrapped in bubblewrap and corrugated cardboard.</p>'."\n";
		$html .= "\t\t\t\t\t".'</figure> <!-- icon_set -->'."\n";
		$html .= "\t\t\t\t\t".'<figure class="icon_set">'."\n";
		$html .= "\t\t\t\t\t\t".'<img class="no_mobile" src="images/clock.jpg" title="view our selection of canvas prints" alt="example of a canvas print" width="220" height="220" />'."\n";
		$html .= "\t\t\t\t\t\t".'<figcaption>timing</figcaption>'."\n";
		$html .= "\t\t\t\t\t\t".'<p>Unframed prints are usually dispatched within 2-4 days. Framed art and canvas are usually dispatched within 10-14 working days. If there is a supplier issue or delay we will contact you immediately with an ETA. Please contact us if you require something by a certain date.</p>'."\n";
		$html .= "\t\t\t\t\t".'</figure> <!-- icon_set -->'."\n";
		$html .= "\t\t\t\t\t".'<figure class="icon_set">'."\n";
		$html .= "\t\t\t\t\t\t".'<img class="no_mobile" src="images/money.jpg" title="view our selection of art prints" alt="example of an art print" width="220" height="220" />'."\n";
		$html .= "\t\t\t\t\t\t".'<figcaption>cost</figcaption>'."\n";
		$html .= "\t\t\t\t\t\t".'<p>A tube of up to 10 prints costs $10 to anywhere in New Zealand.  (35 prints will be sent in 4 tubes at a total cost of $40).  Canvas/framed art cost $20 for up to 2 items in the North Island and $30 for up to 5kgs to the South Island.  Rural delivery may incur additional charges. Rates are calculated at the checkout and may vary slightly from those listed above.</p>'."\n";
		$html .= "\t\t\t\t\t".'</figure> <!-- icon_set -->'."\n";
		$html .= "\t\t\t\t".'</div> <!-- icon_nav -->'."\n";
		$html .= "\t\t\t\t".'<div id="home_text">'."\n";
		$html .= "\t\t\t\t\t".'<p><strong>tracking your order: </strong>You can track your order by going to your \'my account\' page on this website.  When you order has been dispatched, you can use the tracking number to trace your order with the courier over the phone or on their website.</p>'."\n";
		$html .= "\t\t\t\t\t".'<p><strong>damaged goods: </strong>In the remote event that your package is damaged upon arrival, please <a href="contact.html">contact</a> us. <br />'."\n";
		$html .= "\t\t\t\t\t".'<strong id="damaged">shipping addresses: &nbsp;</strong>This must be a street address as we do not deliver to post office boxes.</p>'."\n";
		$html .= "\t\t\t\t\t".'<h4><a href="index.php?page=faq">view more frequently asked questions.</a></h4>'."\n";
		$html .= "\t\t\t\t".'</div><!-- home_text -->'."\n";
		return $html;
	}
}
?>