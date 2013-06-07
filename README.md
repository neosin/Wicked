# WickedFramework

Wicked est un petit framework artisanal, rapide et sympa ne gardant que l'essentiel pour les projets modestes :)


## Quickstart

Nous allons utiliser l'exemple du célèbre HelloWorld afin de couvrir l'utilisation *basique* de **Wicked**.

Le fichier `bootstrap.php` permet d'inclure les librairies et mécanismes de **Wicked** dans votre projet et ainsi d'appeler les classes nécessaires à votre application :

```php
require '../../wicked/bootstrap.php';

$app = new wicked\App();
$app->run();
```

Toutes vos classes seront reconnues par **Wicked** grâce au préfixe de namespace `app\`. Par exemple pour votre contrôleur par défaut :

```php
namespace app\controllers;

class Front {}
```

Afin de garder une simplicité optimale dans le développement de votre application, aucun *contrôleur* ou *modèle* ne devra étendre de quoi que ce soit,
vous laissant ainsi plus de liberté dans la conception de vos classes.

## Routeur

**Wicked** initialise un routeur classique avec la règle suivante :

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
http://your.app/front/hello  => app\views\front\hello.php
```

Si vous souhaitez définir vos propres règles, voir la section approfondie du *Router*.



## Requête

Le framework utilise la librairie **Mog** pour la gestion de la requête et des données utilisateur, [voir la doc](https://github.com/WickedYeti/Mog).
Cependant, un lot d'helpers vous permet d'accéder plus facilement aux données globales.

Accès à la session :

```php
session('foo', 'bar');      // définit une donnée
$foo = session('foo');      // accède à une donnée
session()->clear('foo');    // efface une donnée
session()->clear();         // efface entièrement la session
```

Gestion de l'utilisateur :

```php
auth($user, 9);         // log un objet utilisateur avec un rang
auth(false);            // déconnecte l'utilisateur

user();                 // accède à l'entité définit par login()
$rank = user('rank');   // récupère le rang
user('rank', 5);        // re-définit le rang
```

Messages flash (attention, la récupération consomme le message, il ne sera plus disponible) :

```php
flash('success', 'Yeah !');     // ajouter un message
$message = flash('success');    // consomme un message
```

Données formulaires :

```php
$username = post('username');   // récupère une champ du formulaire
```

Rediriger :

```php
go('user/list); // redirige vers http://your.app/user/list
```

Accéder au Mog et à Syn :

```php
mog();
syn();
```


## Action

Trois comportements spécifiques sont à connaitre quant aux contrôleurs : les valeurs de retour, l'interception de vue et l'auto-wiring.


### Valeurs de retour

Dans le cadre du pattern MVP, **Wicked** prendra les valeurs de retour de la méthode appelée afin de les transmettre à la vue indiquée par le routeur,
sans aucun appel d'une quelconque fonction de votre part. Ainsi :

```php
namespace app\controllers;

class Front
{
    public function hello()
    {
        return ['name' => 'world'];
    }
}
```

La variable `$name` sera accessible dans la vue et contiendra la valeur `"world"`.


### Interception de vue

Il est possible de changer de vue à la volée grâce à l'annotation `@view` :

```php
namespace app\controllers;

class Front
{

    /** @view path/to/another/view.php */
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
    /** @wire wicked.bear */
    public $bear;
}
```


## Vue

La vue est le rendu graphique de votre application. Par défaut, **Wicked** propose un moteur de rendu en HTML.
Par exemple pour l'action `Front::hello` :

```php
<h1>Hello <?= $name ?> !</h1>
```


### Layout

Il arrive très frequemment que plusieurs vues utilisent un *layout* commun afin de ne pas dupliquer le code.
Dans ce cas, il est nécessaire d'indiquer à la vue quel *layout* utiliser :

```php
<?php self::layout('views/layout.php'); ?>

<h1>Hello <?= $name ?> !</h1>
```

Et également au layout, où afficher la vue :

```php
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


### Assets

3 fonctions sont disponibles pour la gestion des fichiers externes :

```php
self::css('layout');                    // pour /public/css/layout.css
self::js('jquery', 'main');             // pour /public/js/jquery.js et /public/js/main.js
self::asset('img/background.png');      // pour /public/img/background.png
```


# Fonctionnement avancé

## Router

### Les presets

Le routeur embarque 3 configurations empiriques en fonction du type d'url souhaité :

Url courte : `http://your.app/method`
```php
// app\controllers\Front::(method)
$router = wicked\core\router\ActionRouter();
```

Url moyenne : `http://your.app/controller/method`
```php
// app\controllers\(controller)::(method)
$router = wicked\core\router\ControllerRouter();
```

Url longue : `http://your.app/bundle/controller/method`
```php
// app\bundles\(bundle)\controllers\(controller)::(method)
$router = wicked\core\router\BundleRouter();
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

Il est également possible de définir une seule règle fixe :

```php
$router = new wicked\core\Router();
$router->set('user/(+id)/edit', 'app/controllers/User::edit');
```

Dans ce cas, grâce au placeholder type `(+arg)`, l'argument est forcé et sera passé à l'action avant les autres arguments possibles.

Il suffit alors d'injecter le router dans l'application :

```php
$app = new wicked\App($router);
```

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


## Authentification

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
Dans le cas contraire, un événement `403` est déclenché.

NB : par défaut, le rang défini par la méthode sera prioritaire sur le contrôleur.


## Les événements

L'application déclenche des événements lors des étapes clés du processus.
En exemple, l'exception `404` soulevée par **Wicked** est redirigée sur le flux des évévenements afin que l'utilisateur puisse agir en conséquence :

```php
$app = new wicked\App();

$app->on('404', function($app, $message) {
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


Les exceptions sont interprétées en tant que qu'évènement, ainsi vous pouvez générer vos propres exceptions :

```php
opps('This is my awesome message !', 999);
```

Et récupérer avec :

```php
$app->on('999', function($app, $message) { ... });
```


## Outils

Wicked embarque un certains nombres d'outils utiles dans les cas d'applications les plus fréquents :

- Lipsum
- Mail
- FTP
- URL
- String
- Date
- Singleton
- Annotation
- ...

Parmis ces outils, l'outils CRUD, vous permettra d'automatiser les tâches chronophages de lecture, écrite, mise à jours et suppression d'une entité :

**Attention, cet outils est encore en version beta**

```php
namespace app\controllers;

use wicked\tools\actions\CRUD;

class Item
{

    use \wicked\tools\wire\All;

    public $crud;

    public function __construct()
    {
        $this->crud = new CRUD('item', 'app\models\Item');
    }

}
```

Une fois l'outils chargé, il vous propose 5 fonctions à placer dans vos méthodes :

```php
public function index()
{
    $items = $this->crud->read();
    return ['items' => $items];
}

public function show($id)
{
    $item = $this->crud->read($id);
    return ['item' => $item];
}

public function create()
{
    if(post()) {
        $item = $this->crud->create($post);
        go('item/edit/' . $item->id);
    }
}

public function edit($id)
{
    $item = $this->crud->read($id);

    if(post())
        $item = $this->crud->update($id, $post);

    return ['item' => $item];
}

public function delete($id)
{
    $this->crud->delete($id);
}
```

Toutes ces fonctions s'occupent notamment de vérifier si l'entité existe, si elle est valide
et se charge de l'opération en base de donnée via **Syn**, elle renvoient false si l'opération à échouée.

## next : coming soon...