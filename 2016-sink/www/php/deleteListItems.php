<?php 

    // First we execute our common code to connection to the database and start the session 
    require("../php/common.php"); 
     
    // At the top of the page we check to see whether the user is logged in or not 
	$grabId = 6;
	if(isset($_POST['getId']))	
	{
		$grabId = $_POST['getId']; 
		$query = "
		DELETE
		FROM problems
		WHERE 
		id = :id
		
		";
		$query_params[':id'] = $grabId; 
		try 
			{ 
				// These two statements run the query against your database table. 
				$stmt = $db->prepare($query); 
				$stmt->execute($query_params);
			} 
		catch(PDOException $ex) 
			{ 
				// Note: On a production website, you should not output $ex->getMessage(). 
				// It may provide an attacker with helpful information about your code.  
				die("Failed to run query: " . $ex->getMessage()); 
			} 
	
	}
   if(empty($_SESSION['user'])) 
    { 
        // If they are not, we redirect them to the login page. 
        header("Location: login.php"); 
         
        // Remember that this die statement is absolutely critical.  Without it, 
        // people can view your members-only content without logging in. 
        die("Redirecting to login.php"); 
    } 
     
    // Everything below this point in the file is secured by the login system 
     
    // We can retrieve a list of members from the database using a SELECT query. 
    // In this case we do not have a WHERE clause because we want to select all 
    // of the rows from the database table. 
    $query = " 
        SELECT 
            id,
			name, 
            description,
			type,
			urgent,
			img
        FROM problems
		WHERE 
			username = :username
		
    "; 
    $query_params = array( 
        ':username' => $_SESSION['user']['username']

    ); 
    try 
    { 
        // These two statements run the query against your database table. 
        $stmt = $db->prepare($query); 
        $stmt->execute($query_params); 
    } 
    catch(PDOException $ex) 
    { 
        // Note: On a production website, you should not output $ex->getMessage(). 
        // It may provide an attacker with helpful information about your code.  
        die("Failed to run query: " . $ex->getMessage()); 
    } 
         
    // Finally, we can retrieve all of the found rows into an array using fetchAll 
    $rows = $stmt->fetchAll(); 
?> 