<!doctype>
<html>
<head>
    <title>My Layout</title>
    <?= self::meta(); ?>
    <?= self::css('layout', 'main'); ?>
</head>
<body>

    <header>My Wicked App</header>

    <div id="content">
        <?= self::content(); ?>
    </div>

</body>
</html>