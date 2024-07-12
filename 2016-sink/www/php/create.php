<?php 

    // First we execute our common code to connection to the database and start the session 
    require("common.php");
    // This if statement checks to determine whether the registration form has been submitted 
    // If it has, then the registration code is run, otherwise the form is displayed 
    
	if(!empty($_POST)) 
    { 
         
        // Make sure the user entered a valid E-Mail address 
        // filter_var is a useful PHP function for validating form input, see: 
        // http://us.php.net/manual/en/function.filter-var.php 
        // http://us.php.net/manual/en/filter.filters.php 
		
        // We will use this SQL query to see whether the username entered by the 
        // user is already in use.  A SELECT query is used to retrieve data from the database. 
        // :username is a special token, we will substitute a real value in its place when 
        // we execute the query. 
         
        // This contains the definitions for any special tokens that we place in 
        // our SQL query.  In this case, we are defining a value for the token 
        // :username.  It is possible to insert $_POST['username'] directly into 
        // your $query string; however doing so is very insecure and opens your 
        // code up to SQL injection exploits.  Using tokens prevents this. 
        // For more information on SQL injections, see Wikipedia: 
        // http://en.wikipedia.org/wiki/SQL_Injection 
       
         
        // An INSERT query is used to add new rows to a database table.
        // Again, we are using special tokens (technically called parameters) to 
        // protect against SQL injection attacks. 
	   $query = " 
            INSERT INTO problems ( 
                username, 
                name, 
                description, 
                type,
				img,
				urgent
            ) VALUES ( 
                :username,
                :name,
				:description,
				:type,
				:img,
				:urgent
            ) 
        "; 
         
        // Here we prepare our tokens for insertion into the SQL query.  We do not 
        // store the original password; only the hashed version of it.  We do store 
        // the salt (in its plaintext form; this is not a security risk). 
        $user = $_SESSION['user']['username'];
		$query_params = array( 
            ':username' => $user,
            ':name' => $_POST['name'],
			':description' => $_POST['description'],
			':type' => $_POST['type'],
			':img' => $_POST['img'],
			':urgent' => $_POST['urgent']
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
        header("Location: ../index.php#!/sinkList"); 
         
        // Calling die or exit after performing a redirect using the header function 
        // is critical.  The rest of your PHP script will continue to execute and 
        // will be sent to the user if you do not die or exit. 
        die("Redirecting to home page"); 
    } 
     
?> 
<h1>Register</h1> 
<form action="create.php" method="post"> 
    name:<br /> 
    <input type="text" name="name" value="" /> 
    <br /><br /> 
    description:<br /> 
    <input type="text" name="description" value="" /> 
    <br /><br /> 
    type:<br /> 
    <input type="password" name="type" value="" /> 
    <br /><br /> 
	 img:<br /> 
    <input type="password" name="img" value="" /> 
    <br /><br /> 
    <input type="submit" value="Register" /> 
</form>