<?php
   // First we execute our common code to connection to the database and start the session 
   require("../php/common.php"); 
     
    // At the top of the page we check to see whether the user is logged in or not 
    if(empty($_SESSION['user'])) 
    { 
        // If they are not, we redirect them to the login page. 
        header("Location: ../index.php"); 
         
        // Remember that this die statement is absolutely critical.  Without it, 
        // people can view your members-only content without logging in. 
        die("Redirecting to login.php"); 
    }	 
    // This if statement checks to determine whether the edit form has been submitted 
    // If it has, then the account updating code is run, otherwise the form is displayed 
    if(!empty($_POST)) 
    { 
        // Make sure the user entered a valid E-Mail address 
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
            die("Invalid E-Mail Address"); 
        } 
         
        // If the user is changing their E-Mail address, we need to make sure that 
        // the new value does not conflict with a value that is already in the system. 
        // If the user is not changing their E-Mail address this check is not needed. 
        if($_POST['email'] != $_SESSION['user']['email']) 
        { 
            // Define our SQL query 
            $query = " 
                SELECT 
                    1 
                FROM users 
                WHERE 
                    email = :email 
            "; 
             
            // Define our query parameter values 
            $query_params = array( 
                ':email' => $_POST['email'] 
            ); 
             
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
             
            // Retrieve results (if any) 
            $row = $stmt->fetch(); 
            if($row) 
            { 
                die("This E-Mail address is already in use"); 
            } 
        } 
         
        // If the user entered a new password, we need to hash it and generate a fresh salt 
        // for good measure. 
        if(!empty($_POST['password'])) 
        { 
            $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
            $password = hash('sha256', $_POST['password'] . $salt); 
            for($round = 0; $round < 65536; $round++) 
            { 
                $password = hash('sha256', $password . $salt); 
            } 
        } 
        else 
        { 
            // If the user did not enter a new password we will not update their old one. 
            $password = null; 
            $salt = null; 
        } 
         
        // Initial query parameter values 
        $query_params = array( 
            ':email' => $_POST['email'],
			':fName' => $_POST['fName'],
			':lName' => $_POST['lName'],
			':streetAddress' => $_POST['streetAddress'],
			':city' => $_POST['city'],
			':zipCode' => $_POST['zipCode'],
			':phoneNumber' => $_POST['phoneNumber'],
            ':user_id' => $_SESSION['user']['id']
        ); 
         
        // If the user is changing their password, then we need parameter values 
        // for the new password hash and salt too. 
        if($password !== null) 
        { 
            $query_params[':password'] = $password; 
            $query_params[':salt'] = $salt; 
        } 
         
        // Note how this is only first half of the necessary update query.  We will dynamically 
        // construct the rest of it depending on whether or not the user is changing 
        // their password. 
        $query = " 
            UPDATE users 
            SET 
                email = :email,
				fName = :fName,
				lName = :lName,
				streetAddress = :streetAddress,
				city = :city,
				zipCode = :zipCode,
				phoneNumber = :phoneNumber
        "; 
         
        // If the user is changing their password, then we extend the SQL query 
        // to include the password and salt columns and parameter tokens too. 
        if($password !== null) 
        { 
            $query .= " 
                , password = :password 
                , salt = :salt
            "; 
        } 
         
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
        $_SESSION['user']['email'] = $_POST['email']; 
         
        // This redirects the user back to the members-only page after they register 
        header("Location: private.php"); 
         
        // Calling die or exit after performing a redirect using the header function 
        // is critical.  The rest of your PHP script will continue to execute and 
        // will be sent to the user if you do not die or exit. 
        die("Redirecting to private.php"); 
    } 
     
?> 

<html >
  <head>
    <meta charset="UTF-8">
    <meta name="google" value="notranslate">


    <title>Sink</title>
    <script src="http://s.codepen.io/assets/libs/modernizr.js" type="text/javascript"></script>

<link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>

<link href="http://dev.see8ch.com/see8ch/v3/fonts/ss-social/ss-social.css" rel="stylesheet" />

<link href="http://dev.see8ch.com/see8ch/v3/fonts/ss-standard/ss-standard.css" rel="stylesheet" />

<link href="http://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">



<link href="/css/login.css" rel="stylesheet" />
    
    

    <script>
  window.console = window.console || function(t) {};
</script>

        <script src="//assets.codepen.io/assets/libs/prefixfree.min-d258f6cb24f3a877e4fb83b348ec8a3f.js"></script>

    
  </head>

  <body>

    	<section id="hire">
    <h1>Change Account Information for <b><?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?></b></h1> 
	
	
    <form action="tiles/accountsettings.php" method="post">
	     
		  <div class="field name-box">
		        <input type="text" id="fName" placeholder="" name="fName" value="<?php echo htmlentities($_SESSION['user']['fName'], ENT_QUOTES, 'UTF-8'); ?>"/>
        		<label for="name">First Name</label>
		        <span class="ss-icon">First Name</span>
	      </div>
		  <div class="field name-box">
		        <input type="text" id="lName" placeholder="" name="lName"/ value="<?php echo htmlentities($_SESSION['user']['lName'], ENT_QUOTES, 'UTF-8'); ?>">
        		<label for="name">Last Name</label>
		        <span class="ss-icon">Last Name</span>
	      </div>
		  <div class="field name-box">
		        <input type="text" id="streetAddress" placeholder="" name="streetAddress" value="<?php echo htmlentities($_SESSION['user']['streetAddress'], ENT_QUOTES, 'UTF-8'); ?>"/>
        		<label for="name">Street Address</label>
		        <span class="ss-icon">Address</span>
	      </div>
		  <div class="field name-box">
		        <input type="text" id="city" placeholder="" name="city" value="<?php echo htmlentities($_SESSION['user']['city'], ENT_QUOTES, 'UTF-8'); ?>"/>
        		<label for="name">City</label>
		        <span class="ss-icon">City</span>
	      </div>
		  <div class="field name-box">
		        <input type="text" id="state" placeholder="" name="state"value="<?php echo htmlentities($_SESSION['user']['state'], ENT_QUOTES, 'UTF-8'); ?>"/>
        		<label for="name">State</label>
		        <span class="ss-icon">State</span>
	      </div>
		  <div class="field name-box">
		        <input type="text" id="zipCode" placeholder="" name="zipCode" value="<?php echo htmlentities($_SESSION['user']['zipCode'], ENT_QUOTES, 'UTF-8'); ?>"/>
        		<label for="name">Zip Code</label>
		        <span class="ss-icon">Zip Code</span>
	      </div>
		  <div class="field name-box">
		        <input type="tel" id="phoneNumber" placeholder="xxxxxxxxx" name="phoneNumber"value="<?php echo htmlentities($_SESSION['user']['phoneNumber'], ENT_QUOTES, 'UTF-8'); ?>"/>
        		<label for="name">Phone Number</label>
		        <span class="ss-icon">Number</span>
	      </div>
	      <div class="field email-box">
		        <input type="email" id="email" placeholder="name@email.com" name="email" value="<?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?>"/>
		        <label for="email">Email</label>
		        <span class="ss-icon">Email</span>
	      </div>

	      <div class="field password">
		        <input type="password" id="password" rows="4" placeholder="(leave blank if you do not want to change your password)" name="password" class="password"/></textarea>
		        <label for="msg">Password</label>
		        <span class="ss-icon">Pass</span>
	      </div>
		  	      <div class="field password">
		        <input type="password" id="password" rows="4" placeholder="(leave blank if you do not want to change your password)" name="password" class="password"/></textarea>
		        <label for="msg">Confirm</label>
		        <span class="ss-icon">Confirm</span>
	      </div>
		  <input class="button" type="submit" value="Update" />
		  </section>

	      <input class="button" type="submit" value="send" />
  </form>
      <script src="//assets.codepen.io/assets/common/stopExecutionOnTimeout-f961f59a28ef4fd551736b43f94620b5.js"></script>

    <script src='//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

        <script>
      $('textarea').blur(function () {
    $('#hire textarea').each(function () {
        $this = $(this);
        if (this.value != '') {
            $this.addClass('focused');
            $('textarea + label + span').css({ 'opacity': 1 });
        } else {
            $this.removeClass('focused');
            $('textarea + label + span').css({ 'opacity': 0 });
        }
    });
});
$('#hire .field:first-child input').blur(function () {
    $('#hire .field:first-child input').each(function () {
        $this = $(this);
        if (this.value != '') {
            $this.addClass('focused');
            $('.field:first-child input + label + span').css({ 'opacity': 1 });
        } else {
            $this.removeClass('focused');
            $('.field:first-child input + label + span').css({ 'opacity': 0 });
        }
    });
});
$('#hire .field:nth-child(2) input').blur(function () {
    $('#hire .field:nth-child(2) input').each(function () {
        $this = $(this);
        if (this.value != '') {
            $this.addClass('focused');
            $('.field:nth-child(2) input + label + span').css({ 'opacity': 1 });
        } else {
            $this.removeClass('focused');
            $('.field:nth-child(2) input + label + span').css({ 'opacity': 0 });
        }
    });
});
      //@ sourceURL=pen.js
    </script>

    
    
  </body>
</html>
