<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use NumberFormatter;
use RuntimeException;

class Business extends BaseController
{

    /**
     * The business's main page as the system builds it, from LINK_INTERNAL_API v1/mart/site (cached by the API): the
     * hero from the business's website settings, and its best sellers, trending and highest-rated artifacts
     * @param string $businessSlug
     * @return string
     */
    public function systemHome(string $businessSlug): string
    {
        $locale = $this->request->getLocale();
        $site   = $this->site($businessSlug, $locale);
        $business = $site['business'];
        $currency = (string) $business['currency_code'];
        $cards    = fn (array $list): array => array_map(fn (array $card): array => $this->card($businessSlug, $card, $currency, $locale), $list);
        $featured = [
            'best_sellers'  => $cards($site['featured']['best_sellers'] ?? []),
            'trending'      => $cards($site['featured']['trending'] ?? []),
            'highest_rated' => $cards($site['featured']['highest_rated'] ?? []),
        ];

        return view('systemHome', $this->layout($site, $businessSlug, $locale) + [
            'page_title'       => $site['meta']['title'],
            'business_name'    => $site['meta']['title'] === $business['name'] ? null : $business['name'],
            'meta_description' => $site['meta']['description'] ?? $business['description'],
            'meta_keywords'    => $site['meta']['keywords'],
            'site'             => $site,
            'featured'         => $featured,
            'hero_tiles'       => $this->heroTiles($featured),
            'carousel'         => $this->unique(array_merge($featured['best_sellers'], $featured['trending'], $featured['highest_rated'])),
            'indicators'       => $this->indicators($business, $locale),
        ]);
    }

    public function productInfo(string $businessSlug, string $artifactPublicId): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
            'page_title' => '[product name]'
        ];
        return view('productInfo', $data);
    }

    /**
     * A service's page, from LINK_INTERNAL_API v1/mart/artifact: its pictures, details, options and reviews, and
     * the button that searches for what can be booked of the chosen variant: free slots of an appointment (booking
     * type A, at /{business}/search-slots) or sessions of a group service (G, at /{business}/search-sessions), each
     * with ?variant={variant public_id}. A product's public_id is sent to its own page.
     * @param string $businessSlug
     * @param string $artifactPublicId
     * @return string|ResponseInterface
     */
    public function serviceInfo(string $businessSlug, string $artifactPublicId): string|ResponseInterface
    {
        $locale = $this->request->getLocale();
        $site   = $this->site($businessSlug, $locale);
        [$code, $body] = $this->fetchApi('v1/mart/artifact', ['business_slug' => $businessSlug, 'public_id' => $artifactPublicId, 'lang' => $locale]);
        if ($code === 404 || $code === 400) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($code !== 200 || ! is_array($body['data'] ?? null)) {
            throw new RuntimeException(lang('Business.load-failed'));
        }
        $data     = $body['data'];
        $artifact = $data['artifact'];
        if ($artifact['type'] !== 'S') {
            return redirect()->to(base_url(rawurlencode($businessSlug) . '/product/' . rawurlencode($artifact['public_id'])), 301);
        }
        $currency = (string) $site['business']['currency_code'];
        $variants = $this->serviceVariants($businessSlug, $data, $currency, $locale);
        $plain    = trim(preg_replace('/\s+/u', ' ', (string) ($artifact['description'] ?? '')));

        return view('serviceInfo', $this->layout($site, $businessSlug, $locale) + [
            'page_title'       => $artifact['name'],
            'business_name'    => $site['business']['name'],
            'meta_description' => $plain !== '' ? mb_strimwidth($plain, 0, 160, '…') : $site['meta']['description'],
            'meta_keywords'    => $site['meta']['keywords'],
            'site'             => $site,
            'artifact'         => $artifact,
            'summary'          => mb_strlen($plain) > self::SUMMARY_LENGTH ? mb_strimwidth($plain, 0, self::SUMMARY_LENGTH, '…') : $plain,
            'summary_cut'      => mb_strlen($plain) > self::SUMMARY_LENGTH,
            'images'           => $data['images'],
            'options'          => $data['options'],
            'variants'         => $variants,
            'reviews'          => array_map(fn (array $review): array => $review + ['date' => $this->date((string) $review['created_at'], $locale)], $data['reviews']),
        ]);
    }

    public function serviceSessions(string $businessSlug): string
    {
        $locale           = $this->request->getLocale();
        $artifactPublicId = $this->request->getGet('variant');
        $site   = $this->site($businessSlug, $locale);
        $data             = [
            'locale' => $locale,
            'page_title' => '[search sessions] - ' . $artifactPublicId,
            'meta_description' => '',
            'meta_keywords'    => '',
            'site'             => $site,
            'css' => listDefaultThemeVariables()
        ];
        return view('serviceSessions', $data);
    }

    public function serviceSlots(string $businessSlug): string
    {
        $locale           = $this->request->getLocale();
        $artifactPublicId = $this->request->getGet('variant');
        $site   = $this->site($businessSlug, $locale);
        $data             = [
            'locale' => $locale,
            'page_title' => '[search slots] - ' . $artifactPublicId,
            'meta_description' => '',
            'meta_keywords'    => '',
            'site'             => $site,
            'css' => listDefaultThemeVariables()
        ];
        return view('serviceSlots', $data);
    }

    public function cart(string $businessSlug): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('cart', $data);
    }

    public function checkout(string $businessSlug): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('checkout', $data);
    }

    public function checkOrderStatus(string $businessSlug, string $orderPublicId): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('checkOrderStatus', $data);
    }

    public function myOrderHistory(string $businessSlug, string $customerPublicId): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('myOrderHistory', $data);
    }

    public function showBusinessPages(string $businessSlug, string $pagePublicId): string
    {
        $locale = $this->request->getLocale();
        return 'Page for ' . $pagePublicId;
    }

    public function ownHome(string $businessSlug): string
    {
        $ownHome = null;
        if (!is_null($ownHome)) {
            return $this->showBusinessPages($businessSlug, $ownHome);
        }
        return $this->systemHome($businessSlug);
    }

    /** API */

    public function searchSessions(string $businessSlug): ResponseInterface
    {
        return $this->response->setJSON([]);
    }

    public function searchSlots(string $businessSlug): ResponseInterface
    {
        return $this->response->setJSON([]);
    }

    public function addToCart(string $businessSlug): ResponseInterface
    {
        return $this->response->setJSON([]);
    }

    public function removeFromCart(string $businessSlug): ResponseInterface
    {
        return $this->response->setJSON([]);
    }

    public function updateCart(string $businessSlug): ResponseInterface
    {
        return $this->response->setJSON([]);
    }

    public function clearCart(string $businessSlug): ResponseInterface
    {
        return $this->response->setJSON([]);
    }

    public function checkoutInit(string $businessSlug): ResponseInterface
    {
        return $this->response->setJSON([]);
    }

    public function checkoutConfirm(string $businessSlug): ResponseInterface
    {
        return $this->response->setJSON([]);
    }

    public function retrieveOrderStatus(string $businessSlug): ResponseInterface
    {
        return $this->response->setJSON([]);
    }

    public function retrieveOrderHistory(string $businessSlug): ResponseInterface
    {
        return $this->response->setJSON([]);
    }

    /** Characters of the description shown beside the pictures before "Read more" */
    private const SUMMARY_LENGTH = 220;

    /**
     * v1/mart/site for the business, which every page needs for its layout
     * @param string $businessSlug
     * @param string $locale
     * @return array
     * @throws PageNotFoundException When there is no such business
     */
    private function site(string $businessSlug, string $locale): array
    {
        [$code, $body] = $this->fetchApi('v1/mart/site', ['business_slug' => $businessSlug, 'lang' => $locale]);
        if ($code === 404 || $code === 400) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($code !== 200 || ! is_array($body['data'] ?? null)) {
            throw new RuntimeException(lang('Business.load-failed'));
        }

        return $body['data'];
    }

    /**
     * What the layout needs on every page of the business: its theme and the tracker's business
     * @param array  $site
     * @param string $businessSlug
     * @param string $locale
     * @return array
     */
    private function layout(array $site, string $businessSlug, string $locale): array
    {
        [$css, $fontsUrl] = $this->theme($site['theme'], $locale);

        return ['locale' => $locale, 'business_slug' => $businessSlug, 'css' => $css, 'google_fonts_url' => $fontsUrl];
    }

    /**
     * The active variants of a service as the page's picker uses them, in their order
     * @param string $businessSlug
     * @param array  $data     v1/mart/artifact
     * @param string $currency
     * @param string $locale
     * @return list<array{public_id: string, values: array<string, string>, label: string, price: string,
     *               compare: string|null, save: string|null, discount: int|null, details: list<string>,
     *               search_url: string, search_label: string, image: string|null}>
     */
    private function serviceVariants(string $businessSlug, array $data, string $currency, string $locale): array
    {
        $images = [];
        foreach ($data['images'] as $image) {
            if ($image['variant_public_id'] !== null) {
                $images[$image['variant_public_id']] ??= $image['url'];
            }
        }
        $valueNames = [];
        foreach ($data['options'] as $option) {
            foreach ($option['values'] as $value) {
                $valueNames[$value['public_id']] = $value['value'];
            }
        }

        return array_map(function (array $variant) use ($businessSlug, $currency, $locale, $images, $valueNames): array {
            $active   = (float) $variant['price']['active'];
            $compare  = $variant['price']['compare'] === null ? null : (float) $variant['price']['compare'];
            $onSale   = $compare !== null && $compare > $active;
            $group    = $variant['booking_type'] === 'G';
            $details  = [];
            if ($variant['duration_minutes'] !== null) {
                $details[] = lang('Business.service.minutes', [(int) $variant['duration_minutes']]);
            }
            $details[] = $group
                ? ($variant['capacity'] === null ? lang('Business.service.group') : lang('Business.service.group-of', [(int) $variant['capacity']]))
                : lang('Business.service.private');
            $values = [];
            foreach ($variant['attribute_values'] as $pair) {
                $values[$pair['attribute_public_id']] = $pair['value_public_id'];
            }
            $names = array_values(array_filter(array_map(static fn (string $id): ?string => $valueNames[$id] ?? null, $values)));

            return [
                'public_id'    => $variant['public_id'],
                'values'       => $values,
                // for a service whose variants have no options to tell them apart
                'label'        => implode(' · ', array_merge($names, $details)),
                'price'        => $this->money($active, $currency, $locale),
                'compare'      => $onSale ? $this->money($compare, $currency, $locale) : null,
                'save'         => $onSale ? lang('Business.service.save', [$this->money($compare - $active, $currency, $locale)]) : null,
                'discount'     => $onSale ? (int) round(100 * ($compare - $active) / $compare) : null,
                'details'      => $details,
                'search_url'   => base_url(rawurlencode($businessSlug) . '/' . ($group ? 'search-sessions' : 'search-slots')) . '?variant=' . rawurlencode($variant['public_id']),
                'search_label' => lang($group ? 'Business.service.search-sessions' : 'Business.service.search-slots'),
                'image'        => $images[$variant['public_id']] ?? null,
            ];
        }, $data['variants']);
    }

    /** "2026-11-10 09:30:00" as the locale writes a date */
    private function date(string $datetime, string $locale): string
    {
        $time = strtotime($datetime);
        if ($time === false) {
            return $datetime;
        }
        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE);

        return (string) $formatter->format($time);
    }

    /** artifact_master.artifact_label => its badge on a card */
    private const LABELS = ['N' => 'new', 'P' => 'popular', 'R' => 'recommended', 'L' => 'limited'];

    /**
     * A card of the API (v1/mart/artifacts) as the templates show it
     * @param string $businessSlug
     * @param array  $card
     * @param string $currency ISO 4217
     * @param string $locale
     * @return array{public_id: string, url: string, name: string, category: string|null, image: string|null,
     *               price: string, compare: string|null, from: bool, discount: int|null, label: string|null,
     *               rating: string|null, rating_count: int}
     */
    private function card(string $businessSlug, array $card, string $currency, string $locale): array
    {
        $active   = (float) $card['price']['active'];
        $compare  = $card['price']['compare'] === null ? null : (float) $card['price']['compare'];
        $discount = $compare !== null && $compare > $active && $compare > 0 ? (int) round(100 * ($compare - $active) / $compare) : null;

        return [
            'public_id'    => $card['public_id'],
            'url'          => base_url(rawurlencode($businessSlug) . '/' . ($card['type'] === 'S' ? 'service' : 'product') . '/' . rawurlencode($card['public_id'])),
            'name'         => $card['name'],
            'category'     => $card['category']['name'] ?? null,
            'image'        => $card['cover_url'],
            'price'        => $this->money($active, $currency, $locale),
            'compare'      => $discount === null ? null : $this->money($compare, $currency, $locale),
            'from'         => (bool) $card['price']['varies'],
            'discount'     => $discount ?: null,
            'label'        => isset(self::LABELS[$card['label'] ?? '']) ? lang('Business.label.' . self::LABELS[$card['label']]) : null,
            'rating'       => (int) $card['rating']['count'] > 0 ? number_format((float) $card['rating']['average'], 1) : null,
            'rating_count' => (int) $card['rating']['count'],
        ];
    }

    /**
     * The hero's three tiles: the first best seller, the first trending and the first highest-rated, each only once;
     * where a list has none, the next of the others fills its place
     * @param array<string, list<array>> $featured
     * @return list<array{card: array, badge: string, accent: bool}>
     */
    private function heroTiles(array $featured): array
    {
        $badges = ['best_sellers' => 'best-seller', 'trending' => 'trending', 'highest_rated' => 'highest-rated'];
        $tiles  = [];
        $used   = [];
        // first one of each list, then whatever is left, so three places are filled while there are cards
        foreach ([0, 1, 2] as $round) {
            foreach ($badges as $list => $badge) {
                $card = $featured[$list][$round] ?? null;
                if ($card === null || isset($used[$card['public_id']]) || count($tiles) === 3) {
                    continue;
                }
                $used[$card['public_id']] = true;
                $tiles[]                  = ['card' => $card, 'badge' => lang('Business.badge.' . $badge), 'accent' => $list === 'trending'];
            }
        }

        return $tiles;
    }

    /**
     * @param list<array> $cards
     * @return list<array> the first of each public_id
     */
    private function unique(array $cards): array
    {
        $unique = [];
        foreach ($cards as $card) {
            $unique[$card['public_id']] ??= $card;
        }

        return array_values($unique);
    }

    /**
     * The CSS variables of the layout and the Google Fonts URL: the business's colours, and its fonts when its
     * language has all three, else the marketplace's own fonts for the locale
     * @param array  $theme v1/mart/site's theme
     * @param string $locale
     * @return array{0: array{fonts: array, colors: array}, 1: string|null} [css, Google Fonts URL; null for the layout's own]
     */
    private function theme(array $theme, string $locale): array
    {
        $css = listDefaultThemeVariables($locale);
        foreach ((array) ($theme['colors'] ?? []) as $column => $color) {
            $variable = '--' . str_replace('_', '-', $column);
            if (isset($css['colors'][$variable]) && preg_match('/^#[0-9a-fA-F]{6}$/', (string) $color)) {
                $css['colors'][$variable] = $color;
            }
        }
        $fonts = (array) ($theme['fonts'] ?? []);
        if (! isset($fonts['heading']['name'], $fonts['nav']['name'], $fonts['default']['name']) || ($theme['google_fonts_url'] ?? null) === null) {
            return [$css, null];
        }
        foreach (['heading', 'nav', 'default'] as $role) {
            // a family name only: letters, digits and spaces, so it cannot break out of the style block
            $name = preg_replace('/[^\p{L}\p{N} ]/u', '', (string) $fonts[$role]['name']);
            $css['fonts']['--' . $role . '-font'] = '"' . $name . '", system-ui, -apple-system, sans-serif';
        }

        return [$css, $theme['google_fonts_url']];
    }

    /**
     * What the hero says about the business for customers to know before they come: its rating, who it is for,
     * and what is paid besides the prices
     * @param array  $business v1/mart/site's business
     * @param string $locale
     * @return list<array{0: string, 1: string}> [Bootstrap icon, text]
     */
    private function indicators(array $business, string $locale): array
    {
        $indicators = [];
        if ((int) $business['rating']['count'] > 0) {
            $indicators[] = ['bi-star-fill', number_format((float) $business['rating']['average'], 1) . ' · ' . lang('Business.reviews', [(int) $business['rating']['count']])];
        }
        if ($business['audience']['age_restricted']) {
            $indicators[] = ['bi-person-exclamation', lang('Business.adults-only')];
        }
        $lgbt = (string) $business['audience']['lgbt'];
        if ($lgbt !== 'GEN' && lang('Business.lgbt.' . $lgbt) !== 'Business.lgbt.' . $lgbt) {
            $indicators[] = ['bi-heart', lang('Business.lgbt.' . $lgbt)];
        }
        $naturist = (string) $business['audience']['naturist'];
        if ($naturist !== 'GEN' && lang('Business.naturist.' . $naturist) !== 'Business.naturist.' . $naturist) {
            $indicators[] = ['bi-sun', lang('Business.naturist.' . $naturist)];
        }

        $currency = (string) $business['currency_code'];
        $amount   = fn (array $charge): string => $charge['unit'] === 'F'
            ? $this->money((float) $charge['value'], $currency, $locale)
            : rtrim(rtrim(number_format((float) $charge['value'], 2, '.', ''), '0'), '.') . '%';
        // tips the customer pays at checkout; off-system and disabled ones are not the page's to tell
        $tip = $business['charges']['tip'];
        if (in_array($tip['policy'], ['O', 'M'], true)) {
            $key  = 'Business.tip.' . ($tip['policy'] === 'M' ? 'required' : 'optional');
            $text = (float) $tip['value'] > 0
                ? lang($key . '-' . ($tip['kind'] === 'M' ? 'minimum' : 'suggested'), [$amount($tip)])
                : lang($key);
            $indicators[] = ['bi-cash-coin', $text];
        }
        $service = $business['charges']['service_charge'];
        if ((float) $service['value'] > 0) {
            $indicators[] = ['bi-receipt', lang($service['unit'] === 'F' ? 'Business.service-charge-fixed' : 'Business.service-charge', [$amount($service)])];
        }

        return $indicators;
    }

    private function money(float $amount, string $currency, string $locale): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $formatted = $formatter->formatCurrency($amount, $currency);

        return $formatted === false ? $currency . ' ' . number_format($amount, 2) : $formatted;
    }
}
