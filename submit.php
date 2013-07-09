 <?
include('./lib/esca/contentAdmin.php'); // ESCA Backend
include('./lib/esca/escaWYSIWYG.php'); // ESCA Frontend
$contentAdmin = new ESContentAdmin("localhost","user","pass","database");
$ca = AutoWYSIWYG::getInstance($contentAdmin);
$redirLink = $_POST['esca_data_redirect'];
try {
	if ($ca->acceptPostSubmission()) {
		print('<h2>:D Success! (probably)</h2>');
	}
} catch (Exception $e) {
	print('<h2>:/ Data is invalid. Did you make sure you added text?</h2>');
}
?>
<a href="<?=$redirLink?>">Click here to return</a>
