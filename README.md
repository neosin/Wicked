# WickedFramework

Wicked est un petit framework artisanal, rapide et sympa ne gardant que l'essentiel pour les projets modestes :)


## Installation

Copier le dossier **wicked** à côté de votre projet suivant cette configuration recommandée :

```
/yourapp
/wicked
```


## Quickstart : Hello world !

Nous allons utiliser l'exemple du célèbre HelloWorld afin de couvrir l'utilisation *basique* de **Wicked**

### Initialisation

Le fichier *bootstrap.php* permet d'inclure les librairies et mécanismes de **Wicked** dans votre projet et ainsi d'appeler les classes nécessaires à votre application :

```php
require '../../wicked/bootstrap.php';

$app = new wicked\App();
$app->run();
```

Toutes vos classes seront reconnues par **Wicked** grâce au préfixe de namespace **app*. Par exemple pour votre contrôleur par défaut :

```php
namespace app\controller;

class Home {}
```

Afin de garder une simplicité optimale dans le développement de votre application, aucun controlleur ou modèle ne devra étendre de quoi que ce soit,
vous laissant ainsi plus de liberté dans la conception de vos classes.

### Routeur

**Wicked** initialise un routeur classique avec la règle suivante :

```
http://your.app/controller/method/args
```

Si l'url est incomplète, par défaut le *contrôleur* sera *app\controller\Home* et la méthode *index* :

```
http://your.app/home/hello  => app\controller\Home::hello
http://your.app/            => app\controller\Home::index
```

La vue appelée se base sur le même chemin que le couple contrôleur/méthode :

```
http://your.app/home/hello     => app\views\home\hello.php
```

Si vous souhaitez définir vos propres règles, voir la section approfondie du *Router*.


### Action

Deux comportements spécifiques sont à connaitre quant aux contrôleurs : les valeurs de retour et l'auto-wiring.


#### Valeurs de retour

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

La variable *$name* sera accessible dans la vue et contiendra la valeur *"world"*.


#### Auto-wire

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

Nb : vous pouvez définir vos propres objets pouvant être accéder par l'auto-wire dans votre *index.php* grâce à la fonction :

```php
$app->set('myvar', $myobj); // accessible dans la PHPDoc par : @context wicked.myvar
```


### Vue

La vue est le rendu graphique de votre application. Par défaut, **Wicked** propose un moteur de rendu en HTML.
Par exemple pour l'action *Home::hello* :

```php
<h1>Hello <?= $name ?> !</h1>
```


#### Layout

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


#### Slot / Hook

Le *layout* a pour vocation d'être fixe et invariant, cependant certains informations nécessite d'être dynamique (le nom de l'utilisateur en cours par exemple)
et peuvent être changées suivant l'action. Afin de déterminer ces zones, le *layout* à besoin de connaitre l'emplacement :

```php
<!doctype html>
<html>
    <head>
        <title>My first WickedApp !</title>
        <meta charset="utf-8">
    </head>
    <body>
        <header><?= self::hook('username') ?></header>
        <?= self::content(); ?>
    </body>
</html>
```

Ainsi que la vue doit envoyer le contenu dynamique :

```php
<?php self::layout('views/layout.php'); ?>
<?php self::slot('username', 'WickedYeti'); ?>
```