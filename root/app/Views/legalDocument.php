<?php
/**
 * The marketplace's Terms of Service or Privacy Policy; see App\Controllers\Home::legalPage()
 * @var string      $slug     terms-of-service or privacy-policy
 * @var string      $locale
 * @var array|null  $document The version shown: version_label, effective_from, body; null when the API could not be reached
 * @var list<array> $sections Its text cut into cards: heading (null for the text before the first one) and body (Markdown)
 * @var list<array> $versions Every version in effect so far, newest first
 * @var string      $www_url  The OtterNova site's copy, linked to when the API could not be reached
 */
$this->extend('_layout_otternova');
$title    = lang('System.page.' . $slug);
$pageUrl  = base_url($slug);
$isLatest = $document === null || $document['version_label'] === ($versions[0]['version_label'] ?? null);
$json     = static fn (string $text): string => json_encode($text, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
$number   = 0;
?>
<?= $this->section('content') ?>
<main class="main">
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= esc($title) ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="<?= esc(base_url(), 'attr') ?>"><?= lang('System.page.home') ?></a></li>
                    <li class="current"><?= esc($title) ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <section id="<?= esc($slug, 'attr') ?>" class="terms-of-service section">
        <div class="container">
            <?php if ($document === null): ?>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="alert alert-warning" role="alert">
                            <?= lang('Legal.unavailable') ?> <a href="<?= esc($www_url, 'attr') ?>" target="_blank" rel="noopener"><?= lang('Legal.read-on-otternova') ?></a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row justify-content-center">
                    <div class="col-lg-7 text-center">
                        <div class="status-badge" data-legal-version="<?= esc($document['version_label'], 'attr') ?>">
                            <i class="bi bi-clock-history"></i>
                            <span><?= lang('Legal.version', [esc($document['version_label']), esc(formatDate((string) $document['effective_from'], $locale))]) ?></span>
                        </div>
                        <?php if (! $isLatest): ?>
                            <div class="alert alert-warning mt-3" role="status">
                                <?= lang('Legal.earlier-version') ?> <a href="<?= esc($pageUrl, 'attr') ?>"><?= lang('Legal.read-current') ?></a>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
                <div class="row justify-content-center mt-5">
                    <div class="col-lg-8">
                        <div class="terms-content">
                            <?php foreach ($sections as $i => $section): ?>
                                <article id="block-<?= $i ?>" class="term-card">
                                    <?php if ($section['heading'] !== null): ?>
                                        <div class="term-header">
                                            <span class="term-label"><?= lang('Legal.section', [sprintf('%02d', ++$number)]) ?></span>
                                            <h3><?= esc($section['heading']) ?></h3>
                                        </div>
                                    <?php endif ?>
                                    <div class="term-body" data-markdown>
                                        <script type="application/json"><?= $json($section['body']) ?></script>
                                    </div>
                                </article>
                            <?php endforeach ?>
                        </div>
                        <?php if (count($versions) > 1): ?>
                            <div class="mt-5">
                                <h4><?= lang('Legal.all-versions') ?></h4>
                                <ul class="list-unstyled">
                                    <?php foreach ($versions as $i => $version): ?>
                                        <li>
                                            <?php if ($version['version_label'] === $document['version_label']): ?>
                                                <strong><?= esc($version['version_label']) ?></strong>
                                            <?php else: ?>
                                                <a href="<?= esc($i === 0 ? $pageUrl : $pageUrl . '/v/' . rawurlencode($version['version_label']), 'attr') ?>"><?= esc($version['version_label']) ?></a>
                                            <?php endif ?>
                                            <span class="text-muted small">
                                                &middot; <?= lang('Legal.effective-from', [esc(formatDate((string) $version['effective_from'], $locale))]) ?>
                                                <?php if ($i === 0): ?>&middot; <?= lang('Legal.current') ?><?php endif ?>
                                            </span>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="row justify-content-center mt-5">
                <div class="col-lg-8">
                    <div class="support-banner">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="support-text">
                                    <h4><?= lang('Legal.questions-title') ?></h4>
                                    <p><?= lang('Legal.questions-text') ?></p>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="<?= esc(getenv('LINK_WWW') . 'contact', 'attr') ?>" class="support-btn" target="_blank" rel="noopener"><?= lang('Legal.get-in-touch') ?> <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php if ($sections !== []): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/12.0.2/marked.min.js"></script>
    <script>
        document.querySelectorAll('[data-markdown]').forEach(function (el) {
            el.innerHTML = marked.parse(JSON.parse(el.querySelector('script').textContent));
        });
    </script>
<?php endif ?>
<?= $this->endSection() ?>
