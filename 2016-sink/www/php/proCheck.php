    <?php
	require("common.php");
	if(empty($_SESSION['user']))
	{
    header("location: /home/index.html");
    die();
	} elseif (empty($_SESSION['user']['about']))
	{
	header("location: ../register-professional-details.html");	
	die();
	} elseif (empty($_SESSION['user']['picture']))
	{
		header("location: ../register-professional-picture.html");	
	} else 
	{
		header("location: ../index.php");	
	}
?>