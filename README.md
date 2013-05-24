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
namespace app\controller;

class Home {}
```

Afin de garder une simplicité optimale dans le développement de votre application, aucun controlleur ou modèle ne devra étendre de quoi que ce soit,
vous laissant ainsi plus de liberté dans la conception de vos classes.

## Routeur

**Wicked** initialise un routeur classique avec la règle suivante :

```
http://your.app/controller/method/args
```

Si l'url est incomplète, par défaut le *contrôleur* sera `app\controller\Home` et la méthode `index` :

```
http://your.app/home/hello  => app\controller\Home::hello
http://your.app/home        => app\controller\Home::index
http://your.app/            => app\controller\Home::index
```

La vue appelée se base sur le même chemin que le couple contrôleur/méthode :

```
http://your.app/home/hello     => app\views\home\hello.php
```

Si vous souhaitez définir vos propres règles, voir la section approfondie du *Router*.



## Requête

Le framework utilise la librairie **Mog** pour la gestion de la requête et des données utilisateur.


## Action

Deux comportements spécifiques sont à connaitre quant aux contrôleurs : les valeurs de retour et l'auto-wiring.


### Valeurs de retour

Dans le cadre du pattern MVP, **Wicked** prendra les valeurs de retour de la méthode appelée afin de les transmettre à la vue indiquée par le routeur,
sans aucun appel d'une quelconque fonction de votre part. Ainsi :

```php
namespace app\controller;

class Home
{
    public function hello()
    {
        return ['name' => 'world'];
    }
}
```

La variable `$name` sera accessible dans la vue et contiendra la valeur `"world"`.


### Auto-wire

Similaire aux EJB de Java, l'auto-wiring permet de lier automatiquement un objet à un attribut d'une classe grâce à la PHPDoc de ce dernier :

```php
namespace app\controller;

class Home
{
    /** @context wicked.mog */
    public $mog;
}
```

Par ce mécanisme, **Wicked** donnera automatiquement son **Mog** au contrôleur afin que l'utilisateur puisse accéder aux fonctionnalités de ce dernier.

Nb : vous pouvez définir vos propres objets pouvant être accédé par l'auto-wire dans votre `index.php` grâce à la fonction :

```php
$app['myvar'] = $myobj; // accessible dans la PHPDoc par : @context wicked.myvar
```

Le framework vous propose 3 traits vous permettant de lier le Mog, Syn ou les 2 en même temps :

```php
namespace app\controller;

class Home
{
    use \wicked\wire\Mog; // ou Syn, ou All
}
```

## Vue

La vue est le rendu graphique de votre application. Par défaut, **Wicked** propose un moteur de rendu en HTML.
Par exemple pour l'action `Home::hello` :

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
// app\controllers\Home::(method)
$router = wicked\core\Router::simple();
```

Url moyenne : `http://your.app/controller/method`
```php
// app\controllers\(controller)::(method)
$router = wicked\core\Router::classic();
```

Url longue : `http://your.app/bundle/controller/method`
```php
// app\bundles\(bundle)\controllers\(controller)::(method)
$router = wicked\core\Router::bundle();
```

### Vos règles

Malgré la présence de ces 3 presets, il est possible d'éditer vos propres règles de routing de cette manières :

```php
$router = new wicked\core\Router();
$router->set(
    ['(:controller)/(:action)', '(:controller)', ''],   // liste des pattern possibles avec placeholder
    'app/controllers/(:controller)::(:action)',         // utilisation des placeholders pour définir l'action
    'views/(:controller)/(:action).php',                // utilisation des placeholders pour définir la vue
    ['controller' => 'Home', 'action' => 'index']       // valeurs par défaut si non spécifiées dans l'url
);
```

Il suffit alors de passer le router à l'application afin de remplacer l'existant :

```php
$app = new wicked\App($router);
```

Il est tout à fait possible de définir plusieurs règles, le routeur s'arretera sur la première qui match l'url.


### Combinaison

Dans le cas où plusieurs règles sont à définir, et dans un soucis de lisibilité, il est possible de fragmenter le routeur de manière logique,
par exemple, le cas d'une application avec front et backoffice :

```php
// router type bundle pointant sur 'admin'
$back = wicked\core\Router::bundle(['base' => 'admin', 'bundle' => 'back']);

// router type classique pointant sur le bundle 'front'
$front = wicked\core\Router::classic(['bundle' => 'front']);

// création du router principale
$router = new wicked\core\Router();
$router->bind($back);
$router->bind($front);

$app = new wicked\App($router);
```

Ainsi, toutes les url commencant par `/admin` pointeront sur le bundle `app\bundles\back`, les autres sur le bundle `app\bundles\front`.


## Authentification

Dans le cas où certaines actions sont strictement protégées pour un certain rang, la gestion se fait par annotation soit sur le contrôleur en entier :

```php
namespace app\controllers;

/**
 * @rank 8
 */
class Home
{
    ...
}
```

Soit directement sur la méthode :

```php
namespace app\controllers;

class Home
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

Cette annotation sera comparée à `$mog->user->rank` afin de déterminer si l'utilisateur a le droit ou non d'accéder à cette action (supérieur ou égal).
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


## next : coming soon...