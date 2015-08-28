<?php

class LoginView extends View
{
	protected function displayContent()
	{
		// if the user is already logged in redirect them to their account page
		if (isset($_SESSION['name'])) {   
			header('Location: index.php?page=account');
		}
		// check if the form has been submitted
		if (isset($_POST['login'])) {
			// form submitted so validate
			$result = $this->model->processLogin();
			// login success so redirect to account page
			if (isset($_SESSION['name']) && $_SESSION[access] == 'admin') {
				header('Location: index.php?page=dashboard');		
			}
			else if (isset($_SESSION['name']) && ($_SESSION[status]) == 'active') {
				header('Location: index.php?page=account');
			}
			// login failed so show user
			else {
				$html .= "\t\t\t\t".'<div id="content">'."\n";
				$html .= "\t\t\t\t\t".'<h2 class="title">login</h2>'."\n";
				// login not successful
				if ($result == 'incorrect') {
					$html .= "\t\t\t\t\t".'<h3 class="validation">sorry, your email or password is incorrect</h3>'."\n";
					$html .= $this->displayLoginForm();
				}
				else if ($result == 'inactive' || $result == 'frozen') {
					$html .= "\t\t\t\t\t".'<h3 class="validation">sorry, your account is not active</h3>'."\n";
					$html .= "\t\t\t\t\t".'<p class="end">please <a href="index.php?page=contact">contact us</a> to discuss your account status</p>'."\n";
				}
				// validation returned errors
				else if (is_array($result)){
					$html .= "\t\t\t\t\t".'<h3 class="validation">please correct your email and password</h3>'."\n";
					$html .= $this->displayLoginForm($result);				
				}
			}
		}	
		// form hasn't been submitted yet	
		else {
			$html = "\t\t\t\t".'<div id="content">'."\n";
			$html .= "\t\t\t\t\t".'<h2 class="title">welcome back</h2>'."\n";
			$html .= $this->displayLoginForm();
		}
		return $html;		
	}

	// this function displays the login form
	private function displayLoginForm($error='') {
		$html = "\t\t\t\t\t".'<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">'."\n";
		$html .= "\t\t\t\t\t\t".'<label for="email">email</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="email" name="email" />'."\n";
		$html .= $error['email'];
		$html .= "\t\t\t\t\t\t".'<label for="password">password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password" />'."\n";
		$html .= $error['password'];
		$html .= "\t\t\t\t\t\t".'<input type="submit" name="login" value="login" />'."\n";
		$html .= "\t\t\t\t\t".'</form>'."\n";
		$html .= "\t\t\t\t\t".'<p class="forgot">Help!<a href="index.php?page=contact"> I forgot my password!</a></p>'."\n";
		$html .= "\t\t\t\t\t".'<p class="help">I want to <a href="index.php?page=register">register for an account</a></p>'."\n";
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}
}
?>