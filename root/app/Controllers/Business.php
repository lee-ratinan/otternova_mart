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
        [$code, $body] = $this->fetchApi('v1/mart/site', ['business_slug' => $businessSlug, 'lang' => $locale]);
        if ($code === 404 || $code === 400) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($code !== 200 || ! is_array($body['data'] ?? null)) {
            throw new RuntimeException(lang('Business.load-failed'));
        }
        $site     = $body['data'];
        $business = $site['business'];
        $currency = (string) $business['currency_code'];
        $cards    = fn (array $list): array => array_map(fn (array $card): array => $this->card($businessSlug, $card, $currency, $locale), $list);
        $featured = [
            'best_sellers'  => $cards($site['featured']['best_sellers'] ?? []),
            'trending'      => $cards($site['featured']['trending'] ?? []),
            'highest_rated' => $cards($site['featured']['highest_rated'] ?? []),
        ];
        [$css, $fontsUrl] = $this->theme($site['theme'], $locale);

        return view('systemHome', [
            'locale'           => $locale,
            'page_title'       => $site['meta']['title'],
            'business_name'    => $site['meta']['title'] === $business['name'] ? null : $business['name'],
            'business_slug'    => $businessSlug,
            'meta_description' => $site['meta']['description'] ?? $business['description'],
            'meta_keywords'    => $site['meta']['keywords'],
            'css'              => $css,
            'google_fonts_url' => $fontsUrl,
            'site'             => $site,
            'featured'         => $featured,
            'hero_tiles'       => $this->heroTiles($featured),
            'carousel'         => $this->unique(array_merge($featured['best_sellers'], $featured['trending'], $featured['highest_rated'])),
        ]);
    }

    public function productInfo(string $businessSlug, string $artifactPublicId): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('productInfo', $data);
    }

    public function serviceInfo(string $businessSlug, string $artifactPublicId): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('serviceInfo', $data);
    }

    public function serviceSessions(string $businessSlug, string $artifactPublicId): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('serviceSessions', $data);
    }

    public function serviceSlots(string $businessSlug, string $artifactPublicId): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
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

    private function money(float $amount, string $currency, string $locale): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $formatted = $formatter->formatCurrency($amount, $currency);

        return $formatted === false ? $currency . ' ' . number_format($amount, 2) : $formatted;
    }
}
