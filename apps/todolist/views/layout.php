<!doctype html>
<html>
    <head>
        <title>Todo List</title>
        <?= self::meta() ?>
        <?= self::css('main') ?>
    </head>
    <body>

    <div id="container">

        <header>
            <h1>TODO List</h1>
        </header>

        <div id="content">
            <?= self::content(); ?>
        </div>

    </div>

    </body>
</html>
