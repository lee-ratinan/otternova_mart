<?php
/**
 * The businesses found by the header's search (Home::searchResults()): a card for each, with its logo (or its
 * initial), name, rating, an 18+ badge when it is for adults only, and its tags, the ones matching the search
 * first; then the pages of results. Without a search it asks for one, and it tells "nothing found" from "the search
 * did not work".
 *
 * @var string     $search_query what was typed
 * @var array|null $results      v1/mart/search: query, businesses, total, page, per_page, pages; null without a search or when it failed
 * @var bool       $failed       the API could not be reached
 * @var callable   $page_url     int => the URL of that page of the results
 */
$this->extend('_layout_otternova');

$businesses = $results['businesses'] ?? [];
$words      = preg_split('/\s+/u', (string) ($results['query'] ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: [];
$fold       = static fn (string $s): string => mb_convert_case($s, MB_CASE_FOLD, 'UTF-8');
// a tag holding a word of the search: shown first, and marked
$matches = static function (string $tag) use ($words, $fold): bool {
    foreach ($words as $word) {
        if (str_contains($fold($tag), $word)) {
            return true;
        }
    }

    return false;
};
$page  = (int) ($results['page'] ?? 1);
$pages = (int) ($results['pages'] ?? 0);
?>
<?= $this->section('content') ?>
    <style>
        .search-results .result-card { display: flex; gap: 16px; height: 100%; padding: 20px; border-radius: 14px; background: var(--surface-color);
            border: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); color: var(--default-color); transition: box-shadow .2s ease, transform .2s ease; }
        .search-results .result-card:hover { box-shadow: 0 10px 28px color-mix(in srgb, var(--default-color), transparent 90%); transform: translateY(-2px); color: var(--default-color); }
        .search-results .result-logo { flex: 0 0 64px; height: 64px; border-radius: 12px; overflow: hidden; display: flex; align-items: center; justify-content: center;
            background: color-mix(in srgb, var(--accent-color), transparent 88%); color: var(--accent-color); font-size: 26px; font-weight: 600; }
        .search-results .result-logo img { width: 100%; height: 100%; object-fit: contain; }
        .search-results .result-name { font-size: 18px; margin: 0 0 4px; color: var(--heading-color); overflow-wrap: anywhere; }
        .search-results .result-meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; font-size: 13px; margin-bottom: 8px;
            color: color-mix(in srgb, var(--default-color), transparent 40%); }
        .search-results .result-meta .bi-star-fill { color: #f5b301; }
        .search-results .badge-adults { background: color-mix(in srgb, #dc3545, transparent 85%); color: #b02a37; border-radius: 4px; padding: 1px 6px; font-weight: 600; }
        .search-results .result-about { font-size: 14px; margin-bottom: 10px; color: color-mix(in srgb, var(--default-color), transparent 25%); }
        .search-results .result-tags { display: flex; flex-wrap: wrap; gap: 6px; }
        .search-results .result-tags span { font-size: 12px; padding: 2px 10px; border-radius: 999px; border: 1px solid color-mix(in srgb, var(--default-color), transparent 85%); }
        .search-results .result-tags span.match { background: color-mix(in srgb, var(--accent-color), transparent 85%); border-color: transparent; color: var(--heading-color); }
        .search-results .empty-state { text-align: center; padding: 48px 16px; }
        .search-results .empty-state .bi { font-size: 40px; color: color-mix(in srgb, var(--default-color), transparent 60%); }
        .search-results .search-again { max-width: 520px; margin: 20px auto 0; }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= $search_query === '' ? lang('System.page.search') : esc(lang('System.search.title-for', [$search_query])) ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="<?= esc(base_url(), 'attr') ?>"><?= lang('System.page.home') ?></a></li>
                    <li class="current"><?= lang('System.page.search') ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <section id="search-results" class="search-results section">
        <div class="container">
            <?php if ($search_query === '' || $failed || $businesses === []): ?>
                <div class="empty-state">
                    <?php if ($failed): ?>
                        <i class="bi bi-cloud-slash"></i>
                        <h4 class="mt-3"><?= lang('System.search.failed') ?></h4>
                    <?php elseif ($search_query === ''): ?>
                        <i class="bi bi-search"></i>
                        <h4 class="mt-3"><?= lang('System.search.prompt') ?></h4>
                        <p class="mb-0"><?= lang('System.search.prompt-help') ?></p>
                    <?php else: ?>
                        <i class="bi bi-emoji-neutral"></i>
                        <h4 class="mt-3"><?= esc(lang('System.search.none', [$search_query])) ?></h4>
                        <p class="mb-0"><?= lang('System.search.none-help') ?></p>
                    <?php endif ?>
                    <form class="search-again input-group" action="<?= esc(base_url('search'), 'attr') ?>" method="get" role="search">
                        <input type="search" class="form-control" name="q" value="<?= esc($search_query, 'attr') ?>" maxlength="100" required
                               placeholder="<?= esc(lang('System.search.placeholder'), 'attr') ?>" aria-label="<?= esc(lang('System.search.label'), 'attr') ?>">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> <?= lang('System.search.submit') ?></button>
                    </form>
                </div>
            <?php else: ?>
                <p class="mb-4"><?= esc(lang('System.search.count', [(int) $results['total']])) ?></p>
                <div class="row g-4">
                    <?php foreach ($businesses as $business): ?>
                        <?php
                        $tags = (array) ($business['tags'] ?? []);
                        usort($tags, static fn (string $a, string $b): int => $matches($b) <=> $matches($a)); // stable: the API's order otherwise
                        $logoUrl = (string) ($business['logo']['url'] ?? '');
                        $about   = trim((string) ($business['description'] ?? ''));
                        $count   = (int) ($business['rating']['count'] ?? 0);
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <a class="result-card" href="<?= esc(base_url(rawurlencode((string) $business['slug'])), 'attr') ?>">
                                <span class="result-logo" aria-hidden="true">
                                    <?php if (preg_match('#^https?://#i', $logoUrl)): ?>
                                        <img src="<?= esc($logoUrl, 'attr') ?>" alt="" loading="lazy">
                                    <?php else: ?>
                                        <?= esc(mb_strtoupper(mb_substr((string) $business['name'], 0, 1))) ?>
                                    <?php endif ?>
                                </span>
                                <div class="flex-grow-1" style="min-width: 0">
                                    <h2 class="result-name"><?= esc((string) $business['name']) ?></h2>
                                    <span class="result-meta">
                                        <?php if ($count > 0): ?>
                                            <span><i class="bi bi-star-fill" aria-hidden="true"></i> <?= esc((string) $business['rating']['average']) ?>
                                                <?= esc(lang('System.search.reviews', [$count])) ?></span>
                                        <?php endif ?>
                                        <?php if (! empty($business['age_restricted'])): ?>
                                            <span class="badge-adults" title="<?= esc(lang('System.search.adults-only'), 'attr') ?>">18+</span>
                                        <?php endif ?>
                                    </span>
                                    <?php if ($about !== ''): ?>
                                        <span class="result-about d-block"><?= esc(mb_strimwidth($about, 0, 140, '…')) ?></span>
                                    <?php endif ?>
                                    <?php if ($tags !== []): ?>
                                        <span class="result-tags">
                                            <?php foreach (array_slice($tags, 0, 6) as $tag): ?>
                                                <span<?= $matches($tag) ? ' class="match"' : '' ?>><?= esc($tag) ?></span>
                                            <?php endforeach ?>
                                        </span>
                                    <?php endif ?>
                                </div>
                            </a>
                        </div>
                    <?php endforeach ?>
                </div>
                <?php if ($pages > 1): ?>
                    <nav class="mt-5" aria-label="<?= esc(lang('System.search.pages'), 'attr') ?>">
                        <ul class="pagination justify-content-center flex-wrap">
                            <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= esc($page_url(max(1, $page - 1)), 'attr') ?>" aria-label="<?= esc(lang('System.search.previous'), 'attr') ?>"><i class="bi bi-chevron-left"></i></a>
                            </li>
                            <?php for ($n = max(1, $page - 2); $n <= min($pages, $page + 2); $n++): ?>
                                <li class="page-item<?= $n === $page ? ' active' : '' ?>">
                                    <a class="page-link" href="<?= esc($page_url($n), 'attr') ?>"<?= $n === $page ? ' aria-current="page"' : '' ?>><?= $n ?></a>
                                </li>
                            <?php endfor ?>
                            <li class="page-item<?= $page >= $pages ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= esc($page_url(min($pages, $page + 1)), 'attr') ?>" aria-label="<?= esc(lang('System.search.next'), 'attr') ?>"><i class="bi bi-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                <?php endif ?>
            <?php endif ?>
        </div>
    </section>
<?= $this->endSection() ?>
