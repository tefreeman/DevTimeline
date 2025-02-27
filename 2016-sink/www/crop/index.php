<?php
/*--------------------------------------------------------------------------------------------
|	@desc:		Live image crop with php and jquery
|	@author:	Aravind Buddha
|	@url:		  http://www.techumber.com
|	@date:		16 September 2012
|	@email:   aravind@techumber.com
|	@license:	Free! to Share,copy, distribute and transmit , 
|               but i'll be glad if my name listed in the credits'
---------------------------------------------------------------------------------------------*/
$milliseconds = round(microtime(true) * 1000);
$newImgName = md5(uniqid($_SESSION['user']['username'], true)).".jpg"; 
$image='';
$err='';
if(isset($_POST['submit'])){
	//error variable to hold your error message 
	$err="";
	$path = "uploads/";
	//alled image format will be used for filter	
	$allowed_formats = array("jpg", "png", "gif", "bmp");
	$imgname = $_FILES['img']['name'];
	$tmpname = $_FILES['img']['tmp_name'];
	$size = $_FILES['img']['size'];
	//validate image
	if(!$imgname){
		$err="<strong>Oh snap!</strong>Please select image..!";
		return false;
	}
	if($size > (1024*1024)){
		$err="<strong>Oh snap!</strong>File Size is too large..!";
	}
	list($name, $ext) = explode(".", $imgname);
	if(!in_array($ext,$allowed_formats)){
			$err="<strong>Oh snap!</strong>Invalid file formats only use jpg,png,gif";
			return false;					
	}
	if($ext=="jpg" || $ext=="jpeg" ){
		$src = imagecreatefromjpeg($tmpname);
	}
	else if($ext=="png"){
		$src = imagecreatefrompng($tmpname);
	}
	else {
		$src = imagecreatefromgif($tmpname);
	}
	list($width,$height)=getimagesize($tmpname);
	if($width > 600){
		$newwidth= 320;
		$newheight=($height/$width)*$newwidth;
		$tmp=imagecreatetruecolor($newwidth,$newheight);
		imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
		$image = $path.$imgname;
		imagejpeg($tmp,$path.$imgname,75);
		move_uploaded_file($image,$path.$imgname);
	}
	else{
		if(move_uploaded_file($tmpname,$path.$imgname)){
			$image="../uploads/".$imgname;
		}
		else{
			$err="<strong>Oh snap!</strong>failed";
		}
	}	
}
?>
<!DOCTYPE html>
 <!--
    o           		                      			        o8      
   o8                   		             			          88      
  o88oo ooooooo   oooo 88	  88 ooo   ooo  oo   oo    oo 88oooo.  ooooooo  ooooodb
   88   88	     88	   88	  88  88   88   88P"Y8bP"Y8b  d8   8b  88  	   88 
   88   8888888  88	   8888888  88   88   88   88   88  88   88  8888888  88
   88   88 		   88	   88	  88	88   88   88   88   88  88   88  88   	   88    
   888  ooooooo   8ooo 88	  88	 V888V   o88o o88o o88o  Y8b8P   oooooooo   d88b   

@url: www.techumber.com
-->
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Image Crop using php and javascript:techumber.com</title>
	<link rel="stylesheet" href="lib/imgareaselect/css/imgareaselect-default.css" />
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<header class="logo wrap">
		<a href="http:/www.techumber.com">
			<img src="http://demo.techumber.com/asserts/img/logostd.png" alt="techumber.com logo"/> 
		</a>
	</header>
		<div class="container wrap">
			<div class="left">
				<?php 
				//if image uploaded this section will shown
					if($image){
						echo "<h2>Select an area on image</h2><img style='' src='".$image."' id=\"imgc\" style='width:100%' >";
					}
				?>
			</div>
			<div class="right">
					<?php 
					//if image uploaded this section will shown
					if($image){	
						//echo '<div>' ;
						echo '<h2>Preview</h2>' ;
						echo '<div class="frame">' ;
						echo '<div id="preview">';
						echo '<img src="'.$image.'" >'; 
						echo '</div></div></div>';
						echo "<div id='output'></div>";
						echo "<img src='' id='cropedimg' />";
						echo '<button id="cropbtn">Crop</button>';
					}
				?>
			</div>	
				<?php
				//if any error while uploading
				if($err){
					echo '<div class="alert alert-error">'.$err.'</div>';
				}
				?>
				<form id="imgcrop" method="post" enctype="multipart/form-data">
					<input type="file" name="img" id="img" />
					<input type="hidden" name="imgName" id="imgName" value="<?php echo($imgname) ?>" />
					<button name="submit">Submit</button>
				</form>
			<div style="clear:both"></div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script type="text/javascript" src="lib/imgareaselect/jquery.imgareaselect.js"></script>
  <script type="text/javascript" src="js/process.js"></script>
</body>
</html>

