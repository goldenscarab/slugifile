<?php
	
/*
 ____                      _____  _  _       
|  _ \  _ __  ___   _ __  |  ___|(_)| |  ___ 
| | | || '__|/ _ \ | '_ \ | |_   | || | / _ \
| |_| || |  | (_) || |_) ||  _|  | || ||  __/
|____/ |_|   \___/ | .__/ |_|    |_||_| \___|
                   |_|  

 */

/**
 * Connecteur vers le controleur dropfile
 *
 * @category   Library
 * @package    connector.php
 * @author     Sylvain CARRE
 * @copyright  2016 Goldenscarab
 * @version    v1.0
 * @license    MIT
 * 
 */



// On inclut le fichier de configuration
include (__DIR__ . "/../config.php");

// On inclut la librairie dropfile
include ("dropfileUpload.php");

// Définition des options du controleur
$options = array(
	'form_name'          => $config['input_form_name'],
	'path'               => $config['path_destination'],
	'filename_separator' => $config['filename_separator'],
	'type_allow'         => $config['type_allow']
);

// Configuration du controleur
$upload = new DropfileUpload($options);

// Lancement du traitement du fichier envoyé
$upload->run();
