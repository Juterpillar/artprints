<?php

class ContactView extends View
{
	protected function displayContent()
	{
		$html = "\t\t\t\t".'<div id="content">'."\n";
		
		if(isset($_POST['send'])) {
			// the contact form has been submitted so validate
			$result = $this->model->validateContactForm();
			
			// If there are no errors on the form send the email
			if ($result['ok']) {
				$eResult .= $this->model->sendContactEmail();

				// The email worked
				if ($eResult) {
					if ($this->device['type'] == 'mobile') {
						$html .= "\t\t\t\t\t".'<div class="success">'."\n";
						$html .= "\t\t\t\t\t\t".'<p>Your message has been sent, thank you!</p>'."\n";
						$html .= "\t\t\t\t\t".'</div>'."\n";
					}
					$html .= $this->displayAside();
					$html .= '<h2>thanks for your email!</h2>'."\n";
					$html .= '<p>back to the <a href="index.php?page=framed">framed |</a><a href="index.php?page=prints"> prints |</a><a href="index.php?page=canvas"> canvas</a> catalogue</p>'."\n";
				}
				// The email didn't work
				else {
					if ($this->device['type'] == 'mobile') {
						$html .= "\t\t\t\t\t".'<div class="success">'."\n";
						$html .= "\t\t\t\t\t\t".'<p class="red">something went wrong there and your message was not sent.  Please give us a call!</p>'."\n";
						$html .= "\t\t\t\t\t".'</div>'."\n";
					}
					$html .= $this->displayAside();
					$html .= '<h2>sorry'. $name.'</h2>'."\n";
					$html .= '<p class="red">something went wrong there and your message was not sent.  Please give us a call!</p>'."\n";
				}
			}
			// errors in the form so display with error messages
			else {
				if ($this->device['type'] == 'mobile') {
						$html .= "\t\t\t\t\t".'<div class="success">'."\n";
						$html .= "\t\t\t\t\t\t".'<p class="red">please correct the following in the form so your message can be sent</p>'."\n";
						$html .= "\t\t\t\t\t".'</div>'."\n";
				}
				$html .= $this->displayAside();
				$html .= "\t\t\t\t\t".'<h2 class="title">send us a message</h2>'."\n";
				$html .= "\t\t\t\t\t".'<p class="red">please correct the following so your message can be sent</p>'."\n";
				$html .= $this->displayContactForm($result);
			}	

		}
		// form hasn't been sent yet
		else {
			$html .= $this->displayAside();
			$html .= "\t\t\t\t\t".'<h2 class="title">send us a message</h2>'."\n";
			$html .= "\t\t\t\t\t".'<p>Have a question or a suggestion?<br />we\'ll reply within 24 hours</p>'."\n";
			$html .= $this->displayContactForm();
		}
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}


	// this funciton displays the side bar on the contact page
	private function displayAside() {
		$html = "\t\t\t\t\t".'<aside>'."\n";
		$html .= "\t\t\t\t\t\t".'<h3 id="phone"><i class="icon-phone"></i> phone</h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="phone_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<p>+64 212 221010</p>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<p>0800 101015</p>'."\n";
		$html .= "\t\t\t\t\t\t".'</div>'."\n";
		$html .= "\t\t\t\t\t\t".'<h3 id="address"><i class="icon-building"></i> address</h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="address_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<p>62 Cambridge Terrace,<br />Te Aro,<br />Wellington 6011,<br />New Zealand</p>'."\n";
		$html .= "\t\t\t\t\t\t".'</div>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<h3 id="map"><i class="icon-screenshot"></i> map</h3>'."\n";
		$html .= "\t\t\t\t\t\t".'<div id="map_options">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<img src="images/static_map.jpg" width="190" height="250" alt="map showing 62 Cambridge Terrace, Wellington" />'."\n";
		$html .= "\t\t\t\t\t\t".'</div>'."\n";
		$html .= "\t\t\t\t\t".'</aside>'."\n";
		return $html;
	}

	// this function displays the contact form on the contact page
	private function displayContactForm($error='') {
		if ($error){
			extract($error);
		}
		$html = "\t\t\t\t\t".'<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">'."\n";
		$html .= "\t\t\t\t\t\t".'<label for="name">name</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="name" value="'.htmlentities(stripslashes($_POST['name'])).'" />'."\n";
		$html .= $name;
		$html .= "\t\t\t\t\t\t".'<label for="phone">phone (optional)</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="phone" value="'.htmlentities(stripslashes($_POST['phone'])).'" />'."\n";
		$html .= "\t\t\t\t\t\t".'<label for="email">email</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="email" name="email" value="'.htmlentities(stripslashes($_POST['email'])).'" />'."\n";
		$html .= $email;
		$message = 'Hi artprints...';
		if (isset($_POST['message'])) {
			$message = htmlentities(stripslashes($_POST['message']));
		}
		$html .= "\t\t\t\t\t\t".'<label for="message">message &nbsp;<i class="icon-pencil"></i></label>'."\n";
		$html .= "\t\t\t\t\t\t".'<textarea rows="20" cols="40" name="message">'.$message.'</textarea>'."\n";
		$html .= $error['message'];
		$html .= "\t\t\t\t\t\t".'<label for="url" class="url_label">Leave this blank</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="url" class="url_input" value="" />'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="submit" name="send" value="send"  />'."\n";
		$html .= "\t\t\t\t\t".'</form>'."\n";
		return $html;
	}
}
?>