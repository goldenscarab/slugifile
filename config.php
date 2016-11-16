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
	 * File config for Slugifile
	 *
	 * @category   configuration
	 * @package    config.php
	 * @author     Sylvain CARRE
	 * @copyright  2016 Goldenscarab
	 * @version    v1.0
	 * @license    MIT
	 *
	 * Documentation : https://github.com/goldenscarab/slugifile
	 * 
	 */

	/**
	 * Is this file configured ?
	 * @var boolean
	 * @default false
	 */
	$configured = false;

	/**
	 * Path of this application
	 * @var String
	 * @default __DIR__
	 */
	$app_path = __DIR__;

	/**
	 * Config for connector
	 * @var Array of String
	 * @default input_form_name 	: 'file'
	 * @default path_destination 	: $app_path . '/uploaded/'
	 * @default filename_separator 	: '-'
	 * @default type_allow 			: ['all']
	 */
	$config = [
		
		// Name of the drop form
		'input_form_name' => 'file',
		
		// Destination path of the file uploaded
		'path_destination' => $app_path . '/uploaded/',
		
		// File name output separator
		'filename_separator' => '-',
		
		// List of file type possible
		'type_allow' => ['all']
	];