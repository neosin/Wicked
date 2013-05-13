<?php self::layout('views/layout.php'); ?>

<form action="" method="post">
    <label for="content">Edit : </label>
    <input type="text" name="content" placeholder="Task name" value="<?= $task->content ?>" />
    <input type="submit" value="Save" />
</form>