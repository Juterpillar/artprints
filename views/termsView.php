<?php
class TermsView extends View
{
	protected function displayContent()
	{
		$html .= "\t\t\t\t".'<div id="content">'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageHeading'].'</h2>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<div class="fineprint">'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<ul>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">By purchasing products from Artprints Publishers and Distributors ("Artprints", "we", "us", "our") or using this website, the purchaser (the "Customer", "you", "your"), agrees to the following terms and conditions of sale (the "Terms").</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Consumer Protection:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Where the provisions of the Consumer Guarantees Act 1993 or any mandatory legislation in your jurisdiction that cannot legally be excluded applies, these terms will be read subject to the application of that legislation, and in the case of any conflict, the provisions of that legislation will apply.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Limitation of Liability: </li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Artprints shall not be liable to the Customer for any incidental, indirect, special or consequential damages arising out of or in connection with the purchase, use or performance of products or services from Artprints. In no case shall Artprints\'s maximum liability exceed the cost of the relevant original order.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Refunds:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer"> Artprints will comply with its obligations under the Consumer Guarantees Act or any other applicable consumer protection legislation that cannot be excluded. Subject to any such applicable laws, refunds or replacement products or services will be given at the discretion of company management.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Addressing:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">If a product is found to be mis-addressed by Artprints then Artprints shall be responsible for the re-delivery, replacement or refunding of the product. If the Customer is found to have given an incorrect or insufficient address, then Artprints will not refund or resend the product and all responsibility for correcting delivery will be borne by the Customer.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i> Incorrect Bank Account Number:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">If the Customer credits money to the incorrect bank account number, and the correct number was provided to the Customer by Artprints, then Artprints will not accept any responsibility for correcting payment and goods will not be dispatched by Artprints until Artprints has received payment in full.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Pricing errors: </li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Although we do our best to ensure prices stated on this website are accurate, errors do occur from time to time. Where a pricing error occurs (subject to applicable law that cannot be excluded), we will not be bound by the incorrect pricing stated, and reserve the right to cancel your purchase.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Cancelling Orders:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Artprints reserves the right to cancel orders for any reason or to cancel the purchase agreement after order confirmation, including (without limitation) where Artprints believes the order to be fraudulent or constitute a mis-use of a promotional or marketing activity or where an error has occurred including but not limited to errors relating to pricing. If we do cancel your order or purchase agreement, we accept no liability for any resulting damages or costs suffered by you, however we will refund the purchase price to you (where it has already been paid).</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>GST:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer"> Prices are New Zealand GST exclusive for logged in registered users unless otherwise stated.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Site Access:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Artprints hereby grants you permission to access and view this site. You may not download the content appearing on this site for commerical use. You agree not to use any device, method or software to interfere with the proper functioning of this site including, but not limited to: spiders, robots, avatars, agents, data mining, gathering or extraction tools and attempts to probe, scan or test or breach security and authentication measures.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<ul>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Copyright & Trademarks:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">You agree that the content you access, view or download on this site and the software used to create it, remains the property of Artprints Limited or its Licensors as defined by New Zealand and International Intellectual Property Laws, including but not limited to Copyright and Trademark laws. You agree not to modify or alter such content in any way, or make it available to any third parties.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Stock Availability:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">All items sold via the Artprints website are subject to availability and Artprints reserves the right not to accept or cancel any order for products that are out of stock or in the event that the agreement with the artist has been terminated.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Delivery Estimates:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Artprints makes every effort practical to ensure that delivery timeframes given are accurate. However the delivery times stated for products on this site assume that the stock availability information provided by our suppliers is accurate and that delivery times are not adversely affected by unforeseen events. By placing an order, you agree not to hold Artprints liable for any loss or damage directly or indirectly arising out of or in connection with any delay or failure to deliver within the estimated time frame.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Communications:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">By creating an account with Artprints you consent to receiving electronic communications from Artprints. These communications will include: account status updates, order status updates, promotional and marketing materials, and other information relating to the service. Your personal details will not be made available to any third party. For more information please see our <a href="index.php?page=policy">Privacy Policy.</a></li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Notice of Copyright Infringement:</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer"> Artprints respects the copyrights of others. If you feel that data is used in Artprints’s product catalogue in a manner that constitutes an infringement of your copyright under applicable copyright legislation , please write to us at:<br />
			P O Box 9000<br />
    		Marion Street<br />
    		Wellington<br />
    		New Zealand<br />
    		<strong>Please include the following details:</strong><br />
    		1. Identification of the copyrighted work that has allegedly been infringed upon and that you wish to have removed. Please include a web address (URL);<br />
        	2. A statement that the complaining party believes that infringement of the complaining party\'s copyright has occurred and the reasons for such belief;<br />
        	3. A statement that you are the copyright holder or authorised to act on their behalf; and contact details including an email address where the complaining party may be contacted.</li>'."\n";
    	$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Linked Sites: </li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer"> This site may contain links to other websites operated by third parties. Artprints is not liable for those sites or any content or information such sites contain.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Disclaimer</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Artprints would like to welcome you to this website, we have worked hard to make it as user-friendly and informative as possible. When viewing the pages of this website, you acknowledge that you have read and accepted the following disclaimer.<br />
			The information on this website is provided "as is" and for general information only. Artprints has provided this information for the benefit of users in good faith and with reasonable care. However, errors and omissions in this website and the information on it may occur from time to time and Artprints does not guarantee that the website will always operate or be error free.<br />
			Accordingly, to the maximum extent permitted by law, Artprints does not accept any liability for the use of or inability to use this site, or for any loss or damage which may directly or indirectly result from any opinion, information, advice, representation or omission contained on this web site.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</div><!-- fineprint -->'."\n";
		$html .= "\t\t\t\t\t".'</div><!-- home_text -->'."\n";
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}
}
?>

