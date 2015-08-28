<?php

// This class contains the method to upload an image file and any other type of file

class Upload {
	private $fileName;			//	name of the file from the upload form
	private $fileTypes=array();	//	array of valid file types for upload
	private $folderPath;		//	folder where uploaded files will be moved
	public $msg;				//	messages generated from this class
	
	public function __construct($fileName, $fileTypes, $folderPath) {
		$this->fileName = $fileName;
		$this->fileTypes = $fileTypes;
		$this->folderPath = $folderPath;
	}

		// contains statements to upload file and returns the name of the file if successful otherwise false
	public function isUploaded() {
		$this->msg = '';
			//	check if the name of the file uploaded is not available
		if (!$_FILES[$this->fileName]['name'])	{
			$this->msg .= 'File name not available';
			return false;
		}

		
			// tests for an error in uploading
		if ($_FILES[$this->fileName]['error']) {	
			switch($_FILES[$this->fileName]['error']) {
				case 1: $this->msg .= 'Your image is too big!';  //  exceeds PHP\'s maximun upload size
					return false;
				case 2: $this->msg .= 'Your image is too large!';  // File exceeds maximum upload file size set in the form
					return false;
				case 3: $this->msg .= 'That only partially worked!';  // File partially uploaded
					return false;
				case 4: $this->msg .= 'Your file wasn\'t uploaded!';  // No file upload
					return false;
			}
		}
		
			// check if file type is invalid
		$type = $_FILES[$this->fileName]['type'];	//	get the type of the uploaded file
		if (!in_array($type, $this->fileTypes)) {
			$this->msg .= 'Wrong file type';
			return false;
		}
		
			// check if file did not reach the server
		if (!is_uploaded_file($_FILES[$this->fileName]['tmp_name'])) {
			$this->msg .= 'File did not reach the temporary location on the server';
			return false;
		}
		$fleName = $_FILES[$this->fileName]['name'];
		$flePath = $this->folderPath ? $this->folderPath.'/'.$fleName : $fleName;
		
			// test if a file with the same name exists and rename it using a random generated (uniqid) if it does
		if (file_exists($flePath)) {
			$newName = uniqid('art').$fleName;
			$flePath = $this->folderPath ? $this->folderPath.'/'.$newName : $newName;
		}

			//move file from temporary location to the path specified and test if it's not successful
		if (!move_uploaded_file($_FILES[$this->fileName]['tmp_name'], $flePath)) {	
			$this->msg .= 'Error in moving file to the specified location';
			return false;
		}
			// check if new file does not exists in the destination folder
		if (!file_exists($flePath)) {
			$this->msg .= 'File did not reach the destination folder';
			return false;
		}
				// all worked fine and returns the filepath
			$this->msg .= 'File '.$_FILES[$this->fileName]['name'].' uploaded successfully ';
			return $flePath;
		}
	}

?>