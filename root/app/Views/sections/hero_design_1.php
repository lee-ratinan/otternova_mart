<?php
/**
 * Hero design 1 (Config\WebpageDesigns::$designs['hero_design_1'] in otternova_api): a full-width picture behind
 * the badge, headline, paragraph and a button to the system home. Drawn by _layout_customized.php, once per section
 * of v1/mart/webpage whose design_code is hero_design_1, with view('sections/' . $section['design_code'], ...).
 * The styles are _layout_customized.php's, shared by every section and page; nothing here but the markup.
 *
 * Text slots (texts.{slot}): badge (optional, 48), headline (120), paragraph (optional, 500, keeps its line breaks),
 * button_label (32). Image slots (images.{slot}): background, 1 picture of 2000x1000.
 *
 * @var array  $section       A section of v1/mart/webpage: {public_id, section_type, design_code, texts: {slot_code:
 *                            text}, images: {slot_code: [url]}}; a text or picture it does not have is missing
 * @var string $business_slug The business, for the button's link to its system home
 */
$texts      = $section['texts'] ?? [];
$background = $section['images']['background'][0] ?? null;
$text       = static fn (string $slot): string => (string) ($texts[$slot] ?? '');
?>
<section id="section-<?= esc($section['public_id'], 'attr') ?>" class="section hero hero-design-1<?= $background === null ? ' no-image' : '' ?>"
    <?php if ($background !== null): ?>style="background-image: url('<?= esc($background, 'attr') ?>')"<?php endif ?>>
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content">
            <?php if ($text('badge') !== ''): ?>
                <span class="hero-badge"><?= esc($text('badge')) ?></span>
            <?php endif ?>
            <?php if ($text('headline') !== ''): ?>
                <h1 class="hero-headline"><?= esc($text('headline')) ?></h1>
            <?php endif ?>
            <?php if ($text('paragraph') !== ''): ?>
                <p class="hero-paragraph"><?= nl2br(esc($text('paragraph'))) ?></p>
            <?php endif ?>
            <?php if ($text('button_label') !== ''): ?>
                <a href="<?= esc(base_url($business_slug . '/home'), 'attr') ?>" class="hero-button"><?= esc($text('button_label')) ?></a>
            <?php endif ?>
        </div>
    </div>
</section>
