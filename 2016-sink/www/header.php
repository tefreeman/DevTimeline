    <?php
	require("/php/common.php");
	if(empty($_SESSION['user']))
	{
    header("location: /home/index.html");
    die();
	} 
?>

	