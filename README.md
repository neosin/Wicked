# WickedFramework

Wicked est un petit framework en PHP5.4 artisanal, rapide et sympa ne gardant que l'essentiel pour les projets modestes :)

[Voir la documentation complète.](https://github.com/WickedYeti/Wicked/blob/master/DOCS.md)

## Quickstart : TODO List

Nous allons prendre l'exemple d'une **TODO list** avec gestion des utilisateurs, un cas simple mais qui permettra de couvrir l'utilisation de **Wicked**.

### Organisation

Il est conseillé d'utiliser une organisation de projet MVP classique, cependant rien n'est imposé et vous pouvez suivre votre propre organisation.

```php
/app
    /controllers
        Front.php
        User.php
        Note.php
    /models
        User.php
        Note.php
    /public
        /css
            main.css
    /views
        /user
            login.php
        /note
            add.php
            index.php
    index.php
    .htaccess
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

## Les modèles

Dans notre application, nous allons gérer 2 types d'entités : `User` et `Note`, chaque `Note` ayant un `User`.

```php
# models/User.php
namespace app\models;

class User
{

    public $id;

    public $username;

    public $password;

}
```

```php
# models/Note.php
namespace app\models;

class Note
{

    public $id;

    public $user;

    public $content;

}
```

## La base de données

**Syn** est un micro-orm intégré à **Wicked**, il nous permettra d'accéder à notre base MySQL ([voir doc](https://github.com/WickedYeti/Syn)) :

```php
# index.php
require '../wicked/bootstrap.php';

$app = new wicked\App();

$app['syn'] = syn\MySQL('todolist');
$app['syn']->user->model('app\models\User');    // mapping, voir doc
$app['syn']->note->model('app\models\Note');    // mapping, voir doc

$app->run();
```

## Définir vos actions

Le coeur d'une application sont les actions et services, la `business layer` . Dans une architecture MVP, les actions sont matérialisées par des *contrôleurs* et des *méthodes*.
N'oubliez pas, pour garder une architecture saine : à chaque action, sa méthode.

### Le point d'entré

Par défaut, le point d'entré de **Wicked** est le contrôleur `Front`, il permet de récupérer la première requête :

```php
# controllers/User.php
namespace app\controllers;

class Front
{

    /**
     * Homepage
     */
    public function index()
    {
        go('/user/login');    // go directly to login
    }

}
```

### L'utilisateur

Dans un premier temps, nous allons définir l'accès utilisateur : le login et le logout

```php
# controllers/User.php
namespace app\controllers;

class User
{

    /**
     * Login user with password
     */
    public function login()
    {
        // form submitted
        if(post()){

            $password = sha1(post('password'));     // hash password
            $users = syn()->user->find([            // find potential users
                'username' => post('username'),
                'password' => $password
            ]);

            if($users)
                auth($users[0]);                    // user found : login
            else
                flash('error', 'Unknown username or password.');
        }
    }

    /**
     * Logout current user
     */
    public function logout()
    {
        auth(false);                                // logout
        go('/user/login');
    }

}
```

### Les notes

Ensuite, nous avons besoin de définir 3 actions concernant les notes : la liste, l'ajout et la suppression
sachant que l'utilisateur doit être obligatoirement loggé (géré par l'annotation `@rank 1`).

```php
# controllers/User.php
namespace app\controllers;

use app\models\Note as NoteModel;

/**
 * @rank 1
 */
class Note
{

    /**
     * Listing
     */
    public function index()
    {
        $notes = syn()->note->find(['user' => user()->id]);   // find all notes that belong to current user
        return ['notes' => $notes];     // return list
    }

    /**
     * Add note
     */
    public function add()
    {
        if(post()){
            $note = new NoteModel();    // create new note
            hydrate($note, post());     // hydrate note data with post
            syn()->note->save($note);   // save note
            go('/');                    // redirect to index
        }
    }

    /**
     * Delete note
     */
    public function delete($id)
    {
        $note = syn()->note->find($id); // retrieve note
        syn()->note->delete($note);     // delete it
        go('/');                        // redirect to index
    }

}
```

## Créer vos vues

Nos actions, correctement préparées, peuvent éventuellement renvoyer des données, il est temps de les affichers dans nos vues.

### Le layout

Tout d'abords, le layout, cadre commun à toutes les vues :

```php
# views/layout.php
<!doctype>
<html>
<head>
    <title>My todo list</title>
    <?= self:meta(); ?>         <!-- add meta charset and viewport -->
    <?= self:css('main'); ?>    <!-- load public/css/main.css -->
</head>
<body>
    <?= self::content(); ?>     <!-- where the view takes place -->
</body>
</html>
```

La vue sera automatique chargée dans `self::content()`.

### Les vues

Chaque vue doit définir le layout qu'elle veut utiliser, dans notre cas, nous n'avons qu'un seul layout.

```php
# views/user/login.php
<?php self::layout('views/layout.php'); ?>  <!-- define the layout -->

<?= flash('error'); ?>

<form method="post">
    <input type="text" name="username" placeholder="Username" />
    <input type="password" name="password" placeholder="Password" />
    <input type="submit" />
</form>
```

```php
# views/note/index.php
<?php self::layout('views/layout.php'); ?>  <!-- define the layout -->

<ul>
    <?php foreach($notes as $note): ?>
    <li><?= $note->content ?> <a href="<?= url('/note/delete/' . $note->id) ?>">delete</a></li>
    <?php endforeach; ?>
</ul>

<a href="<?= url('/note/add') ?>">New note</a>
```

```php
# views/note/add.php
<?php self::layout('views/layout.php'); ?>  <!-- define the layout -->

<form method="post">
    <input type="text" name="content" placeholder="New note" />
    <input type="submit" />
</form>
```

Note : `url()` permet de générer une url complète à partir de la base de votre application : `url('some/where');` devient `http://your.app/some/where`.


## Routeur

Nos actions et nos vues sont prêtes ! Il est temps de comprendre comment le router va définir l'action à executer en fonction de la requête.
Par défaut, le router va appliquer la règle empirique :

```
http://your.app/controller/method/args
```

Si l'url est incomplète, par défaut le *contrôleur* sera `app\controllers\Front` et la méthode `index` :

```
http://your.app/note/add    => app\controllers\Note::add
http://your.app/user        => app\controllers\User::index
http://your.app/            => app\controllers\Front::index
```

La vue appelée se base sur le même chemin que le couple contrôleur/méthode :

```
http://your.app/note/add    => views/note/add.php
```

Tous les segments présent en plus deviennent des arguments de la méthode, ainsi pour `http://your.app/note/delete/5` :

```php
namepsace app\controllers;

class Note
{
    public function delete($id) // $id = 5
    {
        // ...
    }
}
```


## Plop !

C'est fini ! Votre application est en état de marche ;)
Vous pouvez désormais consulter la [documentation](https://github.com/WickedYeti/Wicked/blob/master/DOCS.md), il vous reste encore plein de fonctionnalités à découvrir !