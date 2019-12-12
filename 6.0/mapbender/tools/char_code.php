<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Url-Encode and -Decode</title>
</head>
<body>
<form method='POST'>
  <textarea name="c" rows="10" cols="100"><?php  if($_REQUEST["c"]){echo $_REQUEST["c"];}?></textarea>
  <br>
  <input type='submit' name='encode' value='encode'>
  <br>
  <input type='submit' name='decode' value='decode'>  
</form>
<hr>
<textarea rows="10" cols="100">
<?php
if($_REQUEST["encode"]){
echo utf8_encode($_REQUEST["c"]);
}
if($_REQUEST["decode"]){
echo utf8_decode($_REQUEST["c"]);	
}
?>
</textarea>
</body>
</html>