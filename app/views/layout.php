<!doctype>
<html>
<head>
    <title>My WickedApp</title>
    <?= self::meta(); ?>
    <?= self::css('layout', 'main'); ?>
    <?= self::js('jquery-2.0.2.min', 'main'); ?>
</head>
<body>

    <header>My WickedApp</header>

    <div id="content">
        <?= self::content(); ?>
    </div>

</body>
</html>