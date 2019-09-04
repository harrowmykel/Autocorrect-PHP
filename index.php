<?php 

include_once("autocorrect.php");

$corrected_word = "";
$word = "";
$sentence = "";
if(isset($_GET["doautocorrect"])){
	$word = $_GET["word"];
	$autocorrect = new AutoCorrect();
	$corrected_word = $autocorrect->correct($_GET["word"]);

  if ($corrected_word === $word) {
    $sentence = " - $word is spelled correctly.";
  }  else {
    $sentence = " - $word should be spelled as $corrected_word .";
  }
  /*else if (typeof correction === "undefined") {
    return " - " + word + " didn't get any output from the spellchecker.";
  }*/
}

?>

 <!DOCTYPE html>
 <html>
 <head>
 	<title>Auto Correct</title>
 </head>
 <body>
 
<form action="#" method="get">
  Type in Word and click submit<br><br>
  Word:<br>
  <input type="text" name="word" value="<?php echo $word;?>"><br>
  Corrected Word:<br>
  <input type="text" name="corrected_word" value="<?php echo $corrected_word;?>" disabled>
  <input type="hidden" name="doautocorrect" value="Mouse">
  <br>
  <br><?php echo $sentence;?>
  <br>
  <input type="submit" value="Submit">
</form> 

 </body>
 </html>