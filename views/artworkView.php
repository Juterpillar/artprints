<?php

class ArtworkView extends View
{
	// this class displays the registration form for users to sign up
	
	protected function displayContent()  {
		if ($_SESSION['access'] != 'admin') {
			header("Location: index.php");
		}

		$html = $this->model->displaySideBarOptions();
		$html .= "\t\t\t\t".'<div id="content">'."\n";
		$html .= "\t\t\t\t\t".'<h2 class="title">'.$this->pageInfo['pageHeading'].'</h2>'."\n";

		// if admin has clicked to add an artwork
		if (isset($_GET['edit'])) {
			$html .= $this->model->selectArtworkForm();
			$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
			return $html;	
		}

		// if admin has click to edit an artwork
		else if (isset($_GET['add']) && !isset($_POST['add'])) {
			$html .= "\t\t\t\t\t".'<h4>add a new artwork below</h4>'."\n";
			$html .= $this->displayArtworkForm();
			$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
			return $html;
		}

		
		// check if an artist has been selected
		else if (isset($_POST['art_select']) || isset($_POST['update'])) {
			// if admin has submtited the form to update an artist
			if ($_POST['update']) {
				// form submitted so validate
				$result = $this->model->validateArtworkForm();
				if ($result['ok']) {
					// no errors in the form so add to the database
					$update_subjects = $this->model->updateSubjects();
					$update = $this->model->editArt($_POST['art_id']);
					$update_sizes = $this->model->editSizes($_POST['art_id']);
					$new_image = $this->model->uploadArt('update', $_POST['art_id']);
					// check if added successful
					if ($update == 'success' || $update_subjects == true) {
						if($new_image) {
							$html .= "\t\t\t\t\t".'<h3 class="validation green">congratulations <br />You have successfully updated the details for artwork <a href="index.php?page=product&id='.$_POST['art_id'].'">'.htmlentities(stripslashes($_POST['art_name'])).'</a></h3>'."\n";
							$html .= $this->model->selectArtworkForm();
							$html .= "\t\t\t\t\t".'<h4>or add a new artwork below</h4>'."\n";
							unset($_POST);
							$html .= $this->displayArtworkForm();
							$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
							return $html;
						}
					} 
					else {
						// wasn't added successsful
						$html .= "\t\t\t\t\t".'<h3 class="validation red">You made no changes to the artwork <a href="index.php?page=adminartwork&artID='.$_POST['art_id'].'">'.htmlentities(stripslashes($_POST['art_name'])).'</a></h3>'."\n";
						$html .= $this->model->selectArtworkForm();
						$html .= "\t\t\t\t\t".'<h4>or add a new artwork below</h4>'."\n";
						unset($_POST);
						$html .= $this->displayArtworkForm();
 					}	
				} 
				// validation returned errors
				else {
					$html .= "\t\t".'<h3 class="validation red">please correct the following errors<br />'."\n";
				$html .= "\t\t\t\t\t".'<span class="red half">(and reselect your image)<span></h3>'."\n";
					$html .= $this->displayArtworkForm($result, $artist_info);				}
				}	

			// the changes have not yet been submitted
			else {
				$id = $this->model->checkArtworkSelected();
				if (is_numeric($id)) {
					$art_info = $this->model->getProductByID($id, true);
					$html .= "\t\t\t\t\t".'<h4>edit this artwork below</h4>'."\n";
					$html .= $this->displayArtworkForm('', $art_info);
					$html .= "\t\t\t\t\t".'<h4>or</h4>'."\n";
					$html .= $this->model->selectArtworkForm();
					$html .= "\t\t\t\t\t".'<h4>or</h4>'."\n";
					$html .= "\t\t\t\t\t".'<a href="index.php?page=adminartwork&clear=clear"><h4>add a new artwork</h4></a>'."\n";
				}
				else {
					$html .= "\t\t\t\t\t".'<h3 class="validation red">'.$id.'</h3>'."\n";
					$html .= $this->model->selectArtworkForm();
					$html .= "\t\t\t\t\t".'<h4>or add a new artwork below</h4>'."\n";
					unset($_POST);
					$html .= $this->displayArtworkForm();
				}
			}
		}

		// check if an submitted form to add a new artist
		else if ($_POST['add']) {
			// if admin has submtited the form to update an artist
			// form submitted so validate
			$result = $this->model->validateArtworkForm();
			if ($result['ok']) {
				// no errors in the form so add to the database
				$id = $this->model->editArt();
				// check if added successful
				if (is_numeric($id)) {
					$new_image = $this->model->uploadArt('add', $id);
					$add_subjects = $this->model->updateSubjects('add', $id);
					$update_sizes = $this->model->editSizes($id);
					$html .= "\t\t\t\t\t".'<h3 class="validation green">congratulations <br />You have successfully added artwork <a href="index.php?page=product&id='.$id.'">'.htmlentities(stripslashes($_POST['art_name'])).'</a></h3>'."\n";
					$html .= $this->model->selectArtworkForm();
					$html .= "\t\t\t\t\t".'<h4>or add a new artwork below</h4>'."\n";
					unset($_POST);
					$html .= $this->displayArtworkForm();
				} 
				else {
					// wasn't added successsful
					$html .= "\t\t\t\t\t".'<h3 class="validation red">sorry, artwork '.htmlentities(stripslashes($_POST['art_name'])).' was unable to be added</h3>'."\n";
					$html .= $this->displayArtworkForm();
					$html .= $this->model->selectArtworkForm();
					}	
			} 
			// validation returned errors
			else {
				$html .= "\t\t".'<h3 class="validation red">please correct the following errors<br />'."\n";
				$html .= "\t\t\t\t\t".'<span class="red half">(and reselect your image)<span></h3>'."\n";
				$html .= $this->displayArtworkForm($result, $art_info);		
				$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
				return $html;			
			}
		}	

		// if an artwork has already been selected for editing
		else if ($_GET['artID']) {
			$art_info = $this->model->getProductByID($_GET['artID'], true);
			$html .= "\t\t\t\t\t".'<h4>edit this artwork below</h4>'."\n";
			$html .= $this->displayArtworkForm('', $art_info);
			$html .= "\t\t\t\t\t".'<h4>or</h4>'."\n";
			$html .= $this->model->selectArtworkForm();
			$html .= "\t\t\t\t\t".'<h4>or</h4>'."\n";
			$html .= "\t\t\t\t\t".'<a href="index.php?page=adminartist&clear=clear"><h4>add a new artwork</h4></a>'."\n";
		}

		// no artist selected
		else if (!$_POST['add']) {
			$html .= $this->model->selectArtworkForm();
			$html .= "\t\t\t\t\t".'<h4>or add a new artwork below</h4>'."\n";
			$html .= $this->displayArtworkForm();
		}
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;		
	}


	// this function displays the artist form and shows any validation errors if submitted
	private function displayArtworkForm($error='', $art_info='') {
		if ($art_info != '' && !$error) {
			extract($art_info[0]);
			$_POST['art_id'] = $artID;
			$_POST['artistID'] = $artistID;
			$_POST['art_name'] = $artName;
			$_POST['art_keywords'] = $artKeywords;
			$_POST['art_description'] = $artDescription;
			$_POST['art_image'] = $artImage;
			$_POST['art_type'] = $artType;
			$_POST['artist_name'] = $artistName;
			$_POST['hidden'] = $artHiddenDate;
			$mode = 'update artwork';
			$name = 'update';
			$ticked_subjects = $this->model->getSubjectsByArtID($_POST['art_id']);
			$sizes = $this->model->getSizePrices($artID);
			if (is_array($sizes)) {
				$_POST['size1_id'] = $sizes[0]['sizeId'];
				$_POST['width1'] = $sizes[0]['sizeWidth'];
				$_POST['height1'] = $sizes[0]['sizeHeight'];
				$_POST['apcode1'] = $sizes[0]['APCode'];
				$_POST['rrp1'] = $sizes[0]['sizeRRP'];
				$_POST['wsp1'] = $sizes[0]['sizeWSP'];
				$_POST['royalty1'] = $sizes[0]['sizeRoyalty'];
				$_POST['size2_id'] = $sizes[1]['sizeId'];
				$_POST['width2'] = $sizes[1]['sizeWidth'];
				$_POST['height2'] = $sizes[1]['sizeHeight'];
				$_POST['apcode2'] = $sizes[1]['APCode'];
				$_POST['rrp2'] = $sizes[1]['sizeRRP'];
				$_POST['wsp2'] = $sizes[1]['sizeWSP'];
				$_POST['royalty2'] = $sizes[1]['sizeRoyalty'];
				$_POST['size3_id'] = $sizes[2]['sizeId'];
				$_POST['width3'] = $sizes[2]['sizeWidth'];
				$_POST['height3'] = $sizes[2]['sizeHeight'];
				$_POST['apcode3'] = $sizes[2]['APCode'];
				$_POST['rrp3'] = $sizes[2]['sizeRRP'];
				$_POST['wsp3'] = $sizes[2]['sizeWSP'];
				$_POST['royalty3'] = $sizes[2]['sizeRoyalty'];
			}
		}
		else if(isset($_POST['update']) && $error) {
			$mode = 'update artwork';
			$name = 'update';
			$ticked_subjects =  array($_POST['subject_1'], $_POST['subject_2'], $_POST['subject_3'], $_POST['subject_4'], $_POST['subject_5']. $_POST['subject_6'], $_POST['subject_7'], $_POST['subject_8'], $_POST['subject_9'], $_POST['subject_10'], $_POST['subject_11'], $_POST['subject_12'], $_POST['subject_13'], $_POST['subject_14'], $_POST['subject_15'], $_POST['subject_16']);
		}
		else if(isset($_POST['add']) && $error) {
			$mode = 'add artwork';
			$name = 'add';
			$ticked_subjects =  array($_POST['subject_1'], $_POST['subject_2'], $_POST['subject_3'], $_POST['subject_4'], $_POST['subject_5']. $_POST['subject_6'], $_POST['subject_7'], $_POST['subject_8'], $_POST['subject_9'], $_POST['subject_10'], $_POST['subject_11'], $_POST['subject_12'], $_POST['subject_13'], $_POST['subject_14'], $_POST['subject_15'], $_POST['subject_16']);
		}
		else {
			$mode = 'add artwork';
			$name = 'add';
		}
		$html = "\t\t\t\t\t".'<form action="'.htmlentities($_SERVER['REQUEST_URI']).'" method="post" enctype="multipart/form-data" class="form">'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="hidden" name="art_id" value="'.$_POST['art_id'].'" />'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="hidden" name="MAX_FILE_SIZE" value="15728640" />'."\n";	
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
		$html .= $error['artist_name'];
		// art type
		$html .= "\t\t\t\t\t\t".'<label for="art_type">art type</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<select name="art_type">'."\n";
		$html .= "\t\t\t\t\t\t\t".'<option value="print"';
		if ($_POST['art_type'] == 'print') {
			$html .= ' selected="selected"';
		}
		$html .= '>print</option>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<option value="canvas"';
		if ($_POST['art_type'] == 'canvas') {
			$html .= ' selected="selected"';
		}
		$html .= '>canvas</option>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<option value="framed"';
		if ($_POST['art_type'] == 'framed') {
			$html .= ' selected="selected"';
		}
		$html .= '>framed</option>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<option value="card"';
		if ($_POST['art_type'] == 'card') {
			$html .= ' selected="selected"';
		}
		$html .= '>card</option>'."\n";
		$html .= "\t\t\t\t\t\t\t".'<option value="matted_print"';
		if ($_POST['art_type'] == 'matted_print') {
			$html .= ' selected="selected"';
		}
		$html .= '>matted print</option>'."\n";
		$html .= "\t\t\t\t\t\t".'</select>'."\n";
		$html .= $error['art_type'];
		// art title
		$html .= "\t\t\t\t\t\t".'<label for="art_name">art title</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="art_name" value="'.htmlentities(stripslashes($_POST['art_name'])).'" />'."\n";
		$html .= $error['art_name'];
		// art image
		if ($name == 'update') {
		$html .= "\t\t\t\t\t\t".'<label>image</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="hidden" name="art_image" value="'.$_POST['art_image'].'" />'."\n";		
		$html .= "\t\t\t\t\t\t".'<img class="image_shadow" src="images/thumbs/'.$_POST['art_image'].'" alt="no image">'."\n";
	}
		$html .= "\t\t\t\t\t\t".'<label for="new_image">'.$name.' image</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="file" class="clear" name="new_image" />'."\n";
		$html .= $error['new_image'];
		// art keywords
		$html .= "\t\t\t\t\t\t".'<label for="art_keywords">keywords</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<textarea rows="5" cols="40" name="art_keywords">'.htmlentities(stripslashes($_POST['art_keywords'])).'</textarea>'."\n";
		$html .= $error['keywords'];
		// art descripion
		$html .= "\t\t\t\t\t\t".'<label for="art_description">description</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<textarea rows="5" cols="40" name="art_description">'.htmlentities(stripslashes($_POST['art_description'])).'</textarea>'."\n";
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
		// subjects
		$html .= "\t\t\t\t\t\t".'<label for="subjects">subjects</label>'."\n";
		$subjects = $this->model->getSubjects();

		foreach ($subjects as $subject) {
			$html .= '<input type="checkbox" name="subject_'.$subject['subjectID'].'" value="'.$subject['subjectID'].'" ';
			if (is_array($ticked_subjects)) {
				foreach ($ticked_subjects as $ticked) {
					if ($ticked['subjectID'] == $subject['subjectID']) {
						$html .= 'checked ';
					}
				}
			}
			$html .= '/><p>'.$subject['subjectName'].'</p>'."\n";
		}
		// size 1
		$html .= "\t\t\t\t\t\t".'<p class="center">size 1</p>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="hidden" name="size1_id" value="'.$_POST['size1_id'].'" />'."\n";
		$html .= "\t\t\t\t\t\t".'<label for="width1">width</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="width1" value="'.htmlentities(stripslashes($_POST['width1'])).'" />'."\n";
		$html .= $error['width1'];
		$html .= "\t\t\t\t\t\t".'<label for="height1">height</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="height1" value="'.htmlentities(stripslashes($_POST['height1'])).'" />'."\n";
		$html .= $error['height1'];
		$html .= "\t\t\t\t\t\t".'<label for="apcode1">AP Code</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="apcode1" value="'.htmlentities(stripslashes($_POST['apcode1'])).'" />'."\n";
		$html .= $error['apCode1'];
		$html .= "\t\t\t\t\t\t".'<label for="rrp1">retail price</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="rrp1" value="'.htmlentities(stripslashes($_POST['rrp1'])).'" />'."\n";
		$html .= $error['rrp1'];
		$html .= "\t\t\t\t\t\t".'<label for="wsp1">wholesale price</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="wsp1" value="'.htmlentities(stripslashes($_POST['wsp1'])).'" />'."\n";
		$html .= $error['wsp1'];
		$html .= "\t\t\t\t\t\t".'<label for="royalty1">royalty amount</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="royalty1" value="'.htmlentities(stripslashes($_POST['royalty1'])).'" />'."\n";
		$html .= $error['royalty1'];
		// size 2
		$html .= "\t\t\t\t\t\t".'<p class="center">size 2</p>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="hidden" name="size2_id" value="'.$_POST['size2_id'].'" />'."\n";
		$html .= "\t\t\t\t\t\t".'<label for="width2">width</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="width2" value="'.htmlentities(stripslashes($_POST['width2'])).'" />'."\n";
		$html .= $error['width2'];
		$html .= "\t\t\t\t\t\t".'<label for="height2">height</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="height2" value="'.htmlentities(stripslashes($_POST['height2'])).'" />'."\n";
		$html .= $error['height2'];
		$html .= "\t\t\t\t\t\t".'<label for="apcode2">AP Code</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="apcode2" value="'.htmlentities(stripslashes($_POST['apcode2'])).'" />'."\n";
		$html .= $error['apCode2'];
		$html .= "\t\t\t\t\t\t".'<label for="rrp2">retail price</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="rrp2" value="'.htmlentities(stripslashes($_POST['rrp2'])).'" />'."\n";
		$html .= $error['rrp2'];
		$html .= "\t\t\t\t\t\t".'<label for="wsp2">wholesale price</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="wsp2" value="'.htmlentities(stripslashes($_POST['wsp2'])).'" />'."\n";
		$html .= $error['wsp2'];
		$html .= "\t\t\t\t\t\t".'<label for="royalty2">royalty amount</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="royalty2" value="'.htmlentities(stripslashes($_POST['royalty2'])).'" />'."\n";
		$html .= $error['royalty2'];
		// size 3
		$html .= "\t\t\t\t\t\t".'<p class="center">size 3</p>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="hidden" name="size3_id" value="'.$_POST['size3_id'].'" />'."\n";
		$html .= "\t\t\t\t\t\t".'<label for="width3">width</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="width3" value="'.htmlentities(stripslashes($_POST['width3'])).'" />'."\n";
		$html .= $error['width3'];
		$html .= "\t\t\t\t\t\t".'<label for="height3">height</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="height3" value="'.htmlentities(stripslashes($_POST['height3'])).'" />'."\n";
		$html .= $error['height3'];
		$html .= "\t\t\t\t\t\t".'<label for="apcode3">AP Code</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="apcode3" value="'.htmlentities(stripslashes($_POST['apcode3'])).'" />'."\n";
		$html .= $error['apCode3'];
		$html .= "\t\t\t\t\t\t".'<label for="rrp3">retail price</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="rrp3" value="'.htmlentities(stripslashes($_POST['rrp3'])).'" />'."\n";
		$html .= $error['rrp3'];
		$html .= "\t\t\t\t\t\t".'<label for="wsp3">wholesale price</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="wsp3" value="'.htmlentities(stripslashes($_POST['wsp3'])).'" />'."\n";
		$html .= $error['wsp3'];
		$html .= "\t\t\t\t\t\t".'<label for="royalty3">royalty amount</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" name="royalty3" value="'.htmlentities(stripslashes($_POST['royalty3'])).'" />'."\n";
		$html .= $error['royalty3'];
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