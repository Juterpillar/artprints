<?php
class PolicyView extends View
{
	// this view shows the policy fineprint - link to this page is in the footer
	protected function displayContent()
	{
		$html = "\t\t\t\t".'<div id="content">'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageHeading'].'</h2>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<div class="fineprint">'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<ul>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>What information do we collect?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">We collect information from you when you register on our site.  When ordering or registering on our site, as appropriate, you may be asked to enter your: name, e-mail address, mailing address or phone number. You may, however, visit our site anonymously.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>What do we use your information for?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Any of the information we collect from you may be used in one of the following ways:<br />
			<strong>To personalize your experience:</strong><br />
			Your information helps us to better respond to your individual needs.<br /> 
			<strong>To improve our website:</strong><br />
			We continually strive to improve our website offerings based on the information and feedback we receive from you.<br /> 
			<strong>To improve customer service:</strong><br />
			Your information helps us to more effectively respond to your customer service requests and support needs.<br />
			<strong>To process transactions</strong><br />
			Your information, whether public or private, will not be sold, exchanged, transferred, or given to any other company for any reason whatsoever, without your consent, other than for the express purpose of delivering the purchased product or service requested.<br />
			<strong>To administer a site feature</strong><br />
			This may include a contest, promotion, survey or other<br />
			<strong>To send periodic emails</strong><br />
			The email address you provide for order processing, may be used to send you information and updates pertaining to your order, in addition to receiving occasional company news, updates, related product or service information, etc.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>How do we protect your information?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">We implement a variety of security measures to maintain the safety of your personal information when you place an order or enter, submit, or access your personal information.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'<ul>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Do we use cookies?</li>'."\n";	
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">We do not use cookies.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Do we disclose any information to outside parties?</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">We do not sell, trade, or otherwise transfer to outside parties your personally identifiable information. This does not include trusted third parties who assist us in operating our website, conducting our business, or servicing you, so long as those parties agree to keep this information confidential. We may also release your information when we believe release is appropriate to comply with the law, enforce our site policies, or protect ours or others rights, property, or safety. However, non-personally identifiable visitor information may be provided to other parties for marketing, advertising, or other uses.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Registered Users</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">All registered users of our site may make any changes to their information at anytime by logging in and going to the \'Your Account\' page.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Terms and Conditions</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">Please also visit our Terms and Conditions section establishing the use, disclaimers, and limitations of liability governing the use of our website at <a href="index.php?page=terms">terms and conditions</a></li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Your consent</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">By using our site, you consent to our online privacy policy.</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Changes to our Privacy Policy</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">If we decide to change our privacy policy, we will post those changes on this page.  This policy was last modified on 7 May 2013</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li><i class="icon-question-sign"></i>Contacting us</li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t\t".'<li class="answer">If there are any questions regarding this privacy policy you may  <a href="index.php?page=contact">contact us</a></li>'."\n";
		$html .= "\t\t\t\t\t\t\t\t".'</ul>'."\n";
		$html .= "\t\t\t\t\t\t\t".'</div><!-- fineprint -->'."\n";
		$html .= "\t\t\t\t\t".'</div><!-- home_text -->'."\n";
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}
}
?>

