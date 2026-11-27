<?php
/**
 * Report to Otternova (Business::report()): a visitor reports the business, or one of its products or services, for
 * Otternova's staff to review. The business never sees who reported it. A business that declared who it is for
 * (LGBTQ+ friendly, naturist, adults only) is introduced with a warning that reports about that are ignored and marked
 * as spam, as the API does with them.
 *
 * @var array       $site
 * @var list<array> $breadcrumbs Business::breadcrumbs()
 * @var array|null  $item        {public_id, name, type}: the product or service reported; null for the business
 * @var list<string> $audiences  lgbt, naturist, adult: what the business declared
 * @var list<string> $reasons    reason_code values, in the order shown
 * @var array       $report_form {token: string|null, action, sent: string|null, error: string|null, errors: array<string, string>, old: array<string, string>}
 */
$this->extend('_layout_otternova');

$business = $site['business'];
$form     = $report_form;
$old      = static fn (string $field): string => (string) ($form['old'][$field] ?? '');
$invalid  = static fn (string $field): string => isset($form['errors'][$field]) ? ' is-invalid' : '';
$feedback = static fn (string $field): string => isset($form['errors'][$field]) ? '<div class="invalid-feedback d-block">' . esc($form['errors'][$field]) . '</div>' : '';
$chosen   = $old('reason_code');
?>
<?= $this->section('content') ?>
    <style>
        .report-page .report-card { background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); border-radius: 14px; padding: 28px; scroll-margin-top: 100px; }
        .report-page .report-about { font-size: 15px; color: color-mix(in srgb, var(--default-color), transparent 25%); }
        .report-page .reason-option { border: 1px solid color-mix(in srgb, var(--default-color), transparent 88%); border-radius: 10px; padding: 10px 14px 10px 40px; margin-bottom: 8px; }
        .report-page .reason-option .form-check-input { margin-left: -26px; }
        .report-page .reason-option .hint { display: block; font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 40%); }
        .report-page .form-hint { font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 40%); }
        .report-page .btn-send { background: var(--accent-color); color: var(--contrast-color); border: 0; border-radius: 999px; padding: 10px 28px; }
        .report-page .btn-send:hover { filter: brightness(.92); }
        /* the honeypot: out of sight and out of the tab order, so only a bot fills it in */
        .report-page .report-website { position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden; }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= lang('Business.report-page.title') ?></h1>
            <?= view('section_parts/breadcrumbs', ['breadcrumbs' => $breadcrumbs]) ?>
        </div>
    </div>
    <section id="report" class="report-page section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="report-card" id="report-form">
                        <p class="report-about mb-2">
                            <?= null === $item
                                ? esc(lang('Business.report-page.about-business', [$business['name']]))
                                : esc(lang('Business.report-page.about-item', [$item['name'], $business['name']])) ?>
                        </p>
                        <p class="mb-3"><?= lang('Business.report-page.intro') ?></p>

                        <?php if ($audiences !== []): ?>
                            <div class="alert alert-warning" role="note" id="report-audience-warning">
                                <p class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle"></i> <?= lang('Business.report-page.audience-title') ?></p>
                                <ul class="mb-1">
                                    <?php foreach ($audiences as $audience): ?>
                                        <li><?= lang('Business.report-page.audience.' . $audience) ?></li>
                                    <?php endforeach ?>
                                </ul>
                                <p class="mb-0"><?= lang('Business.report-page.audience-warning') ?></p>
                            </div>
                        <?php endif ?>

                        <?php if ($form['sent'] !== null): ?>
                            <div class="alert alert-success" role="status"><i class="bi bi-check-circle"></i> <?= esc($form['sent']) ?></div>
                        <?php endif ?>
                        <?php if ($form['error'] !== null): ?>
                            <div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-circle"></i> <?= esc($form['error']) ?></div>
                        <?php endif ?>

                        <?php if ($form['token'] === null): ?>
                            <p class="text-muted mb-0"><?= lang('Business.report-page.unavailable') ?></p>
                        <?php elseif ($form['sent'] === null): ?>
                            <form method="post" action="<?= esc($form['action'], 'attr') ?>" novalidate>
                                <?= csrf_field() ?>
                                <input type="hidden" name="form_token" value="<?= esc($form['token'], 'attr') ?>">
                                <input type="hidden" name="item" value="<?= esc($item['public_id'] ?? '', 'attr') ?>">
                                <div class="report-website" aria-hidden="true">
                                    <label for="report-website"><?= lang('Business.contact-page.form-leave-empty') ?></label>
                                    <input type="text" id="report-website" name="website" value="" tabindex="-1" autocomplete="off">
                                </div>
                                <fieldset class="mb-3">
                                    <legend class="form-label fs-6"><?= lang('Business.report-page.reason') ?></legend>
                                    <?php foreach ($reasons as $reason): ?>
                                        <div class="form-check reason-option">
                                            <input class="form-check-input<?= $invalid('reason_code') ?>" type="radio" name="reason_code" id="reason-<?= esc(strtolower($reason), 'attr') ?>"
                                                   value="<?= esc($reason, 'attr') ?>"<?= $chosen === $reason ? ' checked' : '' ?> required>
                                            <label class="form-check-label" for="reason-<?= esc(strtolower($reason), 'attr') ?>">
                                                <?= lang('Business.report-page.reasons.' . $reason . '.label') ?>
                                                <span class="hint"><?= lang('Business.report-page.reasons.' . $reason . '.hint') ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach ?>
                                    <?= $feedback('reason_code') ?>
                                </fieldset>
                                <div class="mb-3">
                                    <label for="report-details" class="form-label"><?= lang('Business.report-page.details') ?></label>
                                    <textarea class="form-control<?= $invalid('details') ?>" id="report-details" name="details" rows="5" maxlength="2000"
                                              aria-describedby="report-details-hint"><?= esc($old('details')) ?></textarea>
                                    <div id="report-details-hint" class="form-hint mt-1"><?= lang('Business.report-page.details-hint') ?></div>
                                    <?= $feedback('details') ?>
                                </div>
                                <div class="mb-3">
                                    <label for="report-email" class="form-label"><?= lang('Business.report-page.email') ?></label>
                                    <input type="email" class="form-control<?= $invalid('email') ?>" id="report-email" name="email" maxlength="128" autocomplete="email"
                                           value="<?= esc($old('email'), 'attr') ?>" aria-describedby="report-email-hint">
                                    <div id="report-email-hint" class="form-hint mt-1"><?= lang('Business.report-page.email-hint') ?></div>
                                    <?= $feedback('email') ?>
                                </div>
                                <?= $feedback('locale') ?>
                                <button type="submit" class="btn btn-send"><i class="bi bi-flag"></i> <?= lang('Business.report-page.send') ?></button>
                            </form>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?= $this->endSection() ?>
