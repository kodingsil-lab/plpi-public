<?php

$pager->setSurroundCount(2);
?>
<?php if ($pager->getPageCount() > 1): ?>
<nav class="admin-pagination" aria-label="Navigasi halaman tabel">
    <?php if ($pager->hasPrevious()): ?>
        <a href="<?= $pager->getFirst() ?>" aria-label="Halaman pertama">&laquo;</a>
        <a href="<?= $pager->getPrevious() ?>" aria-label="Sebelumnya">&lsaquo;</a>
    <?php else: ?>
        <span class="disabled">&laquo;</span>
        <span class="disabled">&lsaquo;</span>
    <?php endif; ?>

    <?php foreach ($pager->links() as $link): ?>
        <?php if ($link['active']): ?>
            <span class="current" aria-current="page"><?= $link['title'] ?></span>
        <?php else: ?>
            <a href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($pager->hasNext()): ?>
        <a href="<?= $pager->getNext() ?>" aria-label="Berikutnya">&rsaquo;</a>
        <a href="<?= $pager->getLast() ?>" aria-label="Halaman terakhir">&raquo;</a>
    <?php else: ?>
        <span class="disabled">&rsaquo;</span>
        <span class="disabled">&raquo;</span>
    <?php endif; ?>
</nav>
<?php endif; ?>
