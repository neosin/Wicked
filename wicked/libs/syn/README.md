# Syn

Syn est un ORM ultra simplifié conçu pour le développement rapide, fun et efficace.
Il est basé sur la couche PDO de PHP et comprend les mêmes driver de base de données.

## Installation

Copier le dossier **syn** dans votre projet et enregistrer le namespace **syn** de la librairie.

### Autoloader

Dans le cas ou vous ne disposez pas d'autoloader, voici un exemple basique vous permettant de tester **Syn** (à placer à la racine de votre projet) :

```php
function __autoload($classname)
{
    $root = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    require_once $root . str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php';
}
```

## Instance

L'ORM prend comme paramètre de constructeur l'object PDO préalablement créé :

```php
$pdo = new \PDO();
$syn = new syn\core\ORM($pdo);
```

Pour les 2 cas les plus récurrents MySQL et SQLite, Syn propose un raccourci avec une configuration par défaut sur le localhost :

```php
$syn = new syn\MySQL('dbname'); // localhost
```

ou

```php
$syn = new syn\MySQL('dbname', ['host', 'username', 'password']); // distant
```

## Structure

Syn permet de synchroniser directement le schema de la base de données en fonction des modèles présent dans votre
projet. Ces fonctionnalités ne sont pas obligatoires ! Syn utilise la classe par défaut de PHP **stdClass** dans le
cas où aucun modèle n'est renseigné.

### Création d'un modèle

```php
namespace app\models;

class User
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $age;
}
```

### Mapping

```php
$syn->user->model('app/models/User');
```

Le mapping va permettre à Syn de caster les résultats de vos requêtes directement dans vos modèles. L'attribute **id** sera toujours considéré comme la clé primaire.

### Synchronisation

L'outil de synchronisation se base sur les meta-commentaires des attributs pour déterminer le bon type de donnée, pour...

```php
/** @var string */
public $name;
```

...l'outil déterminera un type **varchar(255)** pour le champ **name**.

```php
$syn->synchronize();
```

La table **user** sera alors créée ou mise à jours avec les champs **id(int, primary, auto-i)**, **name(varchar)** et **age(int)**.

**@var** possible :

```php
/**
 * @var int         => int
 * @var string      => varchar
 * @var string text => text
 * @var string date => datetime
 */
```

## Utilisation

L'utilisation et le requêtage se fait de la plus naturelle façon possible :

### Créer ou mettre à jours une entité

```php
$user = new User();
$user->name = 'Yéti';

$syn->user->save($user);
```

### Trouver tous les enregistrements d'une entité

```php
$users = $syn->user->find();
```

Si le modèle **user** n'a pas été mappé précédemment, la fonction **find** retournera une(des) instance(s) de **stdClass**

### Trouver les enregistrements d'une entité suivant une condition

```php
$users = $syn->user->find(['age' => 24]);
```

### Trouver <strong>une</strong> entité par ID

```php
$user = $syn->user->find(1);
```

### Trouver <strong>une</strong> entité au hasard

```php
$user = $syn->user->random();
```

### Supprimer une entité

```php
$syn->user->delete($user);
```

### Requête SQL autres

```php
$result = $syn->query('select * from `user` where `age` >= 18');
$result = $syn->query('select * from `user` where `age` >= 18', 'user'); // avec cast des résultats
```

## Filtres

Syn propose la création de filtre sur un modèle donné, permettant d'agir sur le formattage ou la validation des données :

```php
$syn->user->filter('before.save', function($user){
    $user->name = ucfirst($user->name);
    return $user;
});

$user = new User();
$user->name = 'yéti';

$syn->user->save($user);

echo $user->name; // print "Yéti"
```

Si le filtre renvoi **false** au lieu de l'entité, l'action est annulée.

Voici la liste des filtres disponibles :

 * before.save
 * after.save
 * before.delete
 * after.delete

## Backup

Syn permet de sauvegarder l'ensemble de la structure et des données dans un fichier SQL via la méthode :

```php
$syn->backup('mybackup.sql');
```

## À venir

 * Gestion des relations
 * Commit / Rollback

## Feedback

Toutes suggestions de correction et/ou d'amélioration sont les bienvenues : **aymeric.assier@gmail.com**

Enjoy ;)