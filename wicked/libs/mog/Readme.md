# Mog !

Le **Mog** est votre fidèle compagnon vous fournissant toujours les données et actions les plus utiles lors du processus requête/réponse.

## Installation

Copier le dossier **mog** dans votre projet et enregistrer le namespace **mog** de la librairie.

### Autoloader

Dans le cas où vous ne disposez pas d'autoloader, voici un exemple basique vous permettant de tester **Mog** (à placer à la racine de votre projet) :

```php
function __autoload($classname)
{
    $root = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    require_once $root . str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php';
}
```

## Utilisation

### Création

Le **Mog** se créé automatiquement à partir de toutes les variables globales disponibles de la requête :

```php
$mog = new mog\Mog();
```

### Données utilisateurs

Les données utilisateurs sont disponibles sous forme de tableaux :

```php
$mog->get;
$mog->post;
$mog->files;
$mog->cookie;
$mog->session;
$mog->post;
```

Exemples :

```php
$user = $mog->session['current.user'];
```

### Environnement

Les données d'environnements, sous forme d'objet :

```php
$mog->env;
$mog->server;
$mog->headers;
```

Exemple :

```php
$ip = $mog->server->remote_addr;
```

### Raccourcis

Le **Mog** propose un certain nombre de raccourcis particulièrement utiles aux développeurs.

```php
$mog->ip;       // = $mog->server->remote_addr
$mog->local;    // ip local
$mog->url;      // url actuelle
$mog->lang;     // langage de la requête
$mog->method;   // get ou post
$mog->async;    // requête ajax
$mog->sync;     // requête normal
$mog->mobile;   // utilisateur sur tablette ou smartphone
$mog->browser;  // nom du navigateur
```

### Actions

Le **Mog** propose les fonctions les plus récurrentes et utile pour du RAD.

```php
// temps écoulé depuis le début de la requête
$time = $mog->elapsed();

// redirige vers l'url spécifiée
$mog->go('your/url');

// upload un fichier vers un dossier spécifié
$mog->upload('myfile', 'upload/dir');

// force le téléchargement d'un fichier
$mog->download('path/to/my/file');

// retourne les données au format json
$mog->json(['user' => 'WickedYeti']);

// retourne le contenu d'un fichier ou d'un site
$content = $mog->read('your/url');

// écrit dans un fichier
$mog->write('Foo !!', 'bar.txt');

// affiche les données dans un style var_dump
$mog->debug('var', 'anothervar');

// soulève une fatal error personnalisée
$mog->oops('Pas de bras...');

// écrit dans le log
$mog->log('Pas de chocolat !');

// récupère les logs
$logs = $mog->logs();
```

### Le sac du Mog

Un bon compagnon est un compagnon prévoyant, or le **Mog** a toujours sur lui un sac à dos, prêt à stocker tous vos objets !

```php
$mog['map'] = new Map();        // stock l'objet
$mog['map']->find('Lindblum');  // utilise l'objet
$mog->drop('map');              // enlève l'objet
$bag = $mog->bag();             // retourne entièrement le sac
```

## Kupo !!