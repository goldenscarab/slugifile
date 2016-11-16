<?php   
/*
	 ____   _                _   __  _  _       
	/ ___| | | _   _   __ _ (_) / _|(_)| |  ___ 
	\___ \ | || | | | / _` || || |_ | || | / _ \
	 ___) || || |_| || (_| || ||  _|| || ||  __/
	|____/ |_| \__,_| \__, ||_||_|  |_||_| \___|
	                  |___/  

 */

/**
 * Interface Web pour slugifier facilement vos fichiers en local
 *
 * @category   IHM
 * @package    index.php
 * @author     Sylvain CARRE
 * @copyright  2016 Goldenscarab
 * @version    v1.0
 * @license    MIT
 * 
 */


// On inclut le fichier de configuration
include (__DIR__ . "/config.php");

?>


<!DOCTYPE html> 
<html lang="fr"> 
	<head> 
		<meta charset= "utf-8"/>
		<meta name="robots" content="index,follow">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<base href="URL_BASE_PATH"/> 
		<title>Slugifile</title> 
		
		<link rel="stylesheet" href="assets/css/dropfile.css" type="text/css"/>
		<link rel="stylesheet" href="assets/libs/bootstrap/css/bootstrap.min.css" type="text/css"/>
		
		<style>
			body {
				padding-top: 70px;
				padding-bottom: 70px;
			}
			.container {
				max-width: 800px;
			}
			#zone
			{
				width: 100%;
				height: 300px;
				margin-bottom: 30px;
			}
		</style>
		</head> 
	<body> 
	<!-- HTML -->
		<header>
			<nav class="navbar navbar-inverse navbar-fixed-top">
		        <div class="container">
		            <div class="navbar-header">
		              <a class="navbar-brand" href="/">Slugifile</a>
		            </div>
		            <div id="navbar" class="collapse navbar-collapse"></div>
		        </div>
		    </nav>
		</header>
	    <section class="container">
	    	<div class="row">
	    		<div class="col-xs-12">
					<h1>
						Slugifieur de noms de fichiers en masse <br>
						<small>Déplacer vos fichiers à « slugifier » dans la zone</small>
					</h1>
					<hr>
					<?php if(!$configured) : ?>
						<div class="alert alert-info alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<strong>Conseil !</strong> Penser à configurer le fichier « config.php »
						</div>
					<?php endif; ?>
					<div id="zone"></div>
		    	</div>
	    	</div>
	    </section>
		<footer class="container">
			<nav class="navbar navbar-inverse navbar-fixed-bottom">
		        <div class="container">
		            <p class="navbar-text">
		              	&copy; <?= date('Y'); ?> Goldenscarab <a href="https://github.com/goldenscarab"></a>
		            </p>
		        </div>
		    </nav>
		</footer>

		<!-- Scripts -->
		<script src="assets/libs/jquery/jquery-3.1.1.min.js"></script>
		<script src="assets/libs/bootstrap/js/bootstrap.min.js"></script>

		<!-- Dropfile -->
		<script type="text/javascript" src="assets/js/simpledropfile.js"></script>
		<script> 
			$(document).ready(function()
			{
				//Application du plugin sur la zone
				$('#zone').dropfile({
					message: '<i class="glyphicon glyphicon-save-file"></i> Glisser ici vos fichiers',
					onefile: false
				}, displayStatus);
			});

			// Affichage d'un message d'info
			function displayStatus(drop) {
				
				console.info(drop);

				// Si la barre d'alert n'existe pas, on la crée
				if ($('#status').length == 0) {
					var box_alert = "";
					box_alert += '<div id="status" class="alert alert-success alert-dismissible" role="alert">';
					box_alert += '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
					box_alert += '</div>';

					// Ajout du code html au DOM
					$('#zone').after(box_alert);
					$('#status').append("<strong>Dossier : </strong>"+drop.path+"<br/>");
				}

				// On ajout les noms de fichiers
				var line = "<tr><td>&nbsp;&nbsp;✔&nbsp;&nbsp;</td><td>" +drop.oldname + "&nbsp;&nbsp;</td><td>&nbsp;&nbsp;<i class=\"glyphicon glyphicon-arrow-right\"></i>&nbsp;&nbsp;<strong>"+drop.newname+"</strong></td></tr>";
				$('#status').append(line);
			}
			
		</script> 
	</body> 
</html>