<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
  
  require("common.php");
	
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
			user_id, 
			name, 
            description,
			type,
			urgent,
			img,
			rate
        FROM problems
		WHERE 
			user_id = :user_id
		
    "; 
    $query_params = array( 
        ':user_id' => $_SESSION['user']['id']
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
    $row = $stmt->fetchAll(); 
	if(isset($_POST['action']) && !empty($_POST['action']))
	{
        echo json_encode($row, JSON_FORCE_OBJECT);
    }	
	
	