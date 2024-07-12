<?php
   // First we execute our common code to connection to the database and start the session 
   require("../php/common.php"); 

    // At the top of the page we check to see whether the user is logged in or not 
 if(!empty($_SESSION['user'])) 
 {
	 if ($_SESSION['user']['isPro'] == 1)
    {      
      // Initial query parameter values 
        $query_params = array( 
            ':age' => $_POST['age'],
			':area' => $_POST['area'],
			':about' => $_POST['about'],
			':facebook' => $_POST['facebook'],
			':picture' => $_POST['picture'],
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
                age = :age,
				area = :area,
				about = :about,
				facebook = :facebook,
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
         
        // Now that the user's E-Mail address has changed, the data stored in the $_SESSION 
        // array is stale; we need to update it so that it is accurate.
        $_SESSION['user']['age'] = $_POST['age'];
		$_SESSION['user']['area'] = $_POST['area'];
		$_SESSION['user']['facebook'] = $_POST['facebook']; 
		$_SESSION['user']['picture'] = $_POST['picture'];
		$_SESSION['user']['about'] = $_POST['about'];
         
        // This redirects the user to the next page
        header("location: ../register-professional-skillset.html"); 
        die();  
        // Calling die or exit after performing a redirect using the header function 
        // is critical.  The rest of your PHP script will continue to execute and 
        // will be sent to the user if you do not die or exit. 
   
	}
     else {
		
			echo("<script>alert('cannot enter professional details because you are not a professional user');window.location = '../index.php';</script>");
			die();
		  }
		
 }
		else {
			echo("<script>alert('You must be logged in.');window.location = '../index.php';</script>");
					die();
		}	
?> 
