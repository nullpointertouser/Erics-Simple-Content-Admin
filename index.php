<? /* Eric's Simple Content Admin (ESCA)
This demo index contains all the code needed to implement ESCA on a website.
Normally, this code would be spead across the various files of a framework.
*/

include('./lib/esca/contentAdmin.php'); // ESCA Backend
include('./lib/esca/escaWYSIWYG.php'); // ESCA Frontend
include('./lib/misc/html_shortcuts.php');

// Setup MySQL Connection using PHPs MySQLi interface
$mysqli = new mysqli("localhost","user","pass","database");
if ($mysqli->connect_errno) {
	die($mysqli->connect_errno . ": " . $mysqli->connect_error);
}
// Create instance of ESContentAdmin for serverside operations of ESCA
$contentAdmin = new ESContentAdmin($mysqli);
// Other option: $contentAdmin = new ESContentAdmin("localhost","root","icanhasaccess","testing_db");

// Create instance of AutoWYSIWYG; this generates the web interface for editing content.
$ca = AutoWYSIWYG::getInstance($contentAdmin);

/*
The following is redundant; this is an example of setting the location of the resources
folder to the default location. Include this if you're using a different name for your
resources folder.
*/
$ca->setLocations(array(
    'resources' => 'res',
));

// Check if client is an admin. Note that ESCA has NO built in authentication system.
if ($_GET['admin']) {
	// Turn on web interface for editing
	$ca->setEditMode(1);
	/* IMPORTANT: Generated forms will have the action set as the request url be default. This is for testing purposes
	only, and should be changed to avoid XSS vunerabilities. Redirecting to the request url after should be safe though. */
	$ca->setBlipDefaults(array(
		'action' => 'submit.php',
		'data' => array(
			'redirect' => $_SERVER['REQUEST_URI'],
			'foobar' => 'asdf',
		) // I recommend adding comma to the last element so you can add another with no hassle.
	));
	// Check for sumission to this page
	try {
		if ($ca->acceptPostSubmission()) {
			print('<!-- '.htmlentities($_POST['esca_data_foobar']).' -->');
		}
	} catch (Exception $e) {
		print("Exception: ".$e->getMessage());
	}
}

// No longer needed. Later, adding this might enable changes to blips before outputBlip is called
//$ca->loadBlips("box1");
?>
<html>
<head>
	<title>ESCA Example</title>
	<?
		includeJS('./res/js/lib/jquery.js');
		$ca->outputHead(array(
			//'excludeButtons' => array('xhtml'), // Suppose we don't want the site admin editing in html mode
		));
	?>
	<style type="text/css">
		BODY {
			text-align: center;
			background-color: #777777;
		}
		.container {
			width: 90%;
			display: inline-block;
			text-align: left;
			background-color: #CCCCCC;
		}
		.thingy {
			position: relative;
			width: 100%;
		}
		.sidea {
			display: inline-block;
			width: 49%;
		}
		.sideb {
			display: inline-block;
			width: 50%;
		}
		.abox {
			width: 100%;
		}
	</style>
</head>
<body>
	<div class="container">
		<?$ca->outputBlip(array(
					'name' => "title",
					'class' => "abox",
					'style' => "font-size: 40px; height: 44px; background-color: #FFFFFF",
				));?>
		<div class="thingy">
			<div class="sidea">
				<?$ca->outputBlip(array(
					'name' => "box1",
					'class' => "abox",
				));?>
			</div>
			<div class="sideb">
				<?$ca->outputBlip(array(
					'name' => "box2",
					'class' => "abox",
				));?>
			</div>
		</div>
	</div>
</body>
</html>

