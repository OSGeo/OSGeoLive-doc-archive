<?php
	require_once(dirname(__FILE__) . "/../php/mb_validateSession.php");
	
	$id = $_GET["id"];
	if (!preg_match("/[a-zA-Z0-9-_]+/", $id)) {
   		$message = "cancelled";
	}
	else {
		$result = 0;
		$cancel = false;
		
		$uploadedFile = $_FILES['myfile']['tmp_name'];
		$clientFilename = $_FILES['myfile']['name'];
		$serverFilename = Mapbender::session()->get("mb_user_id") . "-" . uniqid(true);

		$uploadDir = TMPDIR;
		if (defined("UPLOAD_DIR")) {
			$uploadDir = UPLOAD_DIR;
		}
		$allowedFileTypes = array();
		if (defined("UPLOAD_WHITELIST_FILE_TYPES")) {
			$allowedFileTypes = explode(",", UPLOAD_WHITELIST_FILE_TYPES);
		}

		// check if file type is valid
		foreach ($allowedFileTypes as $item) {
			$cancel = true;
			$message = _mb("Files with this extension are not allowed. Must be %s.", implode(", ", $allowedFileTypes));
//			$message = _mb("Dateien in diesem Format werden nicht unterstützt, wählen Sie eines der folgenden Bildformate: %s.", implode(", ", $allowedFileTypes));
			if(preg_match("/\.$item\$/i", $clientFilename)) {
				$cancel = false;
				break;
			}
		}
		
		$disallowedFileTypes = array("PHP", "PHP3", "PHP4", "PHTML", "PHP5", "PHP6");
		if (defined("UPLOAD_BLACKLIST_FILE_TYPES")) {
			$disallowedFileTypes = array_merge(
				explode(",", UPLOAD_BLACKLIST_FILE_TYPES), 
				$disallowedFileTypes
			);
		}

		
		// check if file type is valid
		foreach ($disallowedFileTypes as $item) {
			if(preg_match("/\.$item\$/i", $clientFilename)) {
				$cancel = true;
			$message = _mb("Files with extension %s are not allowed. Must be %s.", $item, implode(", ", $allowedFileTypes));
//			$message = _mb("Dateien in dem Format %s werden nicht unterstützt, wählen Sie eines der folgenden Bildformate: %s.", $item, implode(", ", $allowedFileTypes));
				break;
			}
		}

		
		$maxSize = intval(ini_get("upload_max_filesize"))*1024;
		if (defined("UPLOAD_MAX_SIZE_KB") && UPLOAD_MAX_SIZE_KB < $maxSize) {
			$maxSize = UPLOAD_MAX_SIZE_KB;
		}
		if (count($_FILES) === 0 || filesize($uploadedFile) > UPLOAD_MAX_SIZE_KB * 1024) {
			$cancel = true;
			$message = _mb("File size limit (%s KB) exceeded.", UPLOAD_MAX_SIZE_KB);
//			$message = _mb("Datei zu groß (maximal %s KB).", UPLOAD_MAX_SIZE_KB);
		}
		
		$extension = "";
		$pos = strrpos($clientFilename, ".");
		if ($pos !== false) {
			$extension = substr($clientFilename, $pos);
		}
		$serverFilename .= $extension;
		$serverFullFilename = $uploadDir . "/" . $serverFilename;
		$e = new mb_exception($serverFullFilename);
		if (!$cancel) {
			if (!copy($uploadedFile, $serverFullFilename)) {
				$status = "cancelled";
			$message = _mb("File could not be stored on server. Please contact the administrator.");
//			$message = _mb("Die Datei konnte nicht hochgeladen werden. Bitte wenden Sie sich an den Administrator.");
			}
			else {
				$status = "finished";
				$message = _mb("File has been uploaded.");
//				$message = _mb("Die Datei wurde erfolgreich hochgeladen.");
			}
		}
		else {
	   		$status = "cancelled";
		}
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<script type="text/javascript">
			var id = "<?php 
				echo $id . "___" . $serverFullFilename . "___" . $status . "___" . $message;
			?>";
			var filename = "<?php echo $clientFilename;  ?>";
		</script>
	</head>
	<body>
	</body>
</html>
