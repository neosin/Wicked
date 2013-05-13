<?php self::layout('views/layout.php'); ?>

<ul>
    <?php foreach($list as $task): ?>
    <li><?= $task->content ?> <a href="<?= url('/edit/' . $task->id) ?>">edit</a> <a href="<?= url('/delete/' . $task->id) ?>">delete</a></li>
    <?php endforeach; ?>
    <li><a href="<?= url('/add') ?>">+ add</a></li>
</ul>
