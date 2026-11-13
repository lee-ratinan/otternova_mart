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

    /**
     * A product's page, from LINK_INTERNAL_API v1/mart/artifact: its pictures, details, options, specifications and
     * reviews, and the buttons that put the chosen variant in the cart (POST /{business}/add-to-cart) or buy it at once.
     * A service's public_id is sent to its own page.
     * @param string $businessSlug
     * @param string $artifactPublicId
     * @return string|ResponseInterface
     */
    public function productInfo(string $businessSlug, string $artifactPublicId): string|ResponseInterface
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
        if ($artifact['type'] === 'S') {
            return redirect()->to(base_url(rawurlencode($businessSlug) . '/service/' . rawurlencode($artifact['public_id'])), 301);
        }
        $currency = (string) $site['business']['currency_code'];
        $variants = $this->productVariants($data, $currency, $locale);
        $plain    = trim(preg_replace('/\s+/u', ' ', (string) ($artifact['description'] ?? '')));

        return view('productInfo', $this->layout($site, $businessSlug, $locale) + [
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
            'add_to_cart_url'  => base_url(rawurlencode($businessSlug) . '/add-to-cart'),
            'checkout_url'     => base_url(rawurlencode($businessSlug) . '/checkout'),
        ]);
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

    /**
     * The sessions of a group service (a variant with booking type G) a customer can book, from LINK_INTERNAL_API
     * v1/mart/sessions: open ones with a seat left whose booking has not closed. A customer books a whole session,
     * every meeting of it. The page is driven by its query string, so a customer can change any part of it and share
     * the link:
     * - variant:   the variant's public_id (required)
     * - store:     a store's public_id; every store when not given
     * - staff:     a person's public_id; anyone when not given
     * - date_from, date_to: Y-m-d, the days a session starts on (one lasting days is found by its first)
     * A filter the API turns down is dropped and said why.
     * @param string $businessSlug
     * @return string
     */
    public function serviceSessions(string $businessSlug): string
    {
        $locale = $this->request->getLocale();
        $site   = $this->site($businessSlug, $locale);
        $query  = ['business_slug' => $businessSlug, 'lang' => $locale, 'variant' => (string) $this->request->getGet('variant')];
        foreach (['store', 'staff', 'date_from', 'date_to'] as $name) {
            $value = $this->request->getGet($name);
            if (is_string($value) && trim($value) !== '') {
                $query[$name] = trim($value);
            }
        }
        [$code, $body] = $this->fetchApi('v1/mart/sessions', $query);
        $notice        = null;
        if ($code === 422) {
            $errors = is_array($body['errors'] ?? null) ? $body['errors'] : [];
            $notice = implode(' ', array_map('strval', $errors));
            // a person of another store goes with the store
            $query = array_diff_key($query, $errors + (isset($errors['store']) ? ['staff' => true] : []));
            [$code, $body] = $this->fetchApi('v1/mart/sessions', $query);
        }
        if ($code === 404 || $code === 400 || $code === 422) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($code !== 200 || ! is_array($body['data'] ?? null)) {
            throw new RuntimeException(lang('Business.load-failed'));
        }

        $data     = $body['data'];
        $service  = $data['service'];
        $variant  = $service['variant'];
        $currency = (string) $site['business']['currency_code'];
        $stores   = [];
        foreach ($data['stores'] as $store) {
            $stores[$store['public_id']] = $store + ['address_text' => $this->address($store)];
        }
        $staff = [];
        foreach ($data['staff'] as $person) {
            $staff[$person['public_id']] = $person + ['restriction_lines' => $this->restrictionLines($person['restrictions'])];
        }
        $store   = isset($query['store'], $stores[$query['store']]) ? $stores[$query['store']] : null;
        $chosen  = isset($query['staff'], $staff[$query['staff']]) ? $staff[$query['staff']] : null;
        $filters = ['variant' => $variant['public_id'], 'store' => $store['public_id'] ?? null, 'staff' => $chosen['public_id'] ?? null,
            'date_from' => $query['date_from'] ?? null, 'date_to' => $query['date_to'] ?? null];
        $url     = static fn (array $change): string => base_url(rawurlencode($businessSlug) . '/search-sessions') . '?'
            . http_build_query(array_filter(array_merge($filters, $change), static fn ($value): bool => $value !== null && $value !== ''));
        $price   = $variant['price'] ?? null;
        $details = [];
        if ($variant['duration_minutes'] !== null) {
            $details[] = lang('Business.service.minutes', [(int) $variant['duration_minutes']]);
        }
        if ($variant['capacity'] !== null) {
            $details[] = lang('Business.sessions.capacity', [(int) $variant['capacity']]);
        }

        $sessions = [];
        foreach ($data['sessions'] as $session) {
            $place      = $stores[$session['store_public_id']] ?? null;
            $sessions[] = [
                'public_id'       => $session['public_id'],
                'name'            => $session['name'],
                'description'     => $session['description'],
                'dates'           => $this->span($session['starts_at'], $session['ends_at'], $locale),
                'closes'          => $this->moment($session['booking_closes_at'], $locale),
                'seats_available' => (int) $session['seats_available'],
                'capacity'        => (int) $session['capacity'],
                'store'           => $place === null ? null : ['public_id' => $place['public_id'], 'name' => $place['name'], 'address' => $place['address_text'],
                    'phone_number' => $place['phone_number']],
                'staff'           => array_values(array_map(static fn (string $id): array => ['name' => $staff[$id]['name'], 'restrictions' => $staff[$id]['restriction_lines']],
                    array_filter($session['staff'], static fn (string $id): bool => isset($staff[$id])))),
                'slots'           => array_map(fn (array $slot): array => [
                    'number' => (int) $slot['slot_number'],
                    'when'   => $this->span($slot['starts_at'], $slot['ends_at'], $locale),
                    'staff'  => implode(', ', array_filter(array_map(static fn (string $id): ?string => $staff[$id]['name'] ?? null, $slot['staff']))),
                ], $session['slots']),
            ];
        }

        return view('serviceSessions', $this->layout($site, $businessSlug, $locale) + [
            'page_title'       => lang('Business.slots.title', [$service['name']]),
            'business_name'    => $site['business']['name'],
            'meta_description' => $site['meta']['description'],
            'meta_keywords'    => $site['meta']['keywords'],
            'site'             => $site,
            'service'          => $service + [
                'url'     => base_url(rawurlencode($businessSlug) . '/service/' . rawurlencode($service['public_id'])),
                'label'   => $variant['label'],
                'details' => $details,
                'price'   => $price === null ? null : $this->money((float) $price['active'], $currency, $locale),
                'compare' => $price !== null && $price['compare'] !== null && (float) $price['compare'] > (float) $price['active']
                    ? $this->money((float) $price['compare'], $currency, $locale) : null,
            ],
            'variant'          => $variant,
            'stores'           => array_values($stores),
            'store'            => $store,
            'staff'            => array_values($staff),
            'chosen_staff'     => $chosen,
            'date_from'        => $query['date_from'] ?? null,
            'date_to'          => $query['date_to'] ?? null,
            'sessions'         => $sessions,
            'notice'           => $notice,
            'url'              => $url,
        ]);
    }

    /**
     * The times an appointment (a variant with booking type A) can be booked at, from LINK_INTERNAL_API v1/mart/slots,
     * at one store on one day of the booking window the business set (booking_horizon_days and the notice). The page
     * is driven by its query string, so a customer can change any part of it and share the link:
     * - variant: the variant's public_id (required)
     * - store:   the store's public_id; the API picks the first one when not given
     * - date:    Y-m-d, a day of the store's window; the first day with a free time when not given
     * - staff:   a person's public_id; anyone when not given
     * Each time comes with whom it books; a person's restrictions are shown when they are chosen, and with the time
     * picked. A filter the API turns down (a day outside the window, say) is dropped and said why.
     * @param string $businessSlug
     * @return string
     */
    public function serviceSlots(string $businessSlug): string
    {
        $locale = $this->request->getLocale();
        $site   = $this->site($businessSlug, $locale);
        $query  = ['business_slug' => $businessSlug, 'lang' => $locale, 'variant' => (string) $this->request->getGet('variant')];
        foreach (['store', 'date', 'staff'] as $name) {
            $value = $this->request->getGet($name);
            if (is_string($value) && trim($value) !== '') {
                $query[$name] = trim($value);
            }
        }
        [$code, $body] = $this->fetchApi('v1/mart/slots', $query);
        $notice        = null;
        if ($code === 422) {
            $errors = is_array($body['errors'] ?? null) ? $body['errors'] : [];
            $notice = implode(' ', array_map('strval', $errors));
            // a person of another store goes with the store; a date outside its window too
            $query = array_diff_key($query, $errors + (isset($errors['store']) ? ['staff' => true, 'date' => true] : []));
            [$code, $body] = $this->fetchApi('v1/mart/slots', $query);
        }
        if ($code === 404 || $code === 400 || $code === 422) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($code !== 200 || ! is_array($body['data'] ?? null)) {
            throw new RuntimeException(lang('Business.load-failed'));
        }

        $data     = $body['data'];
        $service  = $data['service'];
        $variant  = $service['variant'];
        $currency = (string) $site['business']['currency_code'];
        $stores   = [];
        foreach ($data['stores'] as $store) {
            $stores[$store['public_id']] = $store + ['address_text' => $this->address($store)];
        }
        $staff = [];
        foreach ($data['staff'] as $person) {
            $staff[$person['public_id']] = $person + ['restriction_lines' => $this->restrictionLines($person['restrictions'])];
        }
        $store   = $stores[$data['store']] ?? null;
        $chosen  = isset($query['staff'], $staff[$query['staff']]) ? $staff[$query['staff']] : null;
        $filters = ['variant' => $variant['public_id'], 'store' => $data['store'], 'date' => $data['date'], 'staff' => $chosen['public_id'] ?? null];
        $url     = static fn (array $change): string => base_url(rawurlencode($businessSlug) . '/search-slots') . '?'
            . http_build_query(array_filter(array_merge($filters, $change), static fn ($value): bool => $value !== null && $value !== ''));
        $price   = $variant['price'] ?? null;
        $details = [lang('Business.service.minutes', [(int) $variant['duration_minutes']])];
        if ((int) $variant['staff_required'] > 1) {
            $details[] = lang('Business.slots.staff-required', [(int) $variant['staff_required']]);
        }

        $periods = [];
        foreach ($data['slots'] as $slot) {
            $hour   = (int) substr($slot['time'], 0, 2);
            $people = array_values(array_filter(array_map(static fn (string $id): ?array => $staff[$id] ?? null, $slot['staff'])));
            $periods[$hour < 12 ? 'morning' : ($hour < 17 ? 'afternoon' : 'evening')][] = [
                'starts_at' => $slot['starts_at'],
                'time'      => $slot['time'],
                'end_time'  => $slot['end_time'],
                'staff'     => array_map(static fn (array $p): array => ['public_id' => $p['public_id'], 'name' => $p['name'], 'restrictions' => $p['restriction_lines']], $people),
            ];
        }

        return view('serviceSlots', $this->layout($site, $businessSlug, $locale) + [
            'page_title'       => lang('Business.slots.title', [$service['name']]),
            'business_name'    => $site['business']['name'],
            'meta_description' => $site['meta']['description'],
            'meta_keywords'    => $site['meta']['keywords'],
            'site'             => $site,
            'service'          => $service + [
                'url'     => base_url(rawurlencode($businessSlug) . '/service/' . rawurlencode($service['public_id'])),
                'label'   => $variant['label'],
                'details' => $details,
                'price'   => $price === null ? null : $this->money((float) $price['active'], $currency, $locale),
                'compare' => $price !== null && $price['compare'] !== null && (float) $price['compare'] > (float) $price['active']
                    ? $this->money((float) $price['compare'], $currency, $locale) : null,
            ],
            'variant'          => $variant,
            'policy'           => $this->bookingPolicy($data['rule']),
            'window'           => $data['window'],
            'date'             => $data['date'],
            'date_label'       => $data['date'] === null ? null : $this->day($data['date'], $locale, 'EEEE d MMMM y'),
            'days'             => $data['window'] === null ? [] : $this->dayStrip($data['window'], $data['date'], $locale, $url),
            'stores'           => array_values($stores),
            'store'            => $store,
            'staff'            => array_values($staff),
            'chosen_staff'     => $chosen,
            'periods'          => $periods,
            'slot_count'       => count($data['slots']),
            'notice'           => $notice,
            'url'              => $url,
        ]);
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

    /** Stock at or below which the page says how many are left */
    private const LOW_STOCK = 5;

    /** Most of a variant a customer can put in the cart at once when no store keeps count of it */
    private const MAX_QUANTITY = 99;

    /**
     * The active variants of a product as the page's picker uses them, in their order
     * @param array  $data     v1/mart/artifact
     * @param string $currency
     * @param string $locale
     * @return list<array{public_id: string, values: array<string, string>, label: string, price: string,
     *               compare: string|null, save: string|null, discount: int|null, sku: string|null,
     *               weight: string|null, stock: int|null, max: int, status: string, status_class: string,
     *               image: string|null}>
     */
    private function productVariants(array $data, string $currency, string $locale): array
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
        $number = new NumberFormatter($locale, NumberFormatter::DECIMAL);

        return array_map(function (array $variant) use ($currency, $locale, $images, $valueNames, $number): array {
            $active  = (float) $variant['price']['active'];
            $compare = $variant['price']['compare'] === null ? null : (float) $variant['price']['compare'];
            $onSale  = $compare !== null && $compare > $active;
            // null: no store keeps count, so it is never out of stock
            $stock   = $variant['stock'] === null ? null : max(0, (int) $variant['stock']);
            $weight  = $variant['weight_grams'] === null ? null : (int) $variant['weight_grams'];
            $values  = [];
            foreach ($variant['attribute_values'] as $pair) {
                $values[$pair['attribute_public_id']] = $pair['value_public_id'];
            }
            $names = array_values(array_filter(array_map(static fn (string $id): ?string => $valueNames[$id] ?? null, $values)));
            if ($stock === 0) {
                [$status, $class] = [lang('Business.product.out-of-stock'), 'out'];
            } elseif ($stock !== null && $stock <= self::LOW_STOCK) {
                [$status, $class] = [lang('Business.product.low-stock', [$stock]), 'low'];
            } else {
                [$status, $class] = [lang('Business.product.in-stock'), 'in'];
            }

            return [
                'public_id'    => $variant['public_id'],
                'values'       => $values,
                // for a product whose variants have no options to tell them apart
                'label'        => $names !== [] ? implode(' · ', $names) : ($variant['sku'] ?? $this->money($active, $currency, $locale)),
                'price'        => $this->money($active, $currency, $locale),
                'compare'      => $onSale ? $this->money($compare, $currency, $locale) : null,
                'save'         => $onSale ? lang('Business.service.save', [$this->money($compare - $active, $currency, $locale)]) : null,
                'discount'     => $onSale ? (int) round(100 * ($compare - $active) / $compare) : null,
                'sku'          => $variant['sku'] ?: null,
                'weight'       => $weight === null ? null : ($weight < 1000
                    ? lang('Business.product.grams', [$number->format($weight)])
                    : lang('Business.product.kilograms', [$number->format(round($weight / 1000, 2))])),
                'stock'        => $stock,
                'max'          => $stock ?? self::MAX_QUANTITY,
                'status'       => $status,
                'status_class' => $class,
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

    /** A store's address on one line, or null when it has none */
    private function address(array $store): ?string
    {
        $address = $store['address'] ?? [];
        $parts   = array_filter([$address['line_1'] ?? null, $address['line_2'] ?? null, trim(($address['city'] ?? '') . ' ' . ($address['postal_code'] ?? ''))],
            static fn ($part): bool => $part !== null && trim((string) $part) !== '');

        return $parts === [] ? null : implode(', ', $parts);
    }

    /**
     * The customers a person does not serve, as a customer reads it
     * @param array|null $restrictions {excluded_genders: [M|F|NB], age_min, age_max}
     * @return list<string> None for no restriction
     */
    private function restrictionLines(?array $restrictions): array
    {
        if ($restrictions === null) {
            return [];
        }
        $lines   = [];
        $genders = array_map(static fn (string $gender): string => lang('Business.slots.gender.' . $gender), $restrictions['excluded_genders'] ?? []);
        if ($genders !== []) {
            $lines[] = lang('Business.slots.not-serving', [implode(', ', $genders)]);
        }
        $min = $restrictions['age_min'] ?? null;
        $max = $restrictions['age_max'] ?? null;
        if ($min !== null && $max !== null) {
            $lines[] = lang('Business.slots.age-between', [(int) $min, (int) $max]);
        } elseif ($min !== null) {
            $lines[] = lang('Business.slots.age-from', [(int) $min]);
        } elseif ($max !== null) {
            $lines[] = lang('Business.slots.age-up-to', [(int) $max]);
        }

        return $lines;
    }

    /**
     * The booking rule as a customer reads it: how far ahead, and how much notice
     * @param array $rule v1/mart/slots rule
     * @return list<string>
     */
    private function bookingPolicy(array $rule): array
    {
        $value  = (int) $rule['notice_value'];
        $policy = [lang('Business.slots.horizon', [(int) $rule['horizon_days']])];
        if ($rule['notice_unit'] === 'D') {
            $policy[] = lang('Business.slots.notice-days', [$value, (string) $rule['notice_time']]);
        } elseif ($value >= 60 && $value % 60 === 0) {
            $policy[] = lang('Business.slots.notice-hours', [intdiv($value, 60)]);
        } elseif ($value > 0) {
            $policy[] = lang('Business.slots.notice-minutes', [$value]);
        }

        return $policy;
    }

    /**
     * Up to a week of days around the chosen one, within the window, each a link to its slots
     * @param array{first_date: string, last_date: string} $window
     * @param string   $date Y-m-d, the chosen day
     * @param string   $locale
     * @param callable $url  array of query changes => the page's URL
     * @return array{days: list<array{date: string, weekday: string, day: string, month: string, url: string, active: bool}>, previous: string|null, next: string|null, first: string, last: string}
     */
    private function dayStrip(array $window, string $date, string $locale, callable $url): array
    {
        $first = new \DateTimeImmutable($window['first_date']);
        $last  = new \DateTimeImmutable($window['last_date']);
        $chosen = new \DateTimeImmutable($date);
        // the chosen day in the middle where the window allows
        $start = max($first, min($chosen->modify('-3 days'), $last->modify('-6 days')));
        $days  = [];
        for ($day = $start; $day <= $last && count($days) < 7; $day = $day->modify('+1 day')) {
            $ymd    = $day->format('Y-m-d');
            $days[] = [
                'date'    => $ymd,
                'weekday' => $this->day($ymd, $locale, 'EEE'),
                'day'     => $this->day($ymd, $locale, 'd'),
                'month'   => $this->day($ymd, $locale, 'MMM'),
                'url'     => $url(['date' => $ymd]),
                'active'  => $ymd === $date,
            ];
        }
        $previous = max($first, $chosen->modify('-7 days'));
        $next     = min($last, $chosen->modify('+7 days'));

        return [
            'days'     => $days,
            'previous' => $chosen > $first ? $url(['date' => $previous->format('Y-m-d')]) : null,
            'next'     => $chosen < $last ? $url(['date' => $next->format('Y-m-d')]) : null,
            'first'    => $window['first_date'],
            'last'     => $window['last_date'],
        ];
    }

    /**
     * When something runs, from two ISO 8601 datetimes with the store's offset, on the store's clock: "Fri 20 Nov 2026,
     * 09:00–12:00" on one day, "Wed 18 Nov 2026, 09:00 – Wed 25 Nov 2026, 12:00" across days
     */
    private function span(string $startsAt, ?string $endsAt, string $locale): string
    {
        $start = new \DateTimeImmutable($startsAt);
        $from  = $this->clock($start, $locale, 'EEE d MMM y, HH:mm');
        if ($endsAt === null) {
            return $from;
        }
        $end = new \DateTimeImmutable($endsAt);

        return $start->format('Y-m-d') === $end->format('Y-m-d')
            ? $from . '–' . $end->format('H:i')
            : $from . ' – ' . $this->clock($end, $locale, 'EEE d MMM y, HH:mm');
    }

    /** An ISO 8601 datetime with the store's offset, in words on the store's clock */
    private function moment(?string $at, string $locale): ?string
    {
        return $at === null ? null : $this->clock(new \DateTimeImmutable($at), $locale, 'EEE d MMM y, HH:mm');
    }

    /** A moment by an ICU pattern, at its own offset */
    private function clock(\DateTimeImmutable $at, string $locale, string $pattern): string
    {
        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::NONE, \IntlDateFormatter::NONE, $at->getTimezone(), null, $pattern);

        return (string) $formatter->format($at);
    }

    /** A Y-m-d date in the locale's words, by an ICU pattern ("EEE": Mon) */
    private function day(string $date, string $locale, string $pattern): string
    {
        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::NONE, \IntlDateFormatter::NONE, 'UTC', null, $pattern);

        return (string) $formatter->format(new \DateTimeImmutable($date, new \DateTimeZone('UTC')));
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
