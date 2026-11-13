<?php
/**
 * The sessions of a group service a customer can book (Business::serviceSessions()), from v1/mart/sessions: each is
 * booked whole, every meeting of it
 *
 * @var array        $site         v1/mart/site
 * @var array        $service      v1/mart/sessions service, with url, label, details, price and compare written out
 * @var array        $variant      v1/mart/sessions service.variant
 * @var list<array>  $stores       {public_id, name, address, address_text, phone_number, timezone}
 * @var array|null   $store        the store filtered on, one of $stores
 * @var list<array>  $staff        the people of the sessions: {public_id, name, restrictions, restriction_lines}
 * @var array|null   $chosen_staff the person filtered on, one of $staff
 * @var string|null  $date_from    Y-m-d
 * @var string|null  $date_to      Y-m-d
 * @var list<array>  $sessions     {public_id, name, description, dates, closes, seats_available, capacity,
 *                                 store: {public_id, name, address, phone_number}, staff: [{name, restrictions: [line]}],
 *                                 slots: [{number, when, staff}]}
 * @var string|null  $notice       why a filter was dropped
 * @var callable     $url          array of query changes => this page's URL
 */
$this->extend('_layout_otternova');

$business  = $site['business'];
$homeUrl   = base_url(rawurlencode($business['slug']));
$cartUrl   = base_url(rawurlencode($business['slug']) . '/add-to-cart');
$searchUrl = base_url(rawurlencode($business['slug']) . '/search-sessions');
$initial   = static fn (string $name): string => mb_strtoupper(mb_substr($name, 0, 1));
$filtered  = $store !== null || $chosen_staff !== null || $date_from !== null || $date_to !== null;
?>
<?= $this->section('content') ?>
    <style>
        .session-search .service-summary { display: flex; gap: 14px; align-items: flex-start; }
        .session-search .service-summary img { width: 84px; height: 84px; object-fit: cover; border-radius: 8px; flex: none; }
        .session-search .service-summary .name { font-weight: 600; margin-bottom: 2px; }
        .session-search .service-summary .meta { font-size: 14px; color: color-mix(in srgb, var(--default-color), transparent 30%); }
        .session-search .service-summary .price { font-weight: 600; color: var(--accent-color); }
        .session-search .service-summary del { font-weight: 400; font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 45%); margin-left: 4px; }
        .session-search .policy-list { list-style: none; padding: 0; margin: 0; font-size: 14px; }
        .session-search .policy-list li { margin-bottom: 6px; }
        .session-search .policy-list i { color: var(--accent-color); margin-right: 6px; }
        .session-search .filter-widget-3 .brand-list { max-height: none; }
        .session-search .filter-widget-3 .option-label { justify-content: flex-start; align-items: flex-start; flex-direction: column; }
        .session-search .filter-widget-3 .option-label.person { flex-direction: row; align-items: center; }
        .session-search .option-label .sub { display: block; font-size: 12px; color: color-mix(in srgb, var(--default-color), transparent 40%); }
        .session-search .avatar-initial { width: 26px; height: 26px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: color-mix(in srgb, var(--accent-color), transparent 85%); color: var(--accent-color); font-size: 12px; font-weight: 600; flex: none; margin-right: 6px; }
        .session-search .note-icon { color: var(--accent-color); margin-left: 6px; font-size: 13px; }
        .session-search .restriction-box { border-left: 3px solid var(--accent-color); background: color-mix(in srgb, var(--accent-color), transparent 93%); border-radius: 4px; padding: 10px 12px; font-size: 14px; margin-top: 12px; }
        .session-search .restriction-box strong { display: block; margin-bottom: 4px; }
        .session-search .restriction-box ul { margin: 0; padding-left: 18px; }
        .session-search .date-range { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
        .session-search .date-range label { font-size: 14px; margin-bottom: 4px; }
        .session-search .date-range input { max-width: 200px; }
        .session-search .results-heading { display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
        .session-search .results-heading h2 { font-size: 20px; margin: 0; }
        .session-search .results-heading span { font-size: 14px; color: color-mix(in srgb, var(--default-color), transparent 35%); }
        .session-search .session-btn { width: 100%; height: 100%; text-align: left; border: 1px solid color-mix(in srgb, var(--default-color), transparent 85%); background: var(--surface-color); color: var(--default-color); border-radius: 8px; padding: 12px 14px; }
        .session-search .session-btn:hover { border-color: var(--accent-color); }
        .session-search .session-btn.active { border-color: var(--accent-color); box-shadow: 0 0 0 1px var(--accent-color); background: color-mix(in srgb, var(--accent-color), transparent 92%); }
        .session-search .session-btn .name { display: block; font-weight: 600; font-size: 16px; margin-bottom: 4px; }
        .session-search .session-btn .dates { display: block; font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 25%); }
        .session-search .session-btn .dates i { color: var(--accent-color); margin-right: 4px; }
        .session-search .session-btn .seats { display: inline-block; margin-top: 8px; font-size: 12px; font-weight: 600; padding: 2px 8px; border-radius: 999px; background: color-mix(in srgb, var(--accent-color), transparent 88%); color: var(--accent-color); }
        .session-search .session-btn .seats.few { background: color-mix(in srgb, #d97706, transparent 85%); color: #b45309; }
        .session-search .empty-state { text-align: center; padding: 40px 16px; border: 1px dashed color-mix(in srgb, var(--default-color), transparent 80%); border-radius: 8px; color: color-mix(in srgb, var(--default-color), transparent 25%); }
        .session-search .empty-state i { font-size: 32px; color: var(--accent-color); display: block; margin-bottom: 8px; }
        .session-search .selection-card { z-index: 5; background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 85%); border-radius: 8px; padding: 14px 16px; margin-top: 24px; box-shadow: 0 -4px 16px color-mix(in srgb, var(--default-color), transparent 92%); }
        /* not sticky as on the slots page: a session of many meetings would cover the list */
        .session-search .selection-card.chosen { border-color: var(--accent-color); }
        .session-search .selection-card .label { font-size: 12px; text-transform: uppercase; color: color-mix(in srgb, var(--default-color), transparent 40%); }
        .session-search .selection-card .value { font-weight: 600; }
        .session-search .selection-card .value small { display: block; font-weight: 400; color: color-mix(in srgb, var(--default-color), transparent 35%); }
        .session-search .selection-card [hidden] { display: none !important; }
        .session-search .slot-list { list-style: none; padding: 0; margin: 4px 0 0; font-size: 14px; max-height: 280px; overflow-y: auto; }
        .session-search .slot-list li { display: flex; gap: 10px; padding: 6px 0; border-top: 1px solid color-mix(in srgb, var(--default-color), transparent 90%); }
        .session-search .slot-list li:first-child { border-top: 0; }
        .session-search .slot-list .number { flex: none; width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: color-mix(in srgb, var(--accent-color), transparent 85%); color: var(--accent-color); font-size: 12px; font-weight: 600; }
        .session-search .slot-list .who { display: block; font-size: 12px; color: color-mix(in srgb, var(--default-color), transparent 35%); }
        /* the theme styles .primary-action-btn on the product card only */
        .session-search .primary-action-btn { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 24px; background: var(--accent-color); color: var(--contrast-color); border: none; border-radius: 6px; font-weight: 500; font-size: 14px; transition: background 0.2s ease; }
        .session-search .primary-action-btn:hover { background: color-mix(in srgb, var(--accent-color), #000 15%); color: var(--contrast-color); }
        .session-search .primary-action-btn:focus { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent-color), transparent 80%); }
        .session-search .primary-action-btn:disabled { background: var(--accent-color); color: var(--contrast-color); opacity: .45; }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= esc(lang('Business.slots.title', [$service['name']])) ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="<?= esc($homeUrl, 'attr') ?>"><?= esc($business['name']) ?></a></li>
                    <li><a href="<?= esc($service['url'], 'attr') ?>"><?= esc($service['name']) ?></a></li>
                    <li class="current"><?= lang('Business.sessions.breadcrumb') ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container session-search">
        <div class="row">
            <div class="col-lg-4 sidebar order-2 order-lg-1">
                <div class="widgets-container">
                    <!-- The service being booked -->
                    <div class="widget-item">
                        <h3 class="widget-title"><?= lang('Business.slots.your-booking') ?></h3>
                        <div class="service-summary">
                            <?php if ($service['cover_url'] !== null): ?>
                                <img src="<?= esc($service['cover_url'], 'attr') ?>" alt="" loading="lazy">
                            <?php endif ?>
                            <div>
                                <div class="name"><?= esc($service['name']) ?></div>
                                <div class="meta">
                                    <?= esc(implode(' · ', array_filter(array_merge([$service['label']], $service['details'])))) ?>
                                </div>
                                <?php if ($service['price'] !== null): ?>
                                    <div class="price"><?= esc($service['price']) ?><?php if ($service['compare'] !== null): ?><del><?= esc($service['compare']) ?></del><?php endif ?></div>
                                <?php endif ?>
                                <a href="<?= esc($service['url'], 'attr') ?>" class="small"><?= lang('Business.slots.change') ?></a>
                            </div>
                        </div>
                    </div>

                    <!-- How a session is booked -->
                    <div class="widget-item">
                        <h3 class="widget-title"><?= lang('Business.slots.policy') ?></h3>
                        <ul class="policy-list">
                            <li><i class="bi bi-check2-circle"></i><?= lang('Business.sessions.whole') ?></li>
                            <li><i class="bi bi-check2-circle"></i><?= lang('Business.sessions.closes-policy') ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 order-1 order-lg-2">
                <section id="category-header" class="category-header section">
                    <div class="container">
                        <?php if ($notice !== null && $notice !== ''): ?>
                            <div class="alert alert-warning" role="alert"><i class="bi bi-info-circle me-1"></i><?= esc($notice) ?></div>
                        <?php endif ?>

                        <?php if ($stores !== []): ?>
                            <!-- Where, who and when -->
                            <form method="get" action="<?= esc($searchUrl, 'attr') ?>" id="session-filters" class="row g-3 mb-3">
                                <input type="hidden" name="variant" value="<?= esc($variant['public_id'], 'attr') ?>">

                                <div class="col-md-6">
                                    <div class="filter-widget-3 widget-item h-100 mb-0">
                                        <h3 class="widget-title"><?= lang('Business.slots.location') ?></h3>
                                        <div class="brand-list">
                                            <div class="brand-item">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="store" value="" id="store-any" data-resets="staff"<?= $store === null ? ' checked' : '' ?>>
                                                    <label class="form-check-label option-label" for="store-any"><?= lang('Business.slots.all-stores') ?></label>
                                                </div>
                                            </div>
                                            <?php foreach ($stores as $i => $option): ?>
                                                <div class="brand-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="store" value="<?= esc($option['public_id'], 'attr') ?>" id="store-<?= $i ?>"
                                                               data-resets="staff"<?= ($store['public_id'] ?? null) === $option['public_id'] ? ' checked' : '' ?>>
                                                        <label class="form-check-label option-label" for="store-<?= $i ?>">
                                                            <?= esc($option['name']) ?>
                                                            <?php if ($option['address_text'] !== null): ?><span class="sub"><?= esc($option['address_text']) ?></span><?php endif ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="filter-widget-3 widget-item h-100 mb-0">
                                        <h3 class="widget-title"><?= lang('Business.slots.staff') ?></h3>
                                        <div class="brand-list">
                                            <div class="brand-item">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="staff" value="" id="staff-any"<?= $chosen_staff === null ? ' checked' : '' ?>>
                                                    <label class="form-check-label option-label" for="staff-any"><?= lang('Business.slots.anyone') ?></label>
                                                </div>
                                            </div>
                                            <?php foreach ($staff as $i => $person): ?>
                                                <div class="brand-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="staff" value="<?= esc($person['public_id'], 'attr') ?>" id="staff-<?= $i ?>"
                                                               <?= ($chosen_staff['public_id'] ?? null) === $person['public_id'] ? ' checked' : '' ?>>
                                                        <label class="form-check-label option-label person" for="staff-<?= $i ?>">
                                                            <span class="avatar-initial" aria-hidden="true"><?= esc($initial($person['name'])) ?></span><?= esc($person['name']) ?>
                                                            <?php if ($person['restriction_lines'] !== []): ?>
                                                                <i class="bi bi-info-circle note-icon" title="<?= esc(implode(' · ', $person['restriction_lines']), 'attr') ?>"></i>
                                                            <?php endif ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach ?>
                                        </div>
                                        <?php if ($chosen_staff !== null && $chosen_staff['restriction_lines'] !== []): ?>
                                            <div class="restriction-box" role="note">
                                                <strong><i class="bi bi-info-circle me-1"></i><?= lang('Business.slots.restrictions') ?> · <?= esc($chosen_staff['name']) ?></strong>
                                                <ul>
                                                    <?php foreach ($chosen_staff['restriction_lines'] as $line): ?>
                                                        <li><?= esc($line) ?></li>
                                                    <?php endforeach ?>
                                                </ul>
                                            </div>
                                        <?php endif ?>
                                    </div>
                                </div>

                                <!-- The days a session starts on -->
                                <div class="col-12">
                                    <div class="filter-widget-3 widget-item mb-0">
                                        <h3 class="widget-title"><?= lang('Business.sessions.starts') ?></h3>
                                        <div class="date-range">
                                            <div>
                                                <label for="filter-date-from"><?= lang('Business.sessions.from') ?></label>
                                                <input type="date" class="form-control form-control-sm" id="filter-date-from" name="date_from" value="<?= esc((string) $date_from, 'attr') ?>">
                                            </div>
                                            <div>
                                                <label for="filter-date-to"><?= lang('Business.sessions.to') ?></label>
                                                <input type="date" class="form-control form-control-sm" id="filter-date-to" name="date_to" value="<?= esc((string) $date_to, 'attr') ?>"
                                                       <?= $date_from !== null ? 'min="' . esc($date_from, 'attr') . '"' : '' ?>>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-sm btn-outline-primary"><?= lang('Business.sessions.apply') ?></button>
                                                <?php if ($filtered): ?>
                                                    <a class="btn btn-sm btn-link" href="<?= esc($url(['store' => null, 'staff' => null, 'date_from' => null, 'date_to' => null]), 'attr') ?>"><?= lang('Business.slots.reset') ?></a>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php endif ?>
                    </div>
                </section>

                <section id="category-product-list" class="category-product-list section">
                    <div class="container">
                        <?php if ($stores === []): ?>
                            <div class="empty-state"><i class="bi bi-calendar-x"></i><?= lang('Business.sessions.closed') ?></div>
                        <?php else: ?>
                            <div class="results-heading">
                                <h2><?= lang('Business.sessions.results') ?></h2>
                                <span><?= lang('Business.sessions.found', [count($sessions)]) ?></span>
                            </div>

                            <?php if ($sessions === []): ?>
                                <div class="empty-state"><i class="bi bi-calendar-x"></i><?= lang('Business.sessions.none') ?></div>
                            <?php else: ?>
                                <div class="row g-2">
                                    <?php foreach ($sessions as $i => $session): ?>
                                        <div class="col-md-6">
                                            <button type="button" class="session-btn" data-index="<?= $i ?>">
                                                <span class="name"><?= esc($session['name']) ?></span>
                                                <span class="dates"><i class="bi bi-calendar-event"></i><?= esc($session['dates']) ?></span>
                                                <?php if ($store === null && $session['store'] !== null): ?>
                                                    <span class="dates"><i class="bi bi-geo-alt"></i><?= esc($session['store']['name']) ?></span>
                                                <?php endif ?>
                                                <span class="seats<?= $session['seats_available'] <= 3 ? ' few' : '' ?>"><?= lang('Business.sessions.seats-left', [$session['seats_available']]) ?></span>
                                            </button>
                                        </div>
                                    <?php endforeach ?>
                                </div>

                                <!-- The chosen session, sent on to the booking -->
                                <form class="selection-card" method="post" action="<?= esc($cartUrl, 'attr') ?>" id="session-selection">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="variant" value="<?= esc($variant['public_id'], 'attr') ?>">
                                    <input type="hidden" name="session" value="">
                                    <p class="mb-0" id="selection-empty"><?= lang('Business.sessions.choose-first') ?></p>
                                    <div id="selection-details" hidden>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="label"><?= lang('Business.sessions.service-label') ?></div>
                                                <div class="value"><?= esc($service['name']) ?><?php if ($service['label'] !== ''): ?><small><?= esc($service['label']) ?></small><?php endif ?></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="label"><?= lang('Business.sessions.session-label') ?></div>
                                                <div class="value"><span data-field="name"></span><small data-field="dates"></small></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="label"><?= lang('Business.slots.location') ?></div>
                                                <div class="value"><span data-field="store"></span><small data-field="address"></small></div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="label"><?= lang('Business.sessions.seats-label') ?></div>
                                                <div class="value" data-field="seats"></div>
                                            </div>
                                            <div class="col-md-3 col-6">
                                                <div class="label"><?= lang('Business.sessions.closes-label') ?></div>
                                                <div class="value" data-field="closes"></div>
                                            </div>
                                            <div class="col-12" data-field="description-box">
                                                <div class="label"><?= lang('Business.service.description') ?></div>
                                                <div data-field="description" style="white-space: pre-line; font-size: 14px;"></div>
                                            </div>
                                            <div class="col-12">
                                                <div class="label" data-field="slots-label"></div>
                                                <ul class="slot-list" data-field="slots"></ul>
                                            </div>
                                        </div>
                                        <div class="restriction-box" id="selection-restrictions" role="note" hidden>
                                            <strong><i class="bi bi-info-circle me-1"></i><?= lang('Business.slots.restrictions') ?></strong>
                                            <ul></ul>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" id="selection-acknowledge">
                                                <label class="form-check-label" for="selection-acknowledge"><?= lang('Business.slots.acknowledge') ?></label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn primary-action-btn w-100 mt-3" id="selection-continue" disabled><?= lang('Business.slots.continue') ?></button>
                                    </div>
                                </form>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                </section>
            </div>

        </div>
    </div>
    <script>
        (() => {
            // a store or a person chosen: show what it gives (a person of another store goes with the store)
            const filters = document.getElementById('session-filters');
            const tidy = () => {
                // an empty choice means any: leave it out of the URL
                filters.querySelectorAll('input').forEach((input) => {
                    if (input.value === '' && (input.type !== 'radio' || input.checked)) {
                        input.disabled = true;
                    }
                });
            };
            filters?.querySelectorAll('input[type=radio]').forEach((radio) => radio.addEventListener('change', () => {
                (radio.dataset.resets || '').split(' ').filter(Boolean).forEach((name) => {
                    filters.querySelectorAll('input[name="' + name + '"]').forEach((input) => { input.disabled = true; });
                });
                tidy();
                filters.submit();
            }));
            filters?.addEventListener('submit', tidy);
            const from = document.getElementById('filter-date-from');
            from?.addEventListener('change', () => { document.getElementById('filter-date-to').min = from.value; });

            const form = document.getElementById('session-selection');
            if (!form) {
                return;
            }
            const sessions     = <?= json_encode($sessions, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            const texts        = <?= json_encode([
                'seats' => lang('Business.sessions.seats-of', ['{available}', '{capacity}']),
                'slots' => lang('Business.sessions.slots-label', ['{count}']),
            ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
            const field        = (name) => form.querySelector('[data-field="' + name + '"]');
            const details      = document.getElementById('selection-details');
            const restrictions = document.getElementById('selection-restrictions');
            const acknowledge  = document.getElementById('selection-acknowledge');
            const submit       = document.getElementById('selection-continue');
            const update = () => {
                submit.disabled = form.elements.session.value === '' || (!restrictions.hidden && !acknowledge.checked);
            };
            acknowledge.addEventListener('change', update);
            document.querySelectorAll('.session-btn').forEach((button) => button.addEventListener('click', () => {
                document.querySelectorAll('.session-btn.active').forEach((b) => b.classList.remove('active'));
                button.classList.add('active');
                const session = sessions[Number(button.dataset.index)];
                form.elements.session.value = session.public_id;
                field('name').textContent    = session.name;
                field('dates').textContent   = session.dates;
                field('store').textContent   = session.store ? session.store.name : '';
                field('address').textContent = session.store && session.store.address ? session.store.address : '';
                field('seats').textContent   = texts.seats.replace('{available}', session.seats_available).replace('{capacity}', session.capacity);
                field('closes').textContent  = session.closes || '';
                field('description').textContent = session.description || '';
                field('description-box').hidden  = !session.description;
                field('slots-label').textContent = texts.slots.replace('{count}', session.slots.length);
                field('slots').replaceChildren(...session.slots.map((slot) => {
                    const item = document.createElement('li');
                    const text = document.createElement('div');
                    text.append(slot.when);
                    if (slot.staff) {
                        text.append(Object.assign(document.createElement('span'), {className: 'who', textContent: slot.staff}));
                    }
                    item.append(Object.assign(document.createElement('span'), {className: 'number', textContent: slot.number}), text);
                    return item;
                }));
                // what the people of the session do not serve, to be acknowledged
                const people = session.staff.filter((p) => p.restrictions.length > 0);
                const lines  = people.flatMap((p) => p.restrictions.map((line) => (session.staff.length > 1 ? p.name + ': ' : '') + line));
                restrictions.querySelector('ul').replaceChildren(...lines.map((line) => Object.assign(document.createElement('li'), {textContent: line})));
                restrictions.hidden = lines.length === 0;
                acknowledge.checked = false;
                document.getElementById('selection-empty').hidden = true;
                details.hidden = false;
                form.classList.add('chosen');
                update();
                form.scrollIntoView({block: 'nearest', behavior: 'smooth'});
            }));
        })();
    </script>
<?= $this->endSection() ?>
