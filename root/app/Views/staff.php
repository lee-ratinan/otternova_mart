<?php
/**
 * Our staff (Business::staff()): the people the business shows on its pages, each with their picture (or initials),
 * name, the public attributes the business keeps of them, the languages they speak, their bio, where they work (when
 * the business has more than one place) and the customers they do not serve
 *
 * @var array  $site
 * @var string $home_url
 * @var bool   $many_stores
 * @var list<array{name: string, initials: string, avatar_url: string|null, bio: string|null, attributes: list<array{name: string, values: list<string>}>,
 *      languages: list<array{name: string, level: string}>, restrictions: list<string>, stores: list<string>}> $people
 */
$this->extend('_layout_otternova');

$business = $site['business'];
?>
<?= $this->section('content') ?>
    <style>
        .staff-list .lead-in { max-width: 640px; margin: 0 auto 36px; text-align: center; color: color-mix(in srgb, var(--default-color), transparent 25%); }
        .staff-list .person { background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); border-radius: 16px;
            padding: 28px 24px 24px; height: 100%; display: flex; flex-direction: column; transition: box-shadow .2s ease, transform .2s ease; }
        .staff-list .person:hover { box-shadow: 0 12px 30px color-mix(in srgb, var(--default-color), transparent 90%); transform: translateY(-2px); }
        .staff-list .person-head { text-align: center; margin-bottom: 16px; }
        .staff-list .avatar { width: 112px; height: 112px; border-radius: 50%; object-fit: cover; margin: 0 auto 14px; display: flex; align-items: center; justify-content: center;
            border: 4px solid color-mix(in srgb, var(--accent-color), transparent 80%); }
        .staff-list .avatar.initials { background: color-mix(in srgb, var(--accent-color), transparent 85%); color: var(--accent-color); font-size: 36px; font-weight: 600;
            font-family: var(--heading-font); }
        .staff-list .person h3 { font-size: 20px; margin-bottom: 6px; }
        .staff-list .tags { display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; }
        .staff-list .tag { background: color-mix(in srgb, var(--accent-color), transparent 90%); color: var(--accent-color); border-radius: 999px; padding: 2px 12px; font-size: 13px; }
        .staff-list .facts { margin: 0 0 14px; font-size: 14px; }
        .staff-list .facts dt { font-size: 12px; text-transform: uppercase; letter-spacing: .06em; font-weight: 600; color: color-mix(in srgb, var(--default-color), transparent 45%); margin-top: 10px; }
        .staff-list .facts dd { margin: 2px 0 0; color: color-mix(in srgb, var(--default-color), transparent 15%); }
        .staff-list .facts .level { color: color-mix(in srgb, var(--default-color), transparent 50%); }
        .staff-list .bio { font-size: 15px; line-height: 1.7; color: color-mix(in srgb, var(--default-color), transparent 20%); white-space: pre-line; margin-bottom: 14px; }
        .staff-list .note { margin-top: auto; border-radius: 10px; padding: 10px 14px; font-size: 13px;
            background: color-mix(in srgb, #f0ad4e, transparent 88%); color: color-mix(in srgb, var(--default-color), transparent 10%); }
        .staff-list .note strong { display: block; margin-bottom: 2px; }
        .staff-list .note ul { margin: 0; padding-left: 18px; }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= lang('Business.staff-page.title') ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="<?= esc($home_url, 'attr') ?>"><?= esc($business['name']) ?></a></li>
                    <li class="current"><?= lang('Business.staff-page.title') ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <section id="staff" class="staff-list section">
        <div class="container">
            <p class="lead-in"><?= lang('Business.staff-page.intro') ?></p>
            <div class="row g-4 justify-content-center">
                <?php foreach ($people as $person): ?>
                    <div class="col-xl-4 col-md-6">
                        <article class="person">
                            <div class="person-head">
                                <?php if ($person['avatar_url']): ?>
                                    <img src="<?= esc($person['avatar_url'], 'attr') ?>" alt="<?= esc($person['name'], 'attr') ?>" class="avatar" loading="lazy">
                                <?php else: ?>
                                    <div class="avatar initials" aria-hidden="true"><?= esc($person['initials']) ?></div>
                                <?php endif ?>
                                <h3><?= esc($person['name']) ?></h3>
                            </div>
                            <?php if ($person['attributes'] !== [] || $person['languages'] !== [] || ($many_stores && $person['stores'] !== [])): ?>
                                <dl class="facts">
                                    <?php foreach ($person['attributes'] as $attribute): ?>
                                        <dt><?= esc($attribute['name']) ?></dt>
                                        <dd class="tags justify-content-start mt-1">
                                            <?php foreach ($attribute['values'] as $value): ?>
                                                <span class="tag"><?= esc($value) ?></span>
                                            <?php endforeach ?>
                                        </dd>
                                    <?php endforeach ?>
                                    <?php if ($person['languages'] !== []): ?>
                                        <dt><?= lang('Business.staff-page.speaks') ?></dt>
                                        <dd><?= implode(', ', array_map(static fn (array $l): string => esc($l['name']) . ' <span class="level">(' . esc($l['level']) . ')</span>', $person['languages'])) ?></dd>
                                    <?php endif ?>
                                    <?php if ($many_stores && $person['stores'] !== []): ?>
                                        <dt><?= lang('Business.staff-page.works-at') ?></dt>
                                        <dd><?= esc(implode(', ', $person['stores'])) ?></dd>
                                    <?php endif ?>
                                </dl>
                            <?php endif ?>
                            <?php if ($person['bio']): ?>
                                <p class="bio"><?= esc($person['bio']) ?></p>
                            <?php endif ?>
                            <?php if ($person['restrictions'] !== []): ?>
                                <div class="note">
                                    <strong><i class="bi bi-info-circle"></i> <?= lang('Business.slots.restrictions') ?></strong>
                                    <ul>
                                        <?php foreach ($person['restrictions'] as $line): ?>
                                            <li><?= esc($line) ?></li>
                                        <?php endforeach ?>
                                    </ul>
                                </div>
                            <?php endif ?>
                        </article>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>
