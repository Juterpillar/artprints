<?php

class FaqView extends View
{
	protected function displayContent()
	{
		$html = "\t\t\t\t".'<div id="content">'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">frequently asked questions</h2>'."\n";
		$html .= "\t\t\t\t\t".'<div class="icon_nav">'."\n";
		// questions about artists
		$html .= "\t\t\t\t\t\t".'<div class="question_topics">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<h3 id="artists">artists</h3>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<div class="questions" id="artists_q">'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<ul>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>How do I get my work in your catalogue?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Contact Linda Blake (lindab@afas.co.nz) with your portfolio.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>How do I get paid?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Artist royalties are paid quarterly into your bank account or as agreed in your contract.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>How much do I get paid?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">You will recieve a royalty for every image of yours purchased.  The royalty amount is calculated based on size and other factors.  Linda Blake will discuss this with you when your contract is written.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</div><!-- questions -->'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- question_topics -->'."\n";
		// questions about prints
		$html .= "\t\t\t\t\t\t".'<div class="question_topics">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<h3 id="prints">prints</h3>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<div class="questions" id="prints_q">'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<ul>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>What exactly do the print dimensions refer to?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">The dimensions refers to the image size.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>How long does it take to receive my print?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Prints typically take 3-7 business days to arrive after an order is placed.  If a print is out of stock or is likely to take longer than this we will inform you straight away.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</div><!-- questions -->'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- question_topics -->'."\n";
		// questions about framed prints
		$html .= "\t\t\t\t\t\t".'<div class="question_topics clear">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<h3 id="framed">framed prints</h3>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<div class="questions" id="framed_q">'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<ul >'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Are the framed prints ready to hang?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Yes!  All our framed prints come with tri-hanger hooks and string on the back so they are ready to be displayed on the wall.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>How long does it take to receive my framed print?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Framed prints are usually shipped within 10 business days.  If there is a supplier issue or delay we will let you know immediately with an ETA. </li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Can I change the frame/matboard/glass?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Yes!  If you wish to customise a framed print item, please contact us.  Additional charges may apply.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>What exactly do the framed print dimensions refer to?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">The dimensions for framed art products refer to full size of the item, including the matboard and frame.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</div><!-- questions -->'."\n";
		$html .= "\t\t\t\t\t\t".'</div>'."\n";
		// questions about canvas
		$html .= "\t\t\t\t\t\t".'<div class="question_topics">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<h3 id="canvas">canvas</h3>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<div class="questions" id="canvas_q">'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<ul>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Is the canvas ready to hang?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Yes!  All our canvas come with tri-hanger hooks and string on the back so they are ready to be displayed on the wall.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>How deep is the stretcher frame?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">We use a 30mm stretcher bar.  20mm or 40mm stretcher bars may be available on request.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>How long does it take to receive my canvas?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Canvas items are usually shipped within 10 business days.  If there is a supplier issue or delay we will let you know immediately with an ETA. </li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>What exactly do the canvas dimensions refer to?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">The dimensions for canvas products refer to full size of the item, from the front edge to edge.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</div><!-- questions -->'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- question_topics -->'."\n";
		// questions about payment
		$html .= "\t\t\t\t\t\t".'<div class="question_topics clear">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<h3 id="payment">payment</h3>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<div class="questions" id="payment_q">'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<ul>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Does the price shown include GST?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">The RRP includes GST.  If you are logged in as a wholesaler the price you see is GST exclusive.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>What payment methods do you accept?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">We accept credit card payments made with Visa and Mastercard. All our credit card transactions are processed using a secure, transaction processing facility Payment Express.  We also accept internet banking.  Please allow extra time for funds to clear into our account. Our bank information is:<br />'."\n";
		$html .= 'Bank: BNZ<br />'."\n";
		$html .= 'Account Number: 02-0560-0137697-000</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>When is my payment due?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">We require a 50% deposit when your order is placed and the balance is due by te 20th of the following month.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</div><!-- questions -->'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- question_topics -->'."\n";
		// general questions
		$html .= "\t\t\t\t\t\t".'<div class="question_topics">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<h3 id="general">general</h3>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<div class="questions" id="general_q">'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<ul>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Can I get it in a different size?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">More often than not this is possible.  Contact us if you need a particular size.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>How accurate are the colours?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">We use colour managment to ensure the best colour match on our prints.  However, as all monitors are calibrately differently, we cannot guaratee that the prints will exactly match the colours you see on your screen.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>I am not a wholesale customer.  Can I still place an order?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Unfortunately not. You must be a company registered retailer with a GST number to purchase from this website. If you are not a retailer, most of our prints are availabe for purchase from <a href="http://www.afas.co.nz">afas.co.nz</a>.  <a href="register.html">Click here if you would like to apply to become an account holder.</a></li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Can I change my order once it is submitted?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">You can change your order if it hasn\'t been processed. <a href="contact.html">Contact us</a>if you want to change your order.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</div><!-- questions -->'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- question_topics -->'."\n";
		$html .="\t\t\t\t\t\t".'<div id="shipping_link" lass="question_topics clear">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<h3><a href="index.php?page=shipping">I have a question about shipping</a></h3>'."\n";
		$html .= "\t\t\t\t\t\t".'</div><!-- shipping_link -->'."\n";
		$html .= "\t\t\t\t\t".'</div> <!-- icon_nav -->'."\n";
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}
}
?>