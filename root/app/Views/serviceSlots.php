<?php
/**
 * The times an appointment can be booked at (Business::serviceSlots()), from v1/mart/slots: at one store, one day at a
 * time, each time with whom it books
 *
 * @var array        $site         v1/mart/site
 * @var array        $service      v1/mart/slots service, with url, label, details, price and compare written out
 * @var array        $variant      v1/mart/slots service.variant
 * @var list<string> $policy       the booking rule in words
 * @var array|null   $window       {first_date, last_date} of the store; null when nothing can be booked
 * @var string|null  $date         the day shown, Y-m-d
 * @var string|null  $date_label   that day in words
 * @var array        $days         Business::dayStrip(): days, previous, next, first, last
 * @var list<array>  $stores       {public_id, name, address, address_text, phone_number, timezone, first_date, last_date}
 * @var array|null   $store        the store shown, one of $stores
 * @var list<array>  $staff        the store's people: {public_id, name, restrictions, restriction_lines}
 * @var array|null   $chosen_staff the person filtered on, one of $staff
 * @var array        $periods      morning|afternoon|evening => slots {starts_at, time, end_time, staff: [{public_id, name, restrictions: [line]}]}
 * @var int          $slot_count
 * @var string|null  $notice       why a filter was dropped
 * @var callable     $url          array of query changes => this page's URL
 */
$this->extend('_layout_otternova');
helper('html'); // formatPhoneNumber()

$business = $site['business'];
$homeUrl  = base_url(rawurlencode($business['slug']));
$cartUrl  = base_url(rawurlencode($business['slug']) . '/add-to-cart');
$initial  = static fn (string $name): string => mb_strtoupper(mb_substr($name, 0, 1));
$names    = static fn (array $people): string => implode(', ', array_column($people, 'name'));
?>
<?= $this->section('content') ?>
    <style>
        .slot-search .service-summary { display: flex; gap: 14px; align-items: flex-start; }
        .slot-search .service-summary img { width: 84px; height: 84px; object-fit: cover; border-radius: 8px; flex: none; }
        .slot-search .service-summary .name { font-weight: 600; margin-bottom: 2px; }
        .slot-search .service-summary .meta { font-size: 14px; color: color-mix(in srgb, var(--default-color), transparent 30%); }
        .slot-search .service-summary .price { font-weight: 600; color: var(--accent-color); }
        .slot-search .service-summary del { font-weight: 400; font-size: 13px; color: color-mix(in srgb, var(--default-color), transparent 45%); margin-left: 4px; }
        .slot-search .policy-list { list-style: none; padding: 0; margin: 0; font-size: 14px; }
        .slot-search .policy-list li { margin-bottom: 6px; }
        .slot-search .policy-list i { color: var(--accent-color); margin-right: 6px; }
        .slot-search .filter-widget-3 .brand-list { max-height: none; }
        .slot-search .filter-widget-3 .option-label { justify-content: flex-start; align-items: flex-start; flex-direction: column; }
        .slot-search .filter-widget-3 .option-label.person { flex-direction: row; align-items: center; }
        .slot-search .option-label .sub { display: block; font-size: 12px; color: color-mix(in srgb, var(--default-color), transparent 40%); }
        .slot-search .avatar-initial { width: 26px; height: 26px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: color-mix(in srgb, var(--accent-color), transparent 85%); color: var(--accent-color); font-size: 12px; font-weight: 600; flex: none; margin-right: 6px; }
        .slot-search .note-icon { color: var(--accent-color); margin-left: 6px; font-size: 13px; }
        .slot-search .restriction-box { border-left: 3px solid var(--accent-color); background: color-mix(in srgb, var(--accent-color), transparent 93%); border-radius: 4px; padding: 10px 12px; font-size: 14px; margin-top: 12px; }
        .slot-search .restriction-box strong { display: block; margin-bottom: 4px; }
        .slot-search .restriction-box ul { margin: 0; padding-left: 18px; }
        .slot-search .store-line { display: flex; gap: 10px; align-items: flex-start; font-size: 14px; margin-bottom: 14px; }
        .slot-search .store-line i { color: var(--accent-color); font-size: 18px; }
        .slot-search .day-strip { display: flex; align-items: stretch; gap: 8px; }
        .slot-search .day-strip .days { display: flex; gap: 8px; flex: 1; overflow-x: auto; padding-bottom: 2px; }
        .slot-search .day-chip { flex: 1 0 64px; text-align: center; border: 1px solid color-mix(in srgb, var(--default-color), transparent 85%); border-radius: 8px; padding: 8px 4px; color: var(--default-color); line-height: 1.2; }
        .slot-search .day-chip:hover { border-color: var(--accent-color); color: var(--default-color); }
        .slot-search .day-chip.active { background: var(--accent-color); border-color: var(--accent-color); color: var(--contrast-color); }
        .slot-search .day-chip .weekday, .slot-search .day-chip .month { display: block; font-size: 12px; text-transform: uppercase; opacity: .8; }
        .slot-search .day-chip .day { display: block; font-size: 20px; font-weight: 600; }
        .slot-search .day-nav { display: inline-flex; align-items: center; justify-content: center; width: 36px; flex: none; border-radius: 8px; border: 1px solid color-mix(in srgb, var(--default-color), transparent 85%); color: var(--default-color); }
        .slot-search .day-nav.disabled { opacity: .35; pointer-events: none; }
        .slot-search .date-picker { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; margin-top: 14px; }
        .slot-search .date-picker label { font-size: 14px; margin: 0; }
        .slot-search .date-picker input { max-width: 200px; }
        .slot-search .date-heading { display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
        .slot-search .date-heading h2 { font-size: 20px; margin: 0; }
        .slot-search .date-heading span { font-size: 14px; color: color-mix(in srgb, var(--default-color), transparent 35%); }
        .slot-search .period-title { font-size: 15px; font-weight: 600; margin: 18px 0 10px; }
        .slot-search .period-title i { color: var(--accent-color); margin-right: 6px; }
        .slot-search .slot-btn { width: 100%; text-align: left; border: 1px solid color-mix(in srgb, var(--default-color), transparent 85%); background: var(--surface-color); color: var(--default-color); border-radius: 8px; padding: 10px 12px; }
        .slot-search .slot-btn:hover { border-color: var(--accent-color); }
        .slot-search .slot-btn.active { border-color: var(--accent-color); box-shadow: 0 0 0 1px var(--accent-color); background: color-mix(in srgb, var(--accent-color), transparent 92%); }
        .slot-search .slot-btn .time { display: block; font-weight: 600; font-size: 16px; }
        .slot-search .slot-btn .where { display: block; font-size: 12px; color: color-mix(in srgb, var(--default-color), transparent 35%); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .slot-search .slot-btn .who { display: flex; align-items: center; margin-top: 6px; font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .slot-search .slot-btn .who .avatar-initial { width: 22px; height: 22px; font-size: 11px; }
        .slot-search .empty-state { text-align: center; padding: 40px 16px; border: 1px dashed color-mix(in srgb, var(--default-color), transparent 80%); border-radius: 8px; color: color-mix(in srgb, var(--default-color), transparent 25%); }
        .slot-search .empty-state i { font-size: 32px; color: var(--accent-color); display: block; margin-bottom: 8px; }
        .slot-search .selection-card { z-index: 5; background: var(--surface-color); border: 1px solid color-mix(in srgb, var(--default-color), transparent 85%); border-radius: 8px; padding: 14px 16px; margin-top: 24px; box-shadow: 0 -4px 16px color-mix(in srgb, var(--default-color), transparent 92%); }
        .slot-search .selection-card.chosen { position: sticky; bottom: 0; }
        .slot-search .selection-card .label { font-size: 12px; text-transform: uppercase; color: color-mix(in srgb, var(--default-color), transparent 40%); }
        .slot-search .selection-card .value { font-weight: 600; }
        .slot-search .selection-card .value small { display: block; font-weight: 400; color: color-mix(in srgb, var(--default-color), transparent 35%); }
        .slot-search .selection-card [hidden] { display: none !important; }
        /* the theme styles .primary-action-btn on the product card only */
        .slot-search .primary-action-btn { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 24px; background: var(--accent-color); color: var(--contrast-color); border: none; border-radius: 6px; font-weight: 500; font-size: 14px; transition: background 0.2s ease; }
        .slot-search .primary-action-btn:hover { background: color-mix(in srgb, var(--accent-color), #000 15%); color: var(--contrast-color); }
        .slot-search .primary-action-btn:focus { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent-color), transparent 80%); }
        .slot-search .primary-action-btn:disabled { background: var(--accent-color); color: var(--contrast-color); opacity: .45; }
    </style>
    <div class="page-title light-background">
        <div class="container d-lg-flex justify-content-between align-items-center">
            <h1 class="mb-2 mb-lg-0"><?= esc(lang('Business.slots.title', [$service['name']])) ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="<?= esc($homeUrl, 'attr') ?>"><?= esc($business['name']) ?></a></li>
                    <li><a href="<?= esc($service['url'], 'attr') ?>"><?= esc($service['name']) ?></a></li>
                    <li class="current"><?= lang('Business.slots.breadcrumb') ?></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container slot-search">
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

                    <!-- Booking policy -->
                    <div class="widget-item">
                        <h3 class="widget-title"><?= lang('Business.slots.policy') ?></h3>
                        <ul class="policy-list">
                            <?php foreach ($policy as $line): ?>
                                <li><i class="bi bi-check2-circle"></i><?= esc($line) ?></li>
                            <?php endforeach ?>
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

                        <?php if ($store !== null): ?>
                            <!-- Where, who and when -->
                            <form method="get" action="<?= esc(base_url(rawurlencode($business['slug']) . '/search-slots'), 'attr') ?>" id="slot-filters" class="row g-3 mb-3">
                                <input type="hidden" name="variant" value="<?= esc($variant['public_id'], 'attr') ?>">
                                <input type="hidden" name="date" value="<?= esc((string) $date, 'attr') ?>">

                                <div class="col-md-6">
                                    <div class="filter-widget-3 widget-item h-100 mb-0">
                                        <h3 class="widget-title"><?= lang('Business.slots.location') ?></h3>
                                        <div class="brand-list">
                                            <?php foreach ($stores as $i => $option): ?>
                                                <div class="brand-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="store" value="<?= esc($option['public_id'], 'attr') ?>" id="store-<?= $i ?>"
                                                               data-resets="date staff"<?= $option['public_id'] === $store['public_id'] ? ' checked' : '' ?>>
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
                                                    <input class="form-check-input" type="radio" name="staff" value="" id="staff-any" data-resets="date"<?= $chosen_staff === null ? ' checked' : '' ?>>
                                                    <label class="form-check-label option-label" for="staff-any"><?= lang('Business.slots.anyone') ?></label>
                                                </div>
                                            </div>
                                            <?php foreach ($staff as $i => $person): ?>
                                                <div class="brand-item">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="staff" value="<?= esc($person['public_id'], 'attr') ?>" id="staff-<?= $i ?>"
                                                               data-resets="date"<?= ($chosen_staff['public_id'] ?? null) === $person['public_id'] ? ' checked' : '' ?>>
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
                                <noscript><div class="col-12"><button type="submit" class="btn btn-sm btn-primary">OK</button></div></noscript>
                            </form>

                            <!-- Days of the store's booking window -->
                            <div class="controls-card">
                                <div class="controls-filters">
                                    <div class="day-strip">
                                        <a class="day-nav<?= $days['previous'] === null ? ' disabled' : '' ?>" href="<?= esc($days['previous'] ?? '#', 'attr') ?>" aria-label="<?= esc(lang('Business.slots.previous-week'), 'attr') ?>"><i class="bi bi-chevron-left"></i></a>
                                        <div class="days">
                                            <?php foreach ($days['days'] as $day): ?>
                                                <a class="day-chip<?= $day['active'] ? ' active' : '' ?>" href="<?= esc($day['url'], 'attr') ?>"<?= $day['active'] ? ' aria-current="date"' : '' ?>>
                                                    <span class="weekday"><?= esc($day['weekday']) ?></span>
                                                    <span class="day"><?= esc($day['day']) ?></span>
                                                    <span class="month"><?= esc($day['month']) ?></span>
                                                </a>
                                            <?php endforeach ?>
                                        </div>
                                        <a class="day-nav<?= $days['next'] === null ? ' disabled' : '' ?>" href="<?= esc($days['next'] ?? '#', 'attr') ?>" aria-label="<?= esc(lang('Business.slots.next-week'), 'attr') ?>"><i class="bi bi-chevron-right"></i></a>
                                    </div>
                                    <?php if ($days['first'] !== $days['last']): ?>
                                        <div class="date-picker">
                                            <label for="slot-date"><?= lang('Business.slots.pick-date') ?></label>
                                            <input type="date" class="form-control form-control-sm" id="slot-date" value="<?= esc((string) $date, 'attr') ?>"
                                                   min="<?= esc($days['first'], 'attr') ?>" max="<?= esc($days['last'], 'attr') ?>">
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </section>

                <section id="category-product-list" class="category-product-list section">
                    <div class="container">
                        <?php if ($store === null): ?>
                            <div class="empty-state"><i class="bi bi-calendar-x"></i><?= lang('Business.slots.closed') ?></div>
                        <?php else: ?>
                            <div class="date-heading">
                                <h2><?= esc((string) $date_label) ?></h2>
                                <span><?= lang('Business.slots.available', [$slot_count]) ?></span>
                            </div>
                            <div class="store-line">
                                <i class="bi bi-geo-alt"></i>
                                <div>
                                    <strong><?= esc($store['name']) ?></strong>
                                    <?php if ($store['address_text'] !== null): ?><br><?= esc($store['address_text']) ?><?php endif ?>
                                    <?php if (($store['phone_number'] ?? '') !== ''): ?><br><a href="tel:<?= esc(preg_replace('/[^0-9+]/', '', $store['phone_number']), 'attr') ?>"><?= esc(lang('Business.slots.call', [formatPhoneNumber($store['phone_number'])])) ?></a><?php endif ?>
                                </div>
                            </div>

                            <?php if ($slot_count === 0): ?>
                                <div class="empty-state"><i class="bi bi-calendar-x"></i><?= lang('Business.slots.none') ?></div>
                            <?php endif ?>

                            <?php foreach (['morning' => 'bi-sunrise', 'afternoon' => 'bi-sun', 'evening' => 'bi-moon-stars'] as $period => $icon): ?>
                                <?php if (! empty($periods[$period])): ?>
                                    <div class="period-title"><i class="bi <?= $icon ?>"></i><?= lang('Business.slots.' . $period) ?></div>
                                    <div class="row g-2">
                                        <?php foreach ($periods[$period] as $slot): ?>
                                            <div class="col-6 col-md-4 col-xl-3">
                                                <button type="button" class="slot-btn"
                                                        data-starts-at="<?= esc($slot['starts_at'], 'attr') ?>"
                                                        data-time="<?= esc($slot['time'] . '–' . $slot['end_time'], 'attr') ?>"
                                                        data-staff="<?= esc(json_encode($slot['staff'], JSON_UNESCAPED_UNICODE), 'attr') ?>">
                                                    <span class="time"><?= esc($slot['time']) ?></span>
                                                    <span class="where"><?= esc($slot['time'] . '–' . $slot['end_time']) ?></span>
                                                    <span class="who">
                                                        <?php if (count($slot['staff']) === 1): ?><span class="avatar-initial" aria-hidden="true"><?= esc($initial($slot['staff'][0]['name'])) ?></span><?php endif ?>
                                                        <?= esc($names($slot['staff'])) ?>
                                                    </span>
                                                </button>
                                            </div>
                                        <?php endforeach ?>
                                    </div>
                                <?php endif ?>
                            <?php endforeach ?>

                            <?php if ($slot_count > 0): ?>
                                <!-- The chosen time, sent on to the booking -->
                                <form class="selection-card" method="post" action="<?= esc($cartUrl, 'attr') ?>" id="slot-selection">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="variant" value="<?= esc($variant['public_id'], 'attr') ?>">
                                    <input type="hidden" name="store" value="<?= esc($store['public_id'], 'attr') ?>">
                                    <input type="hidden" name="starts_at" value="">
                                    <div id="selection-staff-inputs"></div>
                                    <p class="mb-0" id="selection-empty"><?= lang('Business.slots.choose-first') ?></p>
                                    <div id="selection-details" hidden>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <div class="label"><?= lang('Business.slots.time-label') ?></div>
                                                <div class="value"><span id="selection-time"></span><small><?= esc((string) $date_label) ?></small></div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="label"><?= lang('Business.slots.staff-label') ?></div>
                                                <div class="value" id="selection-staff"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="label"><?= lang('Business.slots.location') ?></div>
                                                <div class="value"><?= esc($store['name']) ?><?php if ($store['address_text'] !== null): ?><small><?= esc($store['address_text']) ?></small><?php endif ?></div>
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
            // a filter changed: show what it gives, from the first day with a free time again
            const filters = document.getElementById('slot-filters');
            filters?.querySelectorAll('input[type=radio]').forEach((radio) => radio.addEventListener('change', () => {
                const resets = (radio.dataset.resets || '').split(' ');
                filters.querySelectorAll('input').forEach((input) => {
                    if (resets.includes(input.name) && input !== radio && (input.type === 'hidden' || input.name !== radio.name)) {
                        input.disabled = true;
                    }
                });
                // an empty choice means anyone: leave it out of the URL
                filters.querySelectorAll('input[type=radio]:checked').forEach((r) => { if (r.value === '') { r.disabled = true; } });
                filters.submit();
            }));

            const picker = document.getElementById('slot-date');
            picker?.addEventListener('change', () => {
                if (!picker.value || picker.value < picker.min || picker.value > picker.max) {
                    return;
                }
                const url = new URL(window.location.href);
                url.searchParams.set('date', picker.value);
                window.location.href = url.toString();
            });

            const form = document.getElementById('slot-selection');
            if (!form) {
                return;
            }
            const details      = document.getElementById('selection-details');
            const restrictions = document.getElementById('selection-restrictions');
            const acknowledge  = document.getElementById('selection-acknowledge');
            const submit       = document.getElementById('selection-continue');
            const update = () => {
                submit.disabled = form.elements.starts_at.value === '' || (!restrictions.hidden && !acknowledge.checked);
            };
            acknowledge.addEventListener('change', update);
            document.querySelectorAll('.slot-btn').forEach((button) => button.addEventListener('click', () => {
                document.querySelectorAll('.slot-btn.active').forEach((b) => b.classList.remove('active'));
                button.classList.add('active');
                const people = JSON.parse(button.dataset.staff);
                form.elements.starts_at.value = button.dataset.startsAt;
                document.getElementById('selection-staff-inputs').replaceChildren(...people.map((p) => Object.assign(document.createElement('input'), {type: 'hidden', name: 'staff[]', value: p.public_id})));
                document.getElementById('selection-time').textContent  = button.dataset.time;
                document.getElementById('selection-staff').textContent = people.map((p) => p.name).join(', ');
                // what the people booked do not serve, to be acknowledged
                const lines = people.flatMap((p) => p.restrictions.map((line) => (people.length > 1 ? p.name + ': ' : '') + line));
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
