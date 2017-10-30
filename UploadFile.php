<!DOCTYPE html>
<!--
//  File UploadFile.php
//	Author: Sreevilasan K.
//	Written on 2-Oct-2017
-->
<!DOCTYPE html>
<?php
	require 'include/sessioncheck.php';
	require 'include/commonclass.php';

	$target_dir = "images/photo/";
	
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if($check !== false) {
			echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}
	

		// Check if file already exists
		if (file_exists($target_file)) {
			echo "File already exists.";
			$uploadOk = 0;
		}

		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 500000) {
			echo "Sorry, your file is too large.";
			$uploadOk = 0;
		}

		// Allow certain file formats
		if (!($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType != "gif" )) {
			echo "Only JPG, JPEG, PNG & GIF files are allowed.";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "Sorry, your file was not uploaded.";
			// if everything is ok, try to upload file
			} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			} else {
				echo "There was an error while uploading your file.";
			}
		}
	}
?>
<html>
<head>
	<link rel="stylesheet" href="css/LasStyle.css">
</head>

<body>
<table class="table10"><tr><td>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
	<br><br>
	<a href="#" onclick="document.getElementById('fileToUpload').click(); return false;" />Select New Photo</a>
	<input type="file" name="fileToUpload" id="fileToUpload" style="visibility: hidden;" /><br><br>
    <!--<input type="file" name="fileToUpload" id="fileToUpload">-->
<?php 
	if ($target_file != "") {
		echo '<img src="' . $target_file . '" height="300" width="400"><br><br>';	
	}
?>
    <input type="submit" value="Upload Photo" name="submit">
</form>
</td></tr></table>
</body>
</html>