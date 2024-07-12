<?php
   // First we execute our common code to connection to the database and start the session 
   require("../php/common.php"); 

    // At the top of the page we check to see whether the user is logged in or not 
	 $count = $_POST['count'];
	if(empty($_SESSION['user']))
	{
    header('location: index.php');
	die();
	} 
 	elseif($_POST['count'] == 0)
	{      
		echo("<script>alert('you must select at least one skill');window.location.replace('../register-professional-skillset.html');</script>");
		die();
	} else
	{
		$z = 0;
		$count = $_POST['count'];
		for ($x = 0; $x < $count; $x++) 
		{		
			$z += 1;
			$val = ($_POST[$z]);
			echo($val);
			// Initial query parameter values
			$query_params = array(
				':cat_id' => $val,
				':pro_id' => $_SESSION['user']['id']
			);

			// If the user is changing their password, then we need parameter values
			// for the new password hash and salt too.
			// Note how this is only first half of the necessary update query.  We will dynamically
			// construct the rest of it depending on whether or not the user is changing
			// their password.
			$query = " 
			INSERT INTO cat_pro_id( 
				cat_id,
				pro_id
            ) VALUES ( 
				:cat_id,
				:pro_id
            ) 
	
				";

			// Finally we finish the update query by specifying that we only wish
			// to update the one record with for the current user.

			try {
				// Execute the query
				$stmt = $db->prepare($query);
				$result = $stmt->execute($query_params);
			} catch (PDOException $ex) {
				// Note: On a production website, you should not output $ex->getMessage().
				// It may provide an attacker with helpful information about your code.
				die("Failed to run query: " . $ex->getMessage());
			}
			
			// Now that the user's E-Mail address has changed, the data stored in the $_SESSION
			// array is stale; we need to update it so that it is accurate.
		header('location: ../register-professional-picture.html');
		}
	}
        // Calling die or exit after performing a redirect using the header function 
        // is critical.  The rest of your PHP script will continue to execute and 
        // will be sent to the user if you do not die or exit. 
 
