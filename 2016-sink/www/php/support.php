 <?php 
    require("common.php"); 
	if(!empty($_POST)) 
    { 
		$query = " 
            INSERT INTO support ( 
				email,
				name,
				description
            ) VALUES ( 
				:email,
				:name,
				:description
            ) 
        "; 
		$query_params = array( 
			':email' => $_POST['email'],
			':name' => $_POST['name'],
			':description' => $_POST['description']
        ); 
	
	    try 
			{ 
				// Execute the query to create the user 
				$stmt = $db->prepare($query); 
				$result = $stmt->execute($query_params); 
			} 
        catch(PDOException $ex) 
			{ 
				// Note: On a production website, you should not output $ex->getMessage(). 
				// It may provide an attacker with helpful information about your code.  
				die("Failed to run query: " . $ex->getMessage()); 
			} 
         
		// This redirects the user back to the login page after they register 
        header("Location: ../index.php"); 
         
        // Calling die or exit after performing a redirect using the header function 
        // is critical.  The rest of your PHP script will continue to execute and 
        // will be sent to the user if you do not die or exit. 
        die("Redirecting to home page");
	
	
	}
		
		
?>
         