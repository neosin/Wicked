<?php self::layout('{:layout:}'); ?>

<div id="list">

    <?php foreach($listing as $entity): ?>
    <div class="item"><?= $entity->id; ?></div>
    <?php endforeach; ?>

</div>