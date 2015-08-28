<?php

class ArtistView extends View
{
	// this class displays the registration form for users to sign up
	
	protected function displayContent()  {
		if (isset($_GET['clear'])) {
			unset($_POST);
			unset($artist_info);
			unset($_GET);
		}

		if ($_SESSION['access'] != 'admin') {
			header("Location: index.php");
		}

		$html = $this->model->displaySideBarOptions();
		$html .= "\t\t\t\t".'<div id="content">'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageHeading'].'</h2>'."\n";

		// if an artist has already been selected for editing
		if (isset($_GET['artistID'])) {
			$artist_info = $this->model->getArtists($_GET['artistID']);
			$html .= "\t\t\t\t\t".'<h4>edit this artist\'s details below</h4>'."\n";
			$html .= $this->displayArtistForm('', $artist_info);
			$html .= "\t\t\t\t\t".'<h4>or</h4>'."\n";
			$html .= $this->model->selectArtistForm();
			$html .= "\t\t\t\t\t".'<h4>or</h4>'."\n";
			$html .= "\t\t\t\t\t".'<a href="index.php?page=adminartist&amp;clear=clear"><h4>add a new artist</h4></a>'."\n";
		}

		// if admin has clicked to add an artwork
		else if (isset($_GET['edit'])) {
			$html .= $this->model->selectArtistForm();
			$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
			return $html;
			
		}

		// if admin has click to edit an artwork
		else if (isset($_GET['add'])) {
			$html .= "\t\t\t\t\t".'<h4>add a new artist below</h4>'."\n";
			$html .= $this->displayArtistForm();
			$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
			return $html;
		}
		
		// check if an artist has been selected
		else if (isset($_POST['artist_select']) || isset($_POST['update'])) {
			// if admin has submtited the form to update an artist
			if ($_POST['update']) {
				// form submitted so validate
				$result = $this->model->validateArtistForm();
				if ($result['ok']) {
					// no errors in the form so add to the database
					$update = $this->model->editArtist($_POST['artist_id']);
					// check if added successful
					if ($update == 'success') {
						$html .= "\t\t\t\t\t".'<h3 class="validation green">congratulations <br />You have successfully updated the details for artist <a href="index.php?page=adminartist&amp;artistID='.$_POST['artist_id'].'">'.$_POST['known_name'].'</a></h3>'."\n";
						$html .= $this->model->selectArtistForm();
						$html .= "\t\t\t\t\t".'<h4>or add a new artist below</h4>'."\n";
						unset($_POST);
						$html .= $this->displayArtistForm();
					} 
					else {
						// wasn't added successsful
						$html .= "\t\t\t\t\t".'<h3 class="validation red">You made no changes to artist <a href="index.php?page=adminartist&amp;artistID='.$_POST['artist_id'].'">'.$_POST['known_name'].'</a></h3>'."\n";
						$html .= $this->model->selectArtistForm();
						$html .= "\t\t\t\t\t".'<h4>or add a new artist below</h4>'."\n";
						unset($_POST);
						$html .= $this->displayArtistForm();
 					}	
				} 
				// validation returned errors
				else {
					$html .= "\t\t".'<h3 class="validation red">please correct the following errors</h3>'."\n";
					$html .= $this->displayArtistForm($result, $artist_info);				}
				}	

			// the changes have not yet been submitted
			else {
				$artist_info = $this->model->getArtists($_POST['all_artists']);
				$html .= "\t\t\t\t\t".'<h4>edit this artist\'s details below</h4>'."\n";
				$html .= $this->displayArtistForm('', $artist_info);
				$html .= "\t\t\t\t\t".'<h4>or</h4>'."\n";
				$html .= $this->model->selectArtistForm();
				$html .= "\t\t\t\t\t".'<h4>or</h4>'."\n";
				$html .= "\t\t\t\t\t".'<a href="index.php?page=adminartist&amp;clear=clear"><h4>add a new artist</h4></a>'."\n";
			}
		}

		// check if an submitted form to add a new artist
		else if (isset($_POST['add'])) {
			// if admin has submtited the form to update an artist
			// form submitted so validate
			$result = $this->model->validateArtistForm();
			if ($result['ok']) {
				// no errors in the form so add to the database
				$id = $this->model->editArtist();
				// check if added successful
				if (is_numeric($id)) {
					$html .= "\t\t\t\t\t".'<h3 class="validation green">congratulations <br />You have successfully added artist <a href="index.php?page=adminartist&amp;artistID='.$id.'">'.$_POST['known_name'].'</a></h3>'."\n";
					$html .= $this->model->selectArtistForm();
					$html .= "\t\t\t\t\t".'<h4>or add a new artist below</h4>'."\n";
					unset($_POST);
					$html .= $this->displayArtistForm();
				} 
				else {
					// wasn't added successsful
					$html .= "\t\t\t\t\t".'<h3 class="validation red">sorry, artist '.$_POST['known_name'].' was unable to be added</h3>'."\n";
					$html .= $this->displayArtistForm();
					$html .= $this->model->selectArtistForm();
					}	
			} 
			// validation returned errors
			else {
				$html .= "\t\t".'<h3 class="validation red">please correct the following errors</h3>'."\n";
				$html .= $this->displayArtistForm($result, $artist_info);				}
			}	

		// no artist selected
		else {
			$html .= $this->model->selectArtistForm();
			$html .= "\t\t\t\t\t".'<h4>or add a new artist below</h4>'."\n";
			$html .= $this->displayArtistForm();
		}
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;		
	}




	// this function displays the artist form and shows any validation errors if submitted
	private function displayArtistForm($error="", $artist_info='') {
		if ($artist_info && !$error) {
			extract($artist_info[0]);
			$_POST['first_name'] = $artistFirstName;
			$_POST['last_name'] = $artistLastName;
			$_POST['known_name'] = $artistName;
			$_POST['phone'] = $artistPhone;
			$_POST['email'] = $artistEmail;
			$_POST['bank_account'] = $artistBankDets;
			$_POST['bio'] = $artistBio;
			$_POST['hidden'] = $artistHidden;
			$_POST['artist_id'] = $artistID;
			$mode = 'update artist';
			$name = 'update';
		}
		else if(isset($_POST['update']) && $error) {
			$mode = 'update artist';
			$name = 'update';
		}
		else {
			$mode = 'add artist';
			$name = 'add';
		}
		$html = "\t\t\t\t\t".'<form action="index.php?page=adminartist" method="post" class="form">'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="hidden" name="artist_id" value="'.$_POST['artist_id'].'" />'."\n";
		// first name
		$html .= "\t\t\t\t\t\t".'<label for="first_name">artist\'s first name</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="first_name" value="'.htmlentities(stripslashes($_POST['first_name'])).'" />'."\n";
		$html .= $error['first_name'];
		// last name
		$html .= "\t\t\t\t\t\t".'<label for="last_name">artist\'s last name</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="last_name" value="'.htmlentities(stripslashes($_POST['last_name'])).'" />'."\n";
		$html .= $error['last_name'];
		// known as
		$html .= "\t\t\t\t\t\t".'<label for="known_name">artist is known as</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="known_name" value="'.htmlentities(stripslashes($_POST['known_name'])).'"/>'."\n";
		$html .= $error['known_name'];
		// phone
		$html .= "\t\t\t\t\t\t".'<label for="phone">phone</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="phone" value="'.htmlentities(stripslashes($_POST['phone'])).'" />'."\n";
		$html .= $error['phone'];
		// email
		$html .= "\t\t\t\t\t\t".'<label for="email">email</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="email" value="'.htmlentities(stripslashes($_POST['email'])).'" />'."\n";
		$html .= $error['email'];
		// bank account 
		$html .= "\t\t\t\t\t\t".'<label for="bank account">bank account #</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="type" name="bank_account" value="'.htmlentities(stripslashes($_POST['bank_account'])).'" />'."\n";
		$html .= $error['bank_account'];
		// bio
		$html .= "\t\t\t\t\t\t".'<label for="bio">bio</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<textarea rows="20" cols="40" name="bio">'.htmlentities(stripslashes($_POST['bio'])).'</textarea>'."\n";
		// visibility
		$html .= "\t\t\t\t\t\t".'<label for="hidden">visibility/hide</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<select name="hidden">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<option value="">visible</option>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<option value="hidden"';
		if ($_POST['hidden'] != '') {
			$html .= ' selected="selected"';
		}
		$html .= '>hidden</option>'."\n";
		$html .= "\t\t\t\t\t\t".'</select>'."\n";
		// password
		$html .= "\t\t\t\t\t\t".'<label for="password">password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password" value="'.htmlentities(stripslashes($_POST['password'])).'" />'."\n";
		$html .= $error['password'];
		$html .= "\t\t\t\t\t\t".'<input type="submit" name="'.$name.'" value="'.$mode.'" />'."\n";
		$html .= "\t\t\t\t\t".'</form>'."\n";
		return $html;
	}
}
?>