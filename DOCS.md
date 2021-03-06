# WickedFramework

Wicked est un petit framework en PHP5.4 artisanal, rapide et sympa ne gardant que l'essentiel pour les projets modestes :)

1. [Quickstart](#quickstart)
    - [Organisation](#organisation)
    - [Bootstrap](#boostrap)
    - [Namespaces](#namespaces)
    - [Processus](#processus)
2. [Action](#dfinir-vos-actions)
    - [Requête](#requte)
    - [Mog & Syn](#mog--syn)
    - [Session](#session)
    - [Messages flash](#messages-flash)
    - [Formulaire](#donnes-formulaire)
    - [Authentification](#authentification)
    - [Redirection](#redirection)
    - [Interception de vue](#interception-de-vue)
    - [Auto-wire](#auto-wire)
3. [Vue](#crer-votre-vue)
    - [Layout](#layout)
    - [Assets](#assets)
    - [Slot & Hook](#slot--hook)
4. [Router](#rgles-du-routeur)
    - [Presets](#plop)
    - [Règles](#vos-rgles)
    - [Combinaison](#combinaison)
    - [Route actuelle](#la-route-actuelle)
4. [Evénements](#les-vnements)
5. [Outils](#outils)


## Quickstart

### Organisation

Il est conseillé d'utiliser une organisation de projet MVP classique, cependant rien n'est imposé et vous pouvez suivre votre propre organisation.

```php
/app
    /controllers    // contrôleurs et services
    /public         // fichiers css, js et images
    /views          // vues et layouts
    index.php       // point d'entrée de l'application
/wicked
```

### Bootstrap

Le fichier `bootstrap.php` permet d'inclure les librairies et mécanismes de **Wicked** dans votre projet et ainsi d'appeler les classes nécessaires à votre application :

```php
# index.php
require '../wicked/bootstrap.php';

$app = new wicked\App();
$app->run();
```

Le framework utilise un système d'url *friendly* et nécessite un fichier `.htaccess` à la racine de votre application pour rediriger toutes les requêtes vers votre fichier `index.php` :

```
SetEnv PHP_VER 5_4
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !favicon.ico$
    RewriteRule ^(.*)$ index.php?$1 [QSA,L]
</IfModule>
```

### Namespaces

Toutes vos classes seront reconnues par **Wicked** grâce au *vendor* `app\` et par un système par *convention* :

```php
namespace app\foo;

class Bar {}
```

Dans ce cas là, le fichier chargé sera : `foo/Bar.php`.

L'objectif principal de **Wicked** est de garder une simplicité optimale dans le développement de votre application, ainsi aucun *contrôleur* ou *modèle* ne devra étendre de quoi que ce soit,
vous laissant ainsi plus de liberté dans la conception de vos classes.

### Processus

Le procesus d'un framework MVP est relativement simple :

```
Requête -> Action -> Vue
```

Nous allons définir, étape par étape, les différentes classes et fichiers nécessaire à ce processus.


## Définir vos actions

Le coeur d'une application sont les actions et services, la `business layer` . Dans une architecture MVP, les actions sont matérialisées par des *contrôleurs* et des *méthodes*.
Nous allons prendre l'exemple d'un `FrontController` (le point d'entrée de votre application) :

```php
namespace app\controllers;

class Front
{

    public function index()
    {

    }

    public function hello($name)
    {
        // do some stuff
        return ['name' => $name];
    }

}
```

Dans notre contrôleur, nous avons 2 actions disponibles : `index` et `hello`, dont la dernière renvoyant un tableau de données, qui sera passé à la vue dans la suite du processus.

### Requête

**Wicked** utilise la libraire [Mog](https://github.com/WickedYeti/Mog) pour gérer la requête ainsi qu'un ensemble d'helpers :

### Mog & Syn

**Syn** est un micro-ORM, composant indispensable de **Wicked**.
Pour vous permettre un accès simplifié partout dans votre application, 2 fonctions sont disponibles `mog()` et `syn()`  :

```php
namespace app\controllers;

class Front
{
    public function index()
    {
        $route = mog()->route; // récupère la route actuelle
    }
}
```

et

```php
namespace app\controllers;

class Front
{
    public function index()
    {
        $users = syn()->user->find(); // retourne tous les utilisateurs
    }
}
```


### Accès à la session

*NB : Toutes les données non-scalaires seront sérialisées.*

```php
namespace app\controllers;

class Front
{

    public function index()
    {
        session('foo', 'bar');      // définit une donnée
        $foo = session('foo');      // accède à une donnée
        session()->clear('foo');    // efface une donnée
        session()->clear();         // efface entièrement la session
    }

}
```

### Messages flash

Attention, la récupération **consomme** le message, il ne sera plus disponible !

```php
# controllers/Front.php
namespace app\controllers;

class Front
{

    public function index()
    {
        flash('success', 'Yeah !');
    }

}
```

```php
# views/front/index.php
<?php if($message = flash('success')): ?>
<div class="flash">
    <?= $message ?>
</div>
<?php endif; ?>
```


### Données formulaire

```php
namespace app\controllers;

class Front
{

    public function search()
    {
        if(post()) {
            $query = post('query'); // récupère un champ
            syn()->user->find(['username' => $query]);
        }
    }

    public function edit($id)
    {
        $user = syn()->user->find($id);
        hydrate($user, post()); // hydrate l'objet avec les données POST
        syn()->user->save($user);
    }

}
```

### Authentification

Deux helpers permettent d'authentifier et de récupérer l'utilisateur courant et son rang de manière simple : `auth()` et `user()`.

```php
namespace app\controllers;

class User
{

    public function login($id)
    {
        $user = syn()->user->find($id);
        auth($user, 9);
    }

    public function profile()
    {
        $user = user();
        $rank = user('rank');
        return ['user' => $rank, 'rank' => $rank]
    }

    public function logout()
    {
        auth(false);
    }

}
```

Dans le cas où certaines actions sont strictement protégées pour un certain rang, la gestion se fait par annotation soit sur le contrôleur en entier :

```php
namespace app\controllers;

/**
 * @rank 8
 */
class Front
{
    ...
}
```

Soit directement sur la méthode :

```php
namespace app\controllers;

class Front
{

    /**
     * @rank 4
     */
    public function index()
    {
        ...
    }

}
```

Cette annotation sera comparée à `user('rank');` afin de déterminer si l'utilisateur a le droit ou non d'accéder à cette action (supérieur ou égal).
Dans le cas contraire, un événement `403` est déclenché *(par défaut, le rang défini par la méthode sera prioritaire sur le contrôleur)*.

### Redirection

L'url est relative à l'application, pour une redirection vers l'extérieur voir `mog()->go('somwhere');`

```php
namespace app\controllers;

class Front
{

    public function index()
    {
        go('/not/available'); // http://your.app/not/available
    }

}
```

### Interception de vue

Il est possible de changer de vue à la volée dans les contrôleurs grâce à l'annotation `@view` :

```php
namespace app\controllers;

class Front
{

    /**
     * @view path/to/another/view.php
     */
    public function hello()
    {
        return ['name' => 'world'];
    }
}
```

### Auto-wire

Similaire aux EJB de Java, l'auto-wiring permet de lier automatiquement un objet à un attribut d'une classe grâce à la PHPDoc de ce dernier.
Vous pouvez définir vos propres objets pouvant être accédé par l'auto-wire dans votre `index.php` grâce à ce mécanisme :

```php
$app['bear'] = new Bear('graoow');
```

Par ce mécanisme, **Wicked** donnera automatiquement son **Bear** au contrôleur afin que l'utilisateur puisse accéder aux fonctionnalités de ce dernier.

```php
namespace app\controllers;

class Front
{
    /**
     * @wire wicked.bear
     */
    public $bear;
}
```


## Créer votre vue

L'action, correctement effectuée, peut éventuellement renvoyer des données, il est temps de les affichers dans notre vue.
Prenons l'exemple de l'action `app\controllers\Front::hello($name)` :

```html
# views/front/hello.php
<h1>Hello <?= $name ?> !</h1>
```

Le tableau `['name' => $name]` retourné par l'action définit les variables visibles par la vue, dans notre cas : `$name`.

### Layout

Il arrive très frequemment que plusieurs vues utilisent un *layout* commun afin de ne pas dupliquer le code.
Nous allons donc créer le fichier `views/layout.php` :

```html
<!doctype html>
<html>
    <head>
        <title>My first WickedApp !</title>
        <meta charset="utf-8">
    </head>
    <body>
        <?= self::content(); ?>
    </body>
</html>
```

Et définir dans la vue quel *layout* nous allons utiliser :

```html
<?php self::layout('views/layout.php'); ?>

<h1>Hello <?= $name ?> !</h1>
```

Grâce à ce mécanisme, le contenu de la vue sera afficher dans le layout à la place du `self::content()`;

### Assets

3 fonctions sont disponibles afin de facilité l'appel de fichiers publics :

```php
self::css('layout');                    // pour /public/css/layout.css
self::js('jquery', 'main');             // pour /public/js/jquery.js et /public/js/main.js
self::asset('img/background.png');      // pour /public/img/background.png
```

Exemple :

```php
<!doctype html>
<html>
    <head>
        <title>My first WickedApp !</title>
        <meta charset="utf-8">
        <?= self::css('layout'); ?>
        <?= self::js('jquery'); ?>
    </head>
    <body>
        <?= self::content(); ?>
    </body>
</html>
```

### Slot / Hook

Le *layout* a pour vocation d'être fixe et invariant, cependant certaines informations nécessite d'être dynamique (le nom de l'utilisateur en cours par exemple)
et peuvent être changées suivant l'action. Afin de déterminer ces zones, le *layout* à besoin de connaitre l'emplacement :

```php
<!doctype html>
<html>
    <head>
        <title>My first WickedApp !</title>
        <meta charset="utf-8">
    </head>
    <body>
        <header><?= self::hook('username'); ?></header>
        <?= self::content(); ?>
    </body>
</html>
```

Ainsi que la vue doit envoyer le contenu dynamique :

```php
self::layout('views/layout.php');
self::slot('username', 'WickedYeti');
```


## Règles du routeur

Nos actions et nos vues sont prêtes ! Il est temps de comprendre comment le router va définir l'action à executer en fonction de la requête.
Par défaut, le router va appliquer la règle empirique :

```
http://your.app/controller/method/args
```

Si l'url est incomplète, par défaut le *contrôleur* sera `app\controllers\Front` et la méthode `index` :

```
http://your.app/front/hello  => app\controllers\Front::hello
http://your.app/front        => app\controllers\Front::index
http://your.app/             => app\controllers\Front::index
```

La vue appelée se base sur le même chemin que le couple contrôleur/méthode :

```
http://your.app/front/hello  => views/front/hello.php
```

Tous les segments présent en plus deviennent des arguments de la méthode, ainsi pour `http://your.app/front/hello/yeti` :

```php
namepsace app\controllers;

class Front
{
    public function hello($name) // $name = 'yeti'
    {
        return ['name' => $name];
    }
}
```

Exemple :

```
url :       http://your.app/user/show/5
action :    app\controllers\User::show(5);
vue :       views/user/show.php
```

### Les presets

Le routeur embarque 3 configurations empiriques en fonction du type d'url souhaité :

Url courte : `http://your.app/method`
```php
// app\controllers\Front::(method)
$router = wicked\core\router\ActionRouter();
```

Url moyenne : `http://your.app/controller/method` (router par défaut)
```php
// app\controllers\(controller)::(method)
$router = wicked\core\router\ControllerRouter();
```

Url longue : `http://your.app/bundle/controller/method`
```php
// app\bundles\(bundle)\controllers\(controller)::(method)
$router = wicked\core\router\BundleRouter();
```

Il suffit alors d'injecter le router dans l'application :

```php
$app = new wicked\App($router);
```

### Vos règles

Malgré la présence de ces 3 presets, il est possible d'éditer vos propres règles de routing de cette manières :

```php
$router = new wicked\core\Router();
$router->set(
    ['(:controller)/(:action)', '(:controller)', ''],   // liste des pattern possibles avec placeholder
    'app/controllers/(:Controller)::(:action)',         // utilisation des placeholders pour définir l'action
    'views/(:controller)/(:action).php',                // utilisation des placeholders pour définir la vue
    ['controller' => 'Front', 'action' => 'index']       // valeurs par défaut si non spécifiées dans l'url
);
```

Chaque *placeholder* type `(:key)` est capturé dans l'url et transmis afin de construire l'action et la vue.
De plus, chaque clé dispose de sa copie avec la première lettre en majuscule, ex:  `app/controllers/(:Controller)::(:action)` afin de concorder avec les noms de classes.

Il est également possible de définir des règles fixes :

```php
$router = new wicked\core\Router();
$router->set('user/(+id)/edit', 'app/controllers/User::edit');
```

Dans ce cas, grâce au placeholder type `(+arg)`, l'argument est forcé et sera passé à l'action avant les autres arguments possibles.

Il est tout à fait possible de définir plusieurs règles, le routeur s'arretera sur la première qui match la requête.


### Combinaison

Dans le cas où plusieurs règles sont à définir, et dans un soucis de lisibilité, il est possible de fragmenter le routeur de manière logique,
par exemple, le cas d'une application avec front et backoffice :

```php
// back end : http://your.app/admin/controller/method
$back = wicked\core\router\ControllerRouter(['base' => 'admin', 'bundle' => 'back']);

// front end : http://your.app/controller/method
$front = wicked\core\router\ControllerRouter(['bundle' => 'front']);

// création du router principal
$router = new wicked\core\Router();
$router->bind($back);
$router->bind($front);

$app = new wicked\App($router);
```

Ainsi, toutes les url commencant par `/admin` pointeront sur le bundle `app\bundles\back`, les autres sur le bundle `app\bundles\front`.

### La route actuelle

Si vous souhaitez accéder aux informations de la route courante, demandez au Mog !

```php
$mog->route;
$mog->route->action;    // l'action en cours
$mog->route->view;      // la vue appelée
$mog->route->data;      // les placeholders de l'url
```


## Les événements

L'application déclenche des événements lors des étapes clés du processus.
En exemple, l'exception `404` soulevée par **Wicked** est redirigée sur le flux des événements afin que l'utilisateur puisse agir en conséquence :

```php
$app = new wicked\App();

$app->on(404, function($app, $message) {
    die('Good day to die ! Because : ' . $message);
});

$app->run();
```

Ou encore passer des variables communes à toutes les vues :

```php
$app = new wicked\App();

$app->on('render', function($app, $view) {
    $view->set('route', $app->mog->route);
});

$app->run();
```

*NB : Pour chaque événement, le premier paramètre sera toujours l'application courante.*

Voici la liste des événements :
- `before.run`
- `after.run`
- `before.route`
- `after.route`
- `before.build`
- `build`
- `after.build`
- `before.render`
- `render`
- `after.render`
- `set.service`
- `get.service`


Les exceptions sont interprétées en tant que qu'évènements, ainsi vous pouvez générer vos propres exceptions :

```php
oops('Yo Satan !', 666);
```

Et récupérer avec :

```php
$app->on(666, function($app, $message) { ... });
```


## Outils

Wicked embarque un certains nombres d'outils utiles dans les cas d'applications les plus fréquents **(attention, ces outils sont encore en version beta)** :

**Lipsum** : Génère des faux textes, titres, paragraphes, lignes, emails en *Lorem Ipsum*

**Mail** : Classe d'envoi de mail simple et efficace

**FTP** : Un connecteur FTP

**Rest** : Une librairie d'appel Restfull

**URL** : Un parseur est buildeur d'URL

**String** : Un ensemble de fonction de transformation de string

**Date** : Déclinaison française de la gestion de date

**Singleton** : Un trait très utile...

**Annotation** : Parser d'annotation pour classe, propriété ou méthode

**DataArray** : Transformer vos objets en array améliorés

**CRUD** : Automatisation d'action CRUD


## Next : almost finished...


