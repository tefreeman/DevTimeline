<?php
require('../php/common.php')
?>
<head>
	<link rel="stylesheet" type="text/css" href="../css/support.css">
</head>

<h1>Support</h1>
<p id="title">We are sorry that you have problems with SinkIt. Please fill the form below and we will get back to you shortly.</p>

<form action="../php/support.php" method="post">
 
  <input type="hidden" name="email" value="<?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?>">
	Subject:<br>
  <input type="text" name="name" value=""><br>
  description:<br>
  <input type="textarea" name="description" value=""><br><br>
  <input type="submit" value="Submit">
</form>