<?php
/**
 * Hero 01 (Config\WebpageDesigns::$designs['hero_01'] in otternova_api): the headline and buttons on the left, a
 * picture on the right with up to three small figure cards floating over it, on a soft gradient. Drawn by
 * customizedPage.php for a section whose design_code is hero_01; the styles are the theme's (.hero).
 *
 * Text slots (texts.{slot}): badge (optional, 48), headline (120), paragraph (optional, 500, keeps its line breaks),
 * button_label (32, to the system home), button_2_label (optional, 32, to the contact page), card_1_value ..
 * card_3_value (optional, 16) and card_1_label .. card_3_label (optional, 32): a card shows when it has either.
 * Image slots (images.{slot}): picture, 1 of 1200x900.
 *
 * @var array  $section       A section of v1/mart/webpage: {public_id, section_type, design_code, texts: {slot_code:
 *                            text}, images: {slot_code: [url]}}; a text or picture it does not have is missing
 * @var string $business_slug The business, for the buttons' links
 */
$texts   = $section['texts'] ?? [];
$picture = $section['images']['picture'][0] ?? null;
$text    = static fn (string $slot): string => (string) ($texts[$slot] ?? '');
$icons   = [1 => 'bi-star-fill', 2 => 'bi-people', 3 => 'bi-clock'];
$cards   = [];
foreach ($icons as $n => $icon) {
    if ($text("card_{$n}_value") !== '' || $text("card_{$n}_label") !== '') {
        $cards[$n] = ['icon' => $icon, 'value' => $text("card_{$n}_value"), 'label' => $text("card_{$n}_label")];
    }
}
?>
<section id="section-<?= esc($section['public_id'], 'attr') ?>" class="hero section hero-01<?= $picture === null ? ' no-image' : '' ?>">
    <div class="container">
        <div class="row align-items-center">
            <div class="<?= $picture === null ? 'col-lg-8 mx-auto text-center' : 'col-lg-7' ?>" data-aos="fade-up" data-aos-delay="100">
                <div class="hero-content">
                    <?php if ($text('badge') !== ''): ?>
                        <div class="hero-badge"><span class="badge"><?= esc($text('badge')) ?></span></div>
                    <?php endif ?>
                    <?php if ($text('headline') !== ''): ?>
                        <h1><?= esc($text('headline')) ?></h1>
                    <?php endif ?>
                    <?php if ($text('paragraph') !== ''): ?>
                        <p><?= nl2br(esc($text('paragraph'))) ?></p>
                    <?php endif ?>
                    <?php if ($text('button_label') !== '' || $text('button_2_label') !== ''): ?>
                        <div class="hero-buttons<?= $picture === null ? ' justify-content-center' : '' ?>">
                            <?php if ($text('button_label') !== ''): ?>
                                <a href="<?= esc(base_url($business_slug . '/home'), 'attr') ?>" class="btn btn-primary">
                                    <span><?= esc($text('button_label')) ?></span><i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            <?php endif ?>
                            <?php if ($text('button_2_label') !== ''): ?>
                                <a href="<?= esc(base_url($business_slug . '/contact-us'), 'attr') ?>" class="btn btn-outline">
                                    <i class="bi bi-chat-dots me-2"></i><span><?= esc($text('button_2_label')) ?></span>
                                </a>
                            <?php endif ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            <?php if ($picture !== null): ?>
                <div class="col-lg-5" data-aos="fade-up" data-aos-delay="200">
                    <div class="hero-visual">
                        <div class="product-mockup">
                            <div class="mockup-frame">
                                <img src="<?= esc($picture, 'attr') ?>" alt="<?= esc($text('headline'), 'attr') ?>" class="img-fluid">
                            </div>
                            <?php foreach ($cards as $n => $card): ?>
                                <div class="floating-card card-<?= $n ?>" data-aos="fade-up" data-aos-delay="<?= 300 + $n * 100 ?>">
                                    <div class="card-icon"><i class="bi <?= $card['icon'] ?>"></i></div>
                                    <div class="card-content">
                                        <?php if ($card['value'] !== ''): ?>
                                            <div class="card-value"><?= esc($card['value']) ?></div>
                                        <?php endif ?>
                                        <?php if ($card['label'] !== ''): ?>
                                            <div class="card-label"><?= esc($card['label']) ?></div>
                                        <?php endif ?>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
    <div class="hero-background">
        <div class="gradient-blob blob-1"></div>
        <div class="gradient-blob blob-2"></div>
        <div class="grid-pattern"></div>
    </div>
</section>
