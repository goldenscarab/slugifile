<?php
/*
 ____                       __  _  _       
|  _ \  _ __  ___   _ __   / _|(_)| |  ___ 
| | | || '__|/ _ \ | '_ \ | |_ | || | / _ \
| |_| || |  | (_) || |_) ||  _|| || ||  __/
|____/ |_|   \___/ | .__/ |_|  |_||_| \___|
                   |_| 
 */


/**
 * Controleur dropfile
 *
 * @category   Library
 * @package    dropfileUpload.php
 * @author     Sylvain CARRE
 * @copyright  2016 Goldenscarab
 * @version    v1.0
 * @license    MIT
 * 
 */

class DropfileUpload
{
	/* Attributs */
	private $form_name;

	private $path;
	
	private $action;

	private $file_name_old;

	private $file_name_new;

	private $type_allow;

	private $file_type;

	private $file_ext;

	private $file_uploaded;

	private $add_hash;

	private $filename_separator;

	/* Méthodes */
	public function __construct($option)
	{
		
		$this->form_name          = isset($option['form_name']) ? $option['form_name'] : 'file';
		$this->path               = isset($option['path']) ? $option['path'] : $this->sendError('path');
		$this->type_allow         = isset($option['type_allow']) ? $option['type_allow'] : $this->sendError('type_allow');
		$this->add_hash           = isset($option['add_hash']) ? $option['add_hash'] : false;
		$this->filename_separator = isset($option['filename_separator']) ? $option['filename_separator'] : '-';

		//Récupération des entêtes de paramétrage
		$this->getHeaderDropfile();
	}

	private function getHeaderDropfile()
	{
		//On récupère toutes les en-têtes
		$headers = getallheaders();

		//On récupère les valeurs de paramétrage
		$this->action        = isset($headers['fileaction']) ? $headers['fileaction'] : $this->sendError('action');
		$this->file_name_old = isset($headers['filename']) ? $headers['filename'] : null;
	}

	
	public function run()
	{
		switch($this->action)
		{
			case 'create' :
				$this->create();
				break;
			case 'update' :
				$this->update();
				break;
			case 'upload' :
				$this->create();
				break; 
			case 'delete' : 
				$this->delete();
				break;
			default :
				$this->sendError('action');
				break;
		}
	}


// Controler l'action à effectuer
	// 
	// Create 
	// Récupérer le type du fichier V
	// Vérifier que le type est accepté V 
	// Générer un nouveau nom de fichier avec la bonne extention V
	// Copier le fichier temporaire dans le dossier upload V
	// 
	// Update
	// Récupérer le type du fichier 
	// Vérifier que le type est accepté
	// Générer un nouveau nom de fichier avec la bonne extention 
	// Vérifier que le nom n'existe pas.
	// Récuperer l'ancien nom de fichier
	// Vérifier que le nom de fichier existe
	// Supprimer l'ancien fichier
	// Récraser l'ancien fichier avec le nouveau
	// 
	// Suppression
	// Récupérer le nom de fichier
	// Vérifier si le fichier existe
	// Supprimer le fichier
	// 
	private function create()
	{
		//Traitement avant sauvegarde du fichier (vérification type, création nouveau nom, etc)
		$this->treatmentBeforeSaveFile();

		//Récupération du fichier temporaire
		$tmp_name = $this->file_uploaded['tmp_name'];
			
		//Déplacement du fichier dans le dossier cible
		$move_ok = $this->moveFileUploaded($this->path, $tmp_name, $this->file_name_new);

		// Si fichier bien déplacé alors on retourne sont nouveau nom à la vue
		if($move_ok) $this->returnSuccess($this->path, $this->file_uploaded['name'], $this->file_name_new);
	}

	private function update()
	{
		if($this->file_name_old != null)
		{
			//Traitement avant sauvegarde du fichier (vérification type, création nouveau nom, etc)
			$this->treatmentBeforeSaveFile();

			//Récupération du fichier temporaire
			$tmp_name = $this->file_uploaded['tmp_name'];

			//Suppression du fichier à remplacer (ancien fichier)
			$this->deleteFile($this->path."/".$this->file_name_old);

			//Déplacement du fichier dans le dossier cible
			$move_ok = $this->moveFileUploaded($this->path, $tmp_name, $this->file_name_new);

			// Si fichier bien déplacé alors on retourne sont nouveau nom à la vue
			if($move_ok) $this->returnSuccess($this->path, $this->file_uploaded['name'], $this->file_name_new);
		}
		else
		{
			$this->sendError('file_old');
		}
		
	}

	private function delete()
	{
		if($this->file_name_old != null)
		{
			//Suppression du fichier 
			$this->deleteFile($this->path."/".$this->file_name_old);
		}
		else
		{
			$this->sendError('file_old');
		}
	}

	private function treatmentBeforeSaveFile()
	{
		$this->file_uploaded = $this->getFileUploaded($_FILES[$this->form_name]);

		//Récupération du type du fichier envoyé
		$this->file_type = $this->file_uploaded['type'];

		//Vérification du type du fichier
		$this->checkFileType($this->file_type, $this->type_allow);

		//Récupération du nom de fichier envoyé
		$file_name = $this->file_uploaded['name'];

		//Récupération du nom uniquement sans l'extension
		$name = substr($file_name, 0, strpos($file_name, '.'));

		//Slugify le nom
		$name_slug = $this->slugify($name, $this->filename_separator);

		//Récupération de l'extention du fichier
		$this->file_ext = strtolower(substr(strrchr($file_name, '.'), 1));

		if ($this->add_hash) {
			//Création d'un nouveau nom de fichier avec hashage
			do
			{
				//Création d'un nom de fichier unique
				$rand = $this->generateUniqueRandomName();

				//Construction du nom de fichier
				$this->file_name_new = $name_slug . "_" .  $rand . "." . $this->file_ext;
			}
			while(file_exists($this->path."/".$this->file_name_new));

		} else {

			//Création d'un nouveau nom de fichier sans hashage
			$this->file_name_new = $name_slug .'.'. $this->file_ext;
		}
	}

	private function deleteFile($file_name)
	{
		if(file_exists($file_name))
		{
			//Suppression du fichier
			unlink($file_name);
		}
		else
		{
			$this->sendError('file');
		}
	}

	private function checkFileType($type_check, $type_allow)
	{
		// Vérification si le passe partou existe
		if(in_array('all', $type_allow)) {
			return true;
		} else {
			return (in_array($type_check, $type_allow) ? true : $this->sendError('type_allow'));
		}
		
	}

	private function getFileUploaded($form)
	{
		//On récupère les infos du fichier
		$file_uploaded = isset($form) ? $form : $this->sendError('form_name');

		if($file_uploaded['error'] != UPLOAD_ERR_OK)
		{
			$this->sendError('upload');
		}

		return $file_uploaded;
	}

	private function moveFileUploaded($path, $tmpfile, $filename)
	{
		// Si le dossier n'existe pas
		if (!is_dir($path)) {

			// On tente de créer le dossier
			if (! @mkdir($path, 0775)) {
				
				$this->sendError('create_folder_denied');
			}

		}

		// Si il est possible d'écrire à l'url donnée
		if(is_writable($path)) {
			//Déplacement du fichier dans le dossier d'upload
			return move_uploaded_file($tmpfile, $path."/".$filename);
		} else {
			$this->sendError('access_folder_denied');
		}

	}

	private function returnSuccess($path, $old_name, $new_name)
	{
		$data_return = ['status' => 'success', 'path' => $path, 'oldname' => $old_name,'newname' => $new_name];
		echo json_encode($data_return);
	}

	private function generateUniqueRandomName()
	{
		//Taille de la chaine à générer
		$name_length = 10;

		//Créer un identifiant difficile à deviner
		$name = substr(md5(uniqid(rand(), true)), 0, $name_length);

		return $name;
	}


	private function sendError($error)
	{
		$message = null;

		switch($error)
		{
			case 'path' :
				$message = "Dossier d'upload non spécifié";
				break;
			case 'action' : 
				$message = "Action non reconnue";
				break;
			case 'type_allow' :
				$message = "Type de fichier non accepté : [" . $this->file_type . "]";
				break;
			case 'form_name' :
				$message = "Aucun fichier envoyé depuis le champs : [" . $this->form_name . "]";
				break;
			case 'upload' :
				$message =  $this->getFileError($this->file_uploaded['error']) . " : [" . $this->file_uploaded['name'] . "]";
				break;
			case 'access_folder_denied' :
				$message = "Dossier non accessible en écriture : [" . $this->path . "]";
				break;
			case 'create_folder_denied' :
				$message = "Impossible de créer le dossier : [" . $this->path . "]";
				break;
			case 'file_old' : 
				$message = "Fichier à traiter non spécifié";
				break;
			case 'file' : 
				$message = "Le fichier n'existe pas";
				break;
			default :
				$message = "Non déterminée";
				break;
			
		}
		//Debug::var_display($message); 
		echo json_encode(['status' => 'error', 'info' => $message ]);
		exit();
	}

	//Fonction affichant les erreurs liées au téléversement du fichié envoyé
	private function getFileError($error)
	{
		$msg_error = "";
		switch ($error) 
		{
			case UPLOAD_ERR_INI_SIZE : //1
				$msg_error = "La taille du fichier téléchargé excède la valeur ";
				$msg_error .= "de upload_max_filesize, configurée dans le php.ini.";
				break;
			
			case UPLOAD_ERR_FORM_SIZE : //2
				$msg_error = "La taille du fichier téléchargé excède la valeur ";
				$msg_error .= "de MAX_FILE_SIZE, qui a été spécifiée dans le formulaire HTML.";
				break;

			case UPLOAD_ERR_PARTIAL : //3
				$msg_error = "Le fichier n'a été que partiellement téléchargé.";
				break;

			case UPLOAD_ERR_NO_FILE : //4
				$msg_error = "Aucun fichier n'a été téléchargé.";
				break;

			case UPLOAD_ERR_NO_TMP_DIR : //6
				$msg_error = "Un dossier temporaire est manquant.";
				break;

			case UPLOAD_ERR_CANT_WRITE : //7
				$msg_error = "Échec de l'écriture du fichier sur le disque.";
				break;

			case UPLOAD_ERR_EXTENSION : //8
				$msg_error = "Une extension PHP a arrêté l'envoi de fichier.";
				break;

			default:
				$msg_error = "Erreur d'upload inconnue.";
				break;
		}

		return $msg_error;
	}

	/**
	 * Slugify une chaine
	 * 
	 * @param string La chaîne à slugifier
	 * @return string La chaîne slugifiée
	 */
	private function slugify($string, $separator = '-')
	{
		//Table de conversion des caractères étrangé
		$foreign_characters = array(
			'/ä|æ|ǽ/' => 'ae',
			'/ö|œ/' => 'oe',
			'/ü/' => 'ue',
			'/Ä/' => 'Ae',
			'/Ü/' => 'Ue',
			'/Ö/' => 'Oe',
			'/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ|Α|Ά|Ả|Ạ|Ầ|Ẫ|Ẩ|Ậ|Ằ|Ắ|Ẵ|Ẳ|Ặ|А/' => 'A',
			'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª|α|ά|ả|ạ|ầ|ấ|ẫ|ẩ|ậ|ằ|ắ|ẵ|ẳ|ặ|а/' => 'a',
			'/Б/' => 'B',
			'/б/' => 'b',
			'/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
			'/ç|ć|ĉ|ċ|č/' => 'c',
			'/Д/' => 'D',
			'/д/' => 'd',
			'/Ð|Ď|Đ|Δ/' => 'Dj',
			'/ð|ď|đ|δ/' => 'dj',
			'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě|Ε|Έ|Ẽ|Ẻ|Ẹ|Ề|Ế|Ễ|Ể|Ệ|Е|Э/' => 'E',
			'/è|é|ê|ë|ē|ĕ|ė|ę|ě|έ|ε|ẽ|ẻ|ẹ|ề|ế|ễ|ể|ệ|е|э/' => 'e',
			'/Ф/' => 'F',
			'/ф/' => 'f',
			'/Ĝ|Ğ|Ġ|Ģ|Γ|Г|Ґ/' => 'G',
			'/ĝ|ğ|ġ|ģ|γ|г|ґ/' => 'g',
			'/Ĥ|Ħ/' => 'H',
			'/ĥ|ħ/' => 'h',
			'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|Η|Ή|Ί|Ι|Ϊ|Ỉ|Ị|И|Ы/' => 'I',
			'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|η|ή|ί|ι|ϊ|ỉ|ị|и|ы|ї/' => 'i',
			'/Ĵ/' => 'J',
			'/ĵ/' => 'j',
			'/Ķ|Κ|К/' => 'K',
			'/ķ|κ|к/' => 'k',
			'/Ĺ|Ļ|Ľ|Ŀ|Ł|Λ|Л/' => 'L',
			'/ĺ|ļ|ľ|ŀ|ł|λ|л/' => 'l',
			'/М/' => 'M',
			'/м/' => 'm',
			'/Ñ|Ń|Ņ|Ň|Ν|Н/' => 'N',
			'/ñ|ń|ņ|ň|ŉ|ν|н/' => 'n',
			'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ|Ο|Ό|Ω|Ώ|Ỏ|Ọ|Ồ|Ố|Ỗ|Ổ|Ộ|Ờ|Ớ|Ỡ|Ở|Ợ|О/' => 'O',
			'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º|ο|ό|ω|ώ|ỏ|ọ|ồ|ố|ỗ|ổ|ộ|ờ|ớ|ỡ|ở|ợ|о/' => 'o',
			'/П/' => 'P',
			'/п/' => 'p',
			'/Ŕ|Ŗ|Ř|Ρ|Р/' => 'R',
			'/ŕ|ŗ|ř|ρ|р/' => 'r',
			'/Ś|Ŝ|Ş|Ș|Š|Σ|С/' => 'S',
			'/ś|ŝ|ş|ș|š|ſ|σ|ς|с/' => 's',
			'/Ț|Ţ|Ť|Ŧ|τ|Т/' => 'T',
			'/ț|ţ|ť|ŧ|т/' => 't',
			'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ|Ũ|Ủ|Ụ|Ừ|Ứ|Ữ|Ử|Ự|У/' => 'U',
			'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ|υ|ύ|ϋ|ủ|ụ|ừ|ứ|ữ|ử|ự|у/' => 'u',
			'/Ý|Ÿ|Ŷ|Υ|Ύ|Ϋ|Ỳ|Ỹ|Ỷ|Ỵ|Й/' => 'Y',
			'/ý|ÿ|ŷ|ỳ|ỹ|ỷ|ỵ|й/' => 'y',
			'/В/' => 'V',
			'/в/' => 'v',
			'/Ŵ/' => 'W',
			'/ŵ/' => 'w',
			'/Ź|Ż|Ž|Ζ|З/' => 'Z',
			'/ź|ż|ž|ζ|з/' => 'z',
			'/Æ|Ǽ/' => 'AE',
			'/ß/' => 'ss',
			'/Ĳ/' => 'IJ',
			'/ĳ/' => 'ij',
			'/Œ/' => 'OE',
			'/ƒ/' => 'f',
			'/ξ/' => 'ks',
			'/π/' => 'p',
			'/β/' => 'v',
			'/μ/' => 'm',
			'/ψ/' => 'ps',
			'/Ё/' => 'Yo',
			'/ё/' => 'yo',
			'/Є/' => 'Ye',
			'/є/' => 'ye',
			'/Ї/' => 'Yi',
			'/Ж/' => 'Zh',
			'/ж/' => 'zh',
			'/Х/' => 'Kh',
			'/х/' => 'kh',
			'/Ц/' => 'Ts',
			'/ц/' => 'ts',
			'/Ч/' => 'Ch',
			'/ч/' => 'ch',
			'/Ш/' => 'Sh',
			'/ш/' => 'sh',
			'/Щ/' => 'Shch',
			'/щ/' => 'shch',
			'/Ъ|ъ|Ь|ь/' => '',
			'/Ю/' => 'Yu',
			'/ю/' => 'yu',
			'/Я/' => 'Ya',
			'/я/' => 'ya'
		);
		
		//Suppression de tous les guillemets
		$string = preg_replace("#[\"\']#", '', $string);

		//Traitement des caractères étrangé
		$string = preg_replace(array_keys($foreign_characters), array_values($foreign_characters), $string);
		
		//Suppression tous les caractères particulière, ponctuations etc
		$string = preg_replace("#[\.;:'\"\]\?\}\[\{\+\)\(\*&\^\$\#@\!,±`%~']#iu", '', $string);
		
		//Suppression des caractères invisible autre que l'espaces
		$string = preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $string);
		
		//Remplacement de tous les séparateurs par le séparateurs défini
		$string = preg_replace("#[/_|+ -]+#u", $separator, $string);
		
		//Suppression éventuelle du séparateur en début et en fin de chaine
		$string = trim($string, $separator);

		//Conversion en minuscule
		$string = strtolower($string);

		return $string;
	}
}