<?php

class ResizeImage {

	// this function takes the image name, dimension, destination folder and resizes an image accordingly

	private $imageName;  	// name of the file returned for the upload class
	private $dimension;  	// desired dimension of the longer side
	private $destFolder; 	// path of the folder where resized image will be saved
	private $width;			// width of the banner
	private $height;		// height of the banner
	public  $msg;			// message that will be set in the this class
	
	public function __construct($imageName, $dimension, $destFolder, $width='', $height='') {
		$this->imageName = $imageName;
		$this->dimension = $dimension;
		$this->destFolder = $destFolder;
		$this->height = $height;
		$this->width = $width;
	}
	
	// calculates longest size and makes that the max width/height and saves
	public function resize() {
		$this->msg = '';
			// check if the file is not available
		if (!file_exists($this->imageName)) {
			$this->msg .= 'Sorry, source file not found';
			return false;
		}

		// calculate image aspect ratio and for the new width/height
		// imageInfo is an array of info about the image
		$imageInfo = getimagesize($this->imageName);
		$oWidth = $imageInfo[0];  	// is the original height
		$oHeight = $imageInfo[1];  	// is the original width
		
		// if resizing a product image
		if ($this->dimension != '') {
			// which side is longer?  Set that to max height/width and other proportionate
			if ($oHeight > $oWidth) {
				$newH = $this->dimension;
				$newW = ($oWidth * $newH) / $oHeight;
			}
			else {
				$newW = $this->dimension;
				$newH = ($oHeight * $newW) / $oWidth;
			}
		}
		// else resizing a banner
		else {
			$newW = $this->width;
			$newH = $this->height;
		}

		// use the destination folder to create the fullpath of the thumb
		$imgName = basename($this->imageName);
		$dFolder = $this->destFolder ? $this->destFolder.'/' : '';
		$dFile = $this->prefix ? $this->prefix.'_'.$imgName : $imgName;
		$fullPath = $dFolder.''.$dFile;		// have an empty string here as if you don't sometimes you get errors
		
		// return resized fullfilePath to show resize was successful
		if ($this->resizeJpeg($newW, $newH, $oWidth, $oHeight, $fullPath)) {
				return $fullPath;
			}
			else {
				return false;
			}
	}
	
	// this function resizes jpegs to the new size
	private function resizeJpeg($newW, $newH, $oWidth, $oHeight, $fullPath) {
			
		// create a new canvas
		$im = ImageCreateTruecolor($newW, $newH);  
		$baseImage = ImageCreateFromJpeg($this->imageName);
		
		// if successful this returns as true - this is the actual resizing
		if (imagecopyresampled($im, $baseImage, 0, 0, 0, 0, $newW, $newH, $oWidth, $oHeight)) { 
			// resizing is successful and saved to $fullPath
			imageJpeg($im, $fullPath);  
			if (file_exists($fullPath)) {
				$this->msg .= 'Resized file created<br />';
				imagedestroy($im);  // don't really need this cos $im will disappear after function done
				return true;
			}
			else {
				$this->msg .= 'Failure in resize jpeg<br />';
			}	
		}
		else {
			// resizing fails
			$this->msg .= 'Unable to resize image<br />';
			return false;
		}
	} 
	
}

?>