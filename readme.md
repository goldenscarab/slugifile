# Slugifile

Slugifile est un outils web à utiliser en mode « local » pour slugifier à la volée tous vos fichiers avant leur mise en ligne

## Prérequis

- PHP >= 5.5.9
- Un serveur Web apache ou Nginx
- Des connaissances en développement


## Installation

1. Récupérer le code sur votre machine

`git clone https://github.com/goldenscarab/slugifile && cd slugifile && php -S localhost:8000`


## Configuration

Personnalisé avec votre éditeur préféré le fichier `config.php`

### Variable `$configured`

Valeur par defaut : `false`

Mettre cette variable à `true` lorsque le fichier `config.php` est configuré


### Variable `$app_path`

Valeur par defaut : `__DIR__`

Habituellement, il n'est pas nécessaire de changer la valeur de cette variable


### Variable `$config`
#### Clée `input_form_name`

Valeur par defaut : `file`

Nom du formulaire utilisé par la vue drag & drop *(Chaine)*

```
'input_form_name' => 'file',
'input_form_name' => 'drop',
'input_form_name' => 'uploaded_file',
```

#### Clée `path_destination`

Valeur par defaut : `$app_path . '/uploaded/'`

Dossier de destination des fichiers envoyé pour slugification *(Chaine)*

```
'path_destination' => $app_path . '/uploaded/',
'path_destination' => $app_path . '/slugified/',
'path_destination' => '/home/USER/Documents/',
```

#### Clée `filename_separator`

Valeur par defaut : `-`

Sérarateur utiliser pour la slugification *(Chaine)*

```
'filename_separator' => '-',
'filename_separator' => '_',
'filename_separator' => '.',
```

#### Clée `type_allow`

Valeur par defaut : `[all]`

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

## Lancement de l'application

Dans votre navigateur :
`localhost:8000`

## Support

Ouvrez une issue sur GitHub pour que nous jettions un oeil si vous rencontrez un problème.

## Contribuer

N'hésitez pas à forker le projet et à proposer vos idées :)

## Licence

MIT