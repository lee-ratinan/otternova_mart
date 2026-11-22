<?php
/**
 * A page's breadcrumbs (Business::breadcrumbs()): Home, Catalog when the business has its own home page, then the
 * page's trail; the last one, the page itself, is not a link
 *
 * @var list<array{name: string, url: string|null}> $breadcrumbs
 */
?>
<nav class="breadcrumbs" aria-label="<?= esc(lang('Business.breadcrumbs'), 'attr') ?>">
    <ol>
        <?php foreach ($breadcrumbs as $i => $crumb): ?>
            <?php if ($i === array_key_last($breadcrumbs) || $crumb['url'] === null): ?>
                <li class="current" aria-current="page"><?= esc($crumb['name']) ?></li>
            <?php else: ?>
                <li><a href="<?= esc($crumb['url'], 'attr') ?>"><?= esc($crumb['name']) ?></a></li>
            <?php endif ?>
        <?php endforeach ?>
    </ol>
</nav>
