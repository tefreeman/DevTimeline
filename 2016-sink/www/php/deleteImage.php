<?php
// include ImageManipulator class
require_once('ImageManipulator.php');
require('common.php');
error_reporting( error_reporting() & ~E_NOTICE );
$randomId = $_SESSION['user']['username'];
$maxFileSize = 300;
$fullImageName = '';
$fullImageNameMysql = '';
 if(!empty($_SESSION['user'])) 
 {
	// if image is submited
	if(isset($_POST["submit"]))
	{
		// generate random 4 letter string folder names
		function generateRandomString($length = 4) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
			for ($i = 0; $i < $length; $i++) 
			{
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
		} 
		// creates a folder system and checks if it exists and if it doesnt it makes the folder tree
		$fName1 = '../uploads/profilePictures/' . generateRandomString() . '/';
		$fName2 = $fName1 . generateRandomString() . '/';
		$fName3 = $fName2 . generateRandomString() . '/';
		$fName4 = $fName3 . generateRandomString() . '/';
		$fName5 = $fName4 . generateRandomString() . '/';
		
		if(is_dir($fName1)){ }
		else
		{
		mkdir("$fName1");
		}
		if(is_dir($fName2)){ }
		else
		{
		mkdir("$fName2");
		}
		if(is_dir($fName3)){ }
		else
		{
		mkdir("$fName3");
		}
		if(is_dir($fName4)){ }
		else
		{
		mkdir("$fName4");
		}
		if(is_dir($fName5)){ }
		else
		{
		mkdir("$fName5");
		}
		
	}
	if ($_FILES['fileToUpload']['size'] > $maxFileSize*1024) 
		{
		echo '<script>alert("File size must be below 3mb");window.location = "../register-professional-picture.html"</script>';
		die();
		} else
		{
	if ($_FILES['fileToUpload']['error'] > 0) {
		echo "Error: " . $_FILES['fileToUpload']['error'] . "<br />";
		} else {
		// array of valid extensions
		$validExtensions = array('.jpg', '.jpeg', '.gif', '.png');
		// get extension of the uploaded file
		$fileExtension = strrchr($_FILES['fileToUpload']['name'], ".");
		// check if file Extension is on the list of allowed ones
		if (in_array($fileExtension, $validExtensions)) 
	{
			//create a random image name based on user id grabbed from random ID variable
			$newNamePrefix = md5(uniqid($randomId,true));
			$manipulator = new ImageManipulator($_FILES['fileToUpload']['tmp_name']);
			// resizing to 200x200
			$newImage = $manipulator->resample(600, 600, true);
			// saving file to uploads folder
			$manipulator->save($fName5 . $newNamePrefix . '.jpg');
			$fullImageName = "$fName5" . "$newNamePrefix" . ".jpg";
			// subtract the ../ for putting into the mysql database
			$fullImageNameMysql = substr($fullImageName, 2);

		  // Initial query parameter values 
			$query_params = array( 
				':picture' => $fullImageNameMysql,
				':user_id' => $_SESSION['user']['id']
			); 
			 
			// If the user is changing their password, then we need parameter values 
			// for the new password hash and salt too. 
			// Note how this is only first half of the necessary update query.  We will dynamically 
			// construct the rest of it depending on whether or not the user is changing 
			// their password. 
			$query = " 
				UPDATE professionals
				SET 
					picture = :picture
			"; 
			 
			// Finally we finish the update query by specifying that we only wish 
			// to update the one record with for the current user. 
			$query .= " 
				WHERE 
					id = :user_id
			"; 
			 
			try 
			{ 
				// Execute the query 
				$stmt = $db->prepare($query); 
				$result = $stmt->execute($query_params); 
			} 
			catch(PDOException $ex) 
			{ 
				// Note: On a production website, you should not output $ex->getMessage(). 
				// It may provide an attacker with helpful information about your code.  
				die("Failed to run query: " . $ex->getMessage()); 
			} 
			 
			$_SESSION['user']['picture'] = $fullImageNameMysql;
			header('location: ../register-professional-picture-check.php');
			die();
			 
			// Calling die or exit after performing a redirect using the header function 
			// is critical.  The rest of your PHP script will continue to execute and 
			// will be sent to the user if you do not die or exit. 
			
		 
	} 	else {
			echo ("<script>alert('You did not upload a proper image. Please try again');window.location = '../register-professional-picture.html';</script>");
			die();
			
			
		}
	}
		}
 } else 
 {
header('../index.php');
die();	 
 }
?>
 
