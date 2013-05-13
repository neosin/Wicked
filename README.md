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

Le fichier *bootstrap.php* permet d'inclure les librairies et mécanismes de **Wicked** dans votre projet et ainsi d'appeler les classes nécessaire à votre application :

```php
require '../../wicked/bootstrap.php';

$app = new wicked\App();
$app->run();
```

Toutes vos classes seront reconnues par **Wicked** grâce au préfix de namespace **app*. Par exemple pour votre controller par défaut :

```php
namespace app\controllers;

class Home {}
```

Afin de garder une simplicité optimale dans le développement de votre application, aucun controlleur ou modèle ne devra étendre de quelque chose,
vous laissant ainsi plus de liberté dans la conception de vos classes.

### Routeur

**Wicked** initialise un routeur classique avec la règles suivante :

```
http://your.app/controller/method/args
```

Si l'url est incomplète, par défaut le *controller* sera *app\controllers\Home* et la méthode *index* :

```
http://your.app/home/hello  => app\controllers\Home::hello
http://your.app/            => app\controllers\Home::index
```

La vue appelée se base sur le même chemin que le controller/méthode :

```
http://your.app/home/hello     => app\views\home\hello.php
```

Si vous souhaitez définir vos propres règles, voir la section approfondie du *Router*.


### Action

Deux comportements spécifiques sont à connaitre quant aux controllers : les valeurs de retour et l'auto-wiring.


#### Valeurs de retour

Dans le cas du pattern MVC, **Wicked** prendra les valeurs de retour de la méthode appelée afin de les transmettre à la vue indiquée par le routeur,
sans aucun aucun appel d'une quelconque fonctions de votre part. Ainsi :

```php
namespace app\controllers;

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

Similaire aux EJB de Java, l'auto-wiring permet de lier automatiquement un object à un attribut d'une classe grâce à la PHPDoc de ce dernier :

```php
namespace app\controllers;

class Home
{
    /** @context wicked.mog */
    public $mog;
}
```

Par ce mécanisme, **Wicked** donnera automatiquement son **Mog** au controller afin que l'utilisateur puisse accéder à la requête et à divers fonctions utilise propre au framework.

Nb : vous pouvez définir vos propres objets pouvant être accéder par l'auto-wire dans votre *index.php* grâce à la fonction :

```php
$app->set('myvar', $myobj); // accessible dans la PHPDoc par : @context wicked.myvar
```


### Vue

En cours d'écriture...