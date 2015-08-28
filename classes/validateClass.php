<?php

class Validate
{
	// This class contains common methods for validating form entries
	

	// checking names entered and more than 2 characters long
	public function checkName($name) { 
		if(!$name || strlen($name) < 2){
			$msg = "\t\t\t\t\t\t".'<p class="error">Please enter a name</p>';
		}
		return $msg;
	}
		

	// checking for a valid email
	public function checkEmail($email) { 
        if (!$email) {
			$msg = "\t\t\t\t\t\t".'<p class="error">Please enter an email address</p>';
		}
		else if(!preg_match('/^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_\-\.]+\.[a-zA-Z0-9_\-]+$/',$email)){
			$msg = "\t\t\t\t\t\t".'<p class="error">Please correct your email address</p>';
        }
		return $msg;
	}


	// checking for a valid phone number
	public function checkPhone($phone) { 
        if (!$phone) {
			$msg = "\t\t\t\t\t\t".'<p class="error">Please enter a phone number with area code</p>';
		}
		else if(!preg_match('/^(((\+?64\s*[-\.]?[3-9]|\(?0[3-9]\)?)\s*[-\.]?\d{3}\s*[-\.]?\d{4})|((\+?64\s*[-\.\(]?2\d{1}[-\.\)]?|\(?02\d{1}\)?)\s*[-\.]?\d{3}\s*[-\.]?\d{3,5})|((\+?64\s*[-\.]?[-\.\(]?800[-\.\)]?|[-\.\(]?0800[-\.\)]?)\s*[-\.]?\d{3}\s*[-\.]?(\d{2}|\d{5})))$/',$phone)){
			$msg = "\t\t\t\t\t\t".'<p class="error">The phone number you have entered is not a NZ number</p>';
        }
		return $msg;
	}


	// check something was entered into field (generic check)
    public function checkCity($field) {
		if (!$field) {
			$msg = "\t\t\t\t\t\t".'<p class="error">Please enter your city</p>';
		}
		return $msg;
	}


	// check something was entered into field (generic check)
    public function checkRequired($field) {
		if (!$field) {
			$msg = "\t\t\t\t\t\t".'<p class="error">You must fill out this field</p>';
		}
		return $msg;
	}
   	
	
	// check that the password entered is at least 6 characters long
	public function checkPassword($password) { 
		if(strlen($password) < 6 || $password == '') {
			$msg = "\t\t\t\t\t\t".'<p class="error">Your password must be at least 6 characters</p>';	
		}
		return $msg;
	}
	
	
	// check that there is 2 passwords entered for registration and they match
	public function checkPasswordMatch($password1, $password2) { 
		if(!$password1 || !$password2) {
			$msg = "\t\t\t\t\t\t".'<p class="error">Sorry, you\'re missing a password</p>';
		}
		else if ($password1 != $password2) {
			$msg = "\t\t\t\t\t\t".'<p class="error">Sorry, your passwords do not match</p>';
		}
		return $msg;
	}


	// check an option has been selected
	public function checkSelect($option) {
		if (!isset($option) || $option == '0') {
			$msg = "\t\t\t\t\t\t".'<p class="error">Select an option</p>';
		}
		return $msg;
	}

	// checks an entry is numeric
	public function checkNumeric($number, $compulsary='') {
		if (!$number && $compulsary) {
			$msg = "\t\t\t\t\t\t".'<p class="error">This field must be a number</p>';
		}
		else if (!is_numeric($number) && $number) {
			$msg = "\t\t\t\t\t\t".'<p class="error">This field must be a number</p>';
		}
		return $msg;
	}

	// checks an entry is greater than 0
	public function checkAmount($number) {
		if (!$number) {
			$msg = "\t\t\t\t\t\t".'<p class="error">You must enter an amount</p>';
		}
		else if (!is_numeric($number)) {
			$msg = "\t\t\t\t\t\t".'<p class="error">This field must be a number</p>';
		}
		return $msg;
	}
	
	// check a file has been selected for uploading
	public function checkFile() 
	{
		if (!$_FILES['new_image']['name']) { 
			$msg = "\t\t\t\t\t\t".'<p class="error">Choose an image to upload</p>';
		}
		return $msg;
	}


	// check a message was entered
	public function checkMessage($message) {
		if (!$message || strlen($message) < 6) {
			$msg = "\t\t\t\t\t\t".'<p class="error">Please enter your message</p>';
		}
		else if ($message == 'Hi artprints...') {
			$msg = "\t\t\t\t\t\t".'<p class="error">Please enter your message</p>';
		}
		return $msg;
	}

	// this function checks a checkbox is ticked
	public function checkChecked($ticked) {
		if(!isset($ticked)) {
			$msg = "\t\t\t\t\t\t".'<p class="error">Please tick this box</p>';
		}
		return $msg;
	}


	// checks if there is at least one error message in the array of error messages
	public function checkErrorMessages($result) {
		foreach($result as $errorMsg) {
			if (strlen($errorMsg) > 0) {
				return false;
			}		
		}
	return true;
	}	
}
?>