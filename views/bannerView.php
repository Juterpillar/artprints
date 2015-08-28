<?php

class BannerView extends View
{
	// this view shows order and profile info for the logged in customer
	
	protected function displayContent()  {

		// redirect user if not logged in as admin
		if (!$_SESSION['access'] == 'admin' || !isset($_GET['a'])) {
			header("Location: index.php");
		}
		$html .= $this->model->displaySideBarOptions();
		$html .= "\t\t\t\t".'<div id="content" class="width">'."\n";
		
		// if submitted a new banner
		if (isset($_POST['new_banner'])) {
			$result = $this->model->checkPassword($_POST['password']);
			if ($result['ok']) {
				$iresult = $this->model->addBanner();
				if (is_numeric($iresult['bannerID'])) {
					$bannerID = $iresult['bannerID'];
					$banner = $this->model->getBanners($bannerID);
					extract($banner[0]);
					$html .= "\t\t\t\t\t".'<h2 class="validation green">homepage banner added</h2>'."\n";
					$html .= "\t\t\t\t\t".'<p id="edit_banner"><a href="index.php?page=banner&amp;a=e">click here to edit homepage banners</a></p>'."\n";
					$html .= "\t\t\t\t\t".'<a href="'.$bannerLink.'"><img src="images/banners/'.$this->device['type'].'/'.$bannerName.'" alt="'.$bannerAlt.'" width="100%" />'."\n";
				}
				else {
					$html .= "\t\t\t\t\t".'<h2 class="validation red">homepage banner not added</h2>'."\n";
					$html .= "\t\t\t\t\t".'<p id="edit_banner">make sure your image is less than 2MB</p>'."\n";
					$html .= $this->addBannerForm($result);
					$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
					return $html;
				}
			}
			else {
				$html .= "\t\t\t\t\t".'<h2 class="validation red">homepage banner not added</h2>'."\n";
				$html .= $this->addBannerForm($result);
				$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
				return $html;
			}
		}
		// editing a banner visibility
		else if (isset($_POST['edit_banner'])) {
			$edit = $this->model->editBanner();
			if ($edit == true) {
				$html .= "\t\t\t\t\t".'<h2 class="validation green">banner updated</h2>'."\n";
				$html .= $this->editBannersForm();
				$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
				return $html;
			}
			// else error updating
			else {
				$html .= "\t\t\t\t\t".'<h2 class="validation red">no changes made to banner!</h2>'."\n";
				$html .= $this->editBannersForm();
				$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
				return $html;
			}	
		}
		if ($_GET['a'] == 'a') {
			$html .= "\t\t\t\t\t".'<h2 class="title">add a homepage banner</h2>'."\n";
			$html .= "\t\t\t\t\t".'<h4 class="validation">note: your banner will automatically be resized into 3 sizes:<br /> 960 x 290 pixels for desktops<br />735 x 222 pixels for tablets<br />320 x 160 pixels for mobiles</h4>'."\n";
			$html .= $this->addBannerForm();
		}
		else {
			$html .= "\t\t\t\t\t".'<h2 class="title">edit homepage banners</h2>'."\n";
			$html .= $this->editBannersForm();
		}
		$html .= "\t\t\t\t".'</div><!-- content -->'."\n";
		return $html;
	}

	// this function displays the edit Banners form for each banner
	private function editBannersForm() {
		if ($_SESSION['access'] == 'admin') {
			$banners = $this->model->getBanners();
			if (is_array($banners)) {
				foreach ($banners as $banner) {
					extract($banner);
					$html .= "\t\t\t\t\t".'<form action="'.$_SERVER['REQUEST_URI'].'" method="post" class="form banners">'."\n";
					$html .= "\t\t\t\t\t\t".'<input type="hidden" name="banner_id" value="'.$bannerID.'" />'."\n";
					$html .= "\t\t\t\t\t\t".'<img src="images/banners/'.$this->device['type'].'/'.$bannerName.'" alt="'.$bannerAlt.'" width="100%" />'."\n";
					$html .= "\t\t\t\t\t\t".'<label for="alt">description</label>'."\n";
					$html .= "\t\t\t\t\t\t".'<input type="text" id="alt" name="alt" value="'.$bannerAlt.'" />'."\n";
					$html .= "\t\t\t\t\t\t".'<label for="visibility">visibility / hide</label>'."\n";
					$html .= "\t\t\t\t\t\t".'<select name="visibility">'."\n";
					$visibility = array('visible', 'hidden');
					foreach ($visibility as $vis) {
						$html .= "\t\t\t\t\t\t\t".'<option value="'.$vis.'"';
						if ($bannerVisibility == $vis) {
							$html .= ' selected="selected"';
						}
						$html .= '>'.$vis.'</option>'."\n";
					}
					$html .= "\t\t\t\t\t\t".'</select>'."\n";
					$html .= "\t\t\t\t\t\t".'<label  id="link_label" for="link">link to</label>'."\n";
					$html .= "\t\t\t\t\t\t".'<input type="text" id="link" name="link" value="'.$bannerLink.'" />'."\n";
					$html .= "\t\t\t\t\t\t".'<input type="submit" name="edit_banner" value="edit banner" />'."\n";
					$html .= "\t\t\t\t\t".'</form>'."\n";
						}
					}
			return $html;
		}
	}

	// this function displays the artist form and shows any validation errors if submitted
	private function addBannerForm($error="") {
		$html = "\t\t\t\t\t".'<form action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data" class="form new_banner">'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="hidden" name="MAX_FILE_SIZE" value="15728640" />'."\n";
		// banner	
		$html .= "\t\t\t\t\t\t".'<label for="banner_img">new banner</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="file" class="clear" name="banner_img" />'."\n";
		// alt text
		$html .= "\t\t\t\t\t\t".'<label for="alt">description</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" id="alt" name="alt" value="'.htmlentities(stripslashes($_POST['alt'])).'" />'."\n";
		// link
		$html .= "\t\t\t\t\t\t".'<label for="link">link to</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="text" id="link" name="link" value="'.htmlentities(stripslashes($_POST['link'])).'" />'."\n";
		// password
		$html .= "\t\t\t\t\t\t".'<label for="password">password</label>'."\n";
		$html .= "\t\t\t\t\t\t".'<input type="password" name="password" value="'.htmlentities(stripslashes($_POST['password'])).'" />'."\n";
		$html .= $error['password'];
		$html .= "\t\t\t\t\t\t".'<input type="submit" name="new_banner" value="add banner" />'."\n";
		$html .= "\t\t\t\t\t".'</form>'."\n";
		return $html;
	}

}
?>