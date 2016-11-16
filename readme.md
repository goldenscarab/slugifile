# Slugifile

Slugifile est un outils web à utiliser en mode « local » pour slugifier à la volée tous vos fichiers avant leur mise en ligne

## Prérequis

- PHP >= 5.5.9
- Un serveur Web apache ou Nginx
- Des connaissances en développement


## Installation

1. Récupérer le code sur votre serveur Web local

`git clone https://github.com/goldenscarab/slugifile`



## Configuration

Personnalisé avec votre éditeur préféré le fichier `config.php`

### `$configured`
Valeur par defaut : `false`
Mettre cette variable à `true` lorsque le fichier `config.php` est configuré


### `$app_path`
Valeur par defaut : `__DIR__`
Habituellement, il n'est pas nécessaire de changer la valeur de cette variable


### `$config`
####`input_form_name`

Nom du formulaire utilisé par la vue drag & drop *(Chaine)*

```
'input_form_name' => 'file', /* Defaut */
'input_form_name' => 'drop',
'input_form_name' => 'uploaded_file',
```

####`path_destination`

Dossier de destination des fichiers envoyé pour slugification *(Chaine)*

```
'path_destination' => $app_path . '/uploaded/', /* Defaut */
'path_destination' => $app_path . '/slugified/',
'path_destination' => '/home/USER/Documents/',
```

####`filename_separator`

Sérarateur utiliser pour la slugification *(Chaine)*

```
'filename_separator' => '-', /* Defaut */
'filename_separator' => '_',
'filename_separator' => '.',
```

####`type_allow`

Types de fichiers acceptés *(Tableau de Chaines)*

```
'type_allow' => ['all'], /* Defaut */
'type_allow' => [
    'image/jpeg',
	'image/png',
	'image/gif',
	'application/zip',
	'application/pdf'
 ]

```


## Support

Ouvrez une issue sur GitHub pour que nous jettions un oeil si vous rencontrez un problème.

## Contribuer

N'hésitez pas à forker le projet et à proposer vos idées :)

## Licence

MIT