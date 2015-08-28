<?php

class RoyaltyView extends View
{
	// this view shows order and profile info for the logged in customer
	
	protected function displayContent()  {
				// redirect user if not logged in as admin
		if ($_SESSION['access'] != 'admin') {
			header("Location: index.php");
		}
		$html = '';
		$html .= $this->model->displaySideBarOptions();
		$html .= "\t\t\t\t".'<div id="content" class="width">'."\n";
		
		if (isset($_GET['r'])) {
			switch ($_GET['r']) {
			   
			    // months
			    case 'm':
			        if (isset($_POST['date_select'])) {
			        	$month = $_POST['month'];
			        	$year = $_POST['year'];
			    		$dispatched = $this->model->getDispatchedArt('', 'month');
			    		$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
			    		if (is_array($dispatched)) {
			    			$html .= "\t\t\t\t\t".'<h2 class="title">royalities for '.$months[$month].' '.$year.'</h2>'."\n";
			    			$html .= $this->model->displayEachRoyalty($dispatched, 'month');
			    			$html .= $this->model->displaySelect('months', 'choose a month and year from the select boxes below');
			    		}
			    		else {
			    			$html .= "\t\t\t\t\t".'<h2 class="title">there are no royalties for '.$months[$month].' '.$year.'</h2>'."\n";
			    			$html .= $this->model->displaySelect('months', 'choose a month and year from the select boxes below');
			    		}		    		
			    	}
			    	// display the form to select a month
			    	else {
			        	$html .= $this->model->displaySelect('months', 'choose a month and year from the select boxes below');
			        }break;
			    
			    // artist
			    case 'a':
			    	$html .= "\t\t\t\t\t".'<h2 class="title">royalities by artist</h2>'."\n";
			    	// an artist
			    	if (isset($_POST['artist_select'])) {
			    		$artist = explode(':', $_POST['all_artists']);
						$id = $artist[0];
						$name = $artist[1];
						$dispatched = $this->model->getDispatchedArt($id);
						if (is_array($dispatched)) {
							$html .= $this->model->displayEachRoyalty($dispatched, 'artist');
						}
						else {
							$html .= "\t\t\t\t\t".'<h3 class="validation red">there are no dispatched items for artist '.$name.'</h3>'."\n";
							$html .= $this->model->selectArtistForm('royalty');	
						}	
			    	}
			    	// display form to select a customer from
			    	else {
						$html .= $this->model->selectArtistForm('royalty');	
					}
			        break;
			    
			    // money outstanding
			    case 'u':
			    	if (isset($_POST['record'])) {
			    		$result = $this->model->validateRoyaltyPayment();
			    		// validate fine so send query to add payment to the database
			    		if ($result['ok'] == true) {
			    			$record = $this->model->recordRoyaltyPayment();
			    			if ($record == true) {
			    				$html .= "\t\t\t\t\t".'<h3 class="validation green">thank you, your payment has been recorded</h3>'."\n";
			    				$html .= "\t\t\t\t\t".'<h2 class="title">unpaid royalties</h2>'."\n";
			    				$html .= $this->model->allRoyalties(true);
			    				unset($_POST);
			    			}
			    			else {
			    				$html .= "\t\t\t\t\t".'<h3 class="validation red">sorry, your payment was not recorded</h3>'."\n";
			    				$html .= "\t\t\t\t\t".'<h4 class="validation red">please try again or contact your administrator</h4>'."\n";
			    			}
			    		}
			    		// validation errors so redisplay form
			    		else {
			    			$html .= "\t\t\t\t\t".'<h2 class="title">unpaid royalties</h2>'."\n";
			    			$html .= "\t\t\t\t\t".'<h3 class="validation red">please correct the errors below</h3>'."\n";
			    		}
			    	}
			    	else {
			    		$html .= "\t\t\t\t\t".'<h2 class="title">unpaid royalties</h2>'."\n";
			    		$html .= $this->model->allRoyalties(true);
			    	}
			    	$html .= $this->displayRoyaltyPaidForm($result);
			        break;
			    }
			}
			else {
				$html .= "\t\t\t\t\t".'<h2 class="title">all royalties</h2>'."\n";
				$html .= $this->model->allRoyalties();
			}
			$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
			return $html;
	}

	// displays the form to record a new royalty payment made
	private function displayRoyaltyPaidForm($error='') {
		if ($_SESSION['access'] == 'admin') {
			if (isset($error)) {
				extract($error);
			}
			$html .= "\t\t\t\t\t".'<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form">'."\n";
			$html .= "\t\t\t\t\t\t".'<h3>record a new royalty payment</h3>'."\n";
			$html .= "\t\t\t\t\t\t".'<label for="payment">payment amount</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<input type="text" name="payment" value="$'.sprintf("%01.2f", $_POST['payment']).'"/>'."\n";
			$html .= $payment;
			// artist's name
			$allArtists = $this->model->getArtists('', 'all');
			$html .= "\t\t\t\t\t\t".'<label for="artist_name">artist\'s name</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<select name="artistID">'."\n";
			$html .= "\t\t\t\t\t\t\t".'<option value="0">Please select...</option>'."\n";
			foreach ($allArtists as $artist) {
				$html .= "\t\t\t\t\t\t\t".'<option value="'.$artist['artistID'];
				if ($_POST['artistID'] == $artist['artistID']) {
					$html .= '" selected="selected';
				}
				$html .= '">'.$artist['artistName'].'</option>'."\n";
			}
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
			$html .= $artistID;
			$html .= "\t\t\t\t\t\t".'</select>'."\n";
			$html .= "\t\t\t\t\t\t".'<label for="notes">reference details</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<textarea rows="5" cols="20" name="notes">'.htmlentities(stripslashes($orderNotes)).'</textarea>'."\n";
			$html .= "\t\t\t\t\t\t".'<label for="password">password</label>'."\n";
			$html .= "\t\t\t\t\t\t".'<input type="password" name="password" />'."\n";
			$html .= $password;
			$html .= "\t\t\t\t\t\t".'<input type="submit" name="record" value="record payment" />'."\n";
			$html .= "\t\t\t\t\t".'</form>'."\n";
			return $html;
		}
	}

}
?>