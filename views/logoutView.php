<?php

class LogoutView extends View
{
	// this view logs the user out then displays a message confirming they have been logged out
	
	protected function displayContent()  {	
		// clear the session
		$this->model->logout();
		// check the session is cleared
		if(!isset($_SESSION['name'])) {
			$html = "\t\t\t\t".'<div id="content">'."\n";
			$html .= "\t\t\t\t\t".'<h2>'.$this->pageInfo['pageHeading'].'</h2>'."\n";
			$html .= "\t\t\t\t\t".'<h3>You have been successfully logged out of your Artprints account.</h3>'."\n";
			$html .= "\t\t\t\t\t".'<p class="push_page"><a href=index.php?page=login>Login to your account</a></p>'."\n";
			
		}
		// something went wrong and the logout failed
		else {
			$html .= "\t\t\t\t\t".'<p>Sorry, you are still logged in.  Please <a href="index.php?page=logout">try again</a></p>'."\n";
		}
		$html .= "\t\t\t\t".'</div><!--- content -->'."\n";
		return $html;
	}
}
?>