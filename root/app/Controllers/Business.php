<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use NumberFormatter;
use RuntimeException;

class Business extends BaseController
{
    /** listSupportedSubdivisions() */
    protected $helpers = ['list'];


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
            // a time or session the cart turned down comes back here with why
            'notice'           => session()->getFlashdata('cart_error') ?? $notice,
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
            // a time or session the cart turned down comes back here with why
            'notice'           => session()->getFlashdata('cart_error') ?? $notice,
            'url'              => $url,
        ]);
    }

    /**
     * The customer's cart at this business, kept in their session (Business::cartLines()): what each line is, its
     * price when it was put in, how many, and for a booking the names of the people it is for
     * @param string $businessSlug
     * @return string
     */
    public function cart(string $businessSlug): string
    {
        $locale   = $this->request->getLocale();
        $site     = $this->site($businessSlug, $locale);
        $currency = (string) $site['business']['currency_code'];
        $base     = base_url(rawurlencode($businessSlug));

        return view('cart', $this->layout($site, $businessSlug, $locale) + [
            'page_title'       => lang('Business.cart.title'),
            'business_name'    => $site['business']['name'],
            'meta_description' => $site['meta']['description'],
            'meta_keywords'    => $site['meta']['keywords'],
            'site'             => $site,
            'lines'            => $this->cartViews($businessSlug, $currency, $locale),
            'summary'          => $this->cartSummary($businessSlug, $currency, $locale),
            'message'          => session()->getFlashdata('cart_message'),
            'error'            => session()->getFlashdata('cart_error'),
            'problems'         => session()->getFlashdata('cart_problems') ?? [],
            'urls'             => [
                'home'     => $base,
                'update'   => $base . '/update-cart',
                'remove'   => $base . '/remove-from-cart',
                'clear'    => $base . '/clear-cart',
                'checkout' => $base . '/checkout-init',
            ],
        ]);
    }

    /**
     * The checkout: the customer's details, then how the products get to them (only when there are any), then the
     * discount code and how to pay, beside what it all costs. Everything is priced by v1/mart/quote, never by the
     * cart's own prices; a cart that can no longer be had as it is goes back to the cart page, each line with why.
     * @param string $businessSlug
     * @return string|ResponseInterface
     */
    public function checkout(string $businessSlug): string|ResponseInterface
    {
        $locale = $this->request->getLocale();
        $site   = $this->site($businessSlug, $locale);
        $lines  = $this->cartLines($businessSlug);
        if ($lines === []) {
            return redirect()->to($this->businessUrl($businessSlug, 'cart'));
        }
        $state = $this->checkoutState($businessSlug);
        [$code, $quote] = $this->quoteCart($businessSlug, $lines, $state['choice'], $locale);
        if ($code !== 200) {
            return redirect()->to($this->businessUrl($businessSlug, 'cart'))->with('cart_error', lang('Business.cart.try-again'));
        }
        if (($back = $this->backToCart($businessSlug, $lines, $quote)) !== null) {
            return $back;
        }
        $this->dropBadDiscount($businessSlug, $quote);
        $state    = $this->checkoutState($businessSlug);
        $currency = (string) $site['business']['currency_code'];
        // a checkout starts once for what the cart holds, however often the page is loaded
        $events  = [];
        $cartKey = md5(json_encode(array_map(static fn (array $l): array => [$l['id'], $l['quantity']], $lines)));
        $started = session()->get('checkout_started') ?? [];
        if (($started[$businessSlug] ?? null) !== $cartKey) {
            $started[$businessSlug] = $cartKey;
            session()->set('checkout_started', $started);
            $events[] = ['checkout_start', [
                'items'    => array_sum(array_column($quote['lines'], 'quantity')),
                'value'    => (float) $quote['totals']['total'],
                'currency' => (string) $quote['currency'],
            ]];
        }

        return view('checkout', $this->layout($site, $businessSlug, $locale) + [
            'tracker_events'   => $events,
            'page_title'       => lang('Business.checkout.title'),
            'business_name'    => $site['business']['name'],
            'meta_description' => $site['meta']['description'],
            'meta_keywords'    => $site['meta']['keywords'],
            'site'             => $site,
            'state'            => $state,
            'checkout'         => $this->checkoutView($quote, $lines, $currency, $locale),
            'errors'           => session()->getFlashdata('checkout_errors') ?? [],
            'error'            => session()->getFlashdata('checkout_error'),
            'notices'          => session()->getFlashdata('checkout_notices') ?? [],
            'urls'             => [
                'cart'    => $this->businessUrl($businessSlug, 'cart'),
                'quote'   => $this->businessUrl($businessSlug, 'checkout-quote'),
                'confirm' => $this->businessUrl($businessSlug, 'checkout-confirm'),
            ],
        ]);
    }

    /**
     * An order's payment: what to do to pay it, by its payment method's handler (a PromptPay QR with the amount in it,
     * or the business's instructions), and what was ordered. Only for the browser that placed it, which keeps its key.
     * @param string $businessSlug
     * @param string $orderNumber the business's number of the order (IN2611-0001); its public_id is never shown
     * @return string
     */
    public function payment(string $businessSlug, string $orderNumber): string
    {
        $locale = $this->request->getLocale();
        $site   = $this->site($businessSlug, $locale);
        $key    = session()->get('orders')[$businessSlug][$orderNumber] ?? null;
        if (! is_string($key)) {
            throw PageNotFoundException::forPageNotFound();
        }
        [$code, $body] = $this->fetchApi('v1/mart/order', ['business_slug' => $businessSlug, 'lang' => $locale, 'number' => $orderNumber, 'key' => $key]);
        if ($code === 404) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($code !== 200 || ! is_array($body['data']['order'] ?? null)) {
            throw new RuntimeException(lang('Business.load-failed'));
        }
        $order    = $body['data']['order'];
        $currency = (string) $order['currency'];

        return view('payment', $this->layout($site, $businessSlug, $locale) + [
            'page_title'       => lang('Business.payment.title'),
            'business_name'    => $site['business']['name'],
            'meta_description' => $site['meta']['description'],
            'meta_keywords'    => $site['meta']['keywords'],
            'site'             => $site,
            'order'            => $this->orderView($order, $currency, $locale),
            'just_placed'      => session()->getFlashdata('order_placed') === $orderNumber,
            'home_url'         => $this->businessUrl($businessSlug, ''),
        ]);
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

    /**
     * Puts one thing in the cart, after the API says it can still be had. What it is comes from the fields sent:
     * - a group session (booking type G): variant, session, and acknowledged=1 when its people do not serve everyone;
     *   one seat, or one more seat when the session is in the cart already
     * - an appointment (booking type A): variant, store, starts_at (as v1/mart/slots wrote it), staff[] (whom the
     *   time books), and acknowledged=1 when they do not serve everyone; refused when it is in the cart already, or
     *   when one of those people is booked in the cart at an overlapping time
     * - a product: artifact, variant, quantity (1 when not given), added to what of the variant the cart has, within
     *   its stock; intent=buy sends a form post on to the checkout
     * A request with X-Requested-With: XMLHttpRequest is answered in JSON ({ok, message, line, summary}; 422 when it is
     * turned down, 503 when the API cannot be reached), a form post by a redirect to the cart, or back with the reason.
     * @param string $businessSlug
     * @return ResponseInterface
     */
    public function addToCart(string $businessSlug): ResponseInterface
    {
        $locale = $this->request->getLocale();
        $site   = $this->site($businessSlug, $locale);
        $lines  = $this->cartLines($businessSlug);
        $before = array_column($lines, 'quantity', 'id');
        try {
            $variant = $this->postId('variant');
            if ($this->postText('session') !== '') {
                $index = $this->addSessionLine($businessSlug, $lines, $variant, $this->postId('session'), $locale);
            } elseif ($this->postText('starts_at') !== '') {
                $staff = $this->request->getPost('staff');
                $staff = is_array($staff) ? array_values(array_unique(array_filter(array_map(static fn ($id): string => is_string($id) ? trim($id) : '', $staff)))) : [];
                if ($staff === [] || count($staff) > 10 || mb_strlen($this->postText('starts_at')) > 40) {
                    throw new \DomainException(lang('Business.cart.invalid'));
                }
                $index = $this->addAppointmentLine($businessSlug, $lines, $variant, $this->postId('store'), $this->postText('starts_at'), $staff, $locale);
            } else {
                $quantity = $this->postText('quantity') === '' ? 1 : $this->postQuantity();
                $index    = $this->addProductLine($businessSlug, $lines, $this->postId('artifact'), $variant, $quantity, $locale);
            }
        } catch (\DomainException|RuntimeException $e) {
            return $this->cartFailure($businessSlug, $site, $e);
        }
        $added = $lines[$index]['kind'] === 'P' ? $lines[$index]['quantity'] - ($before[$lines[$index]['id']] ?? 0) : 1;
        $this->saveCart($businessSlug, $lines);
        $buy   = $this->postText('intent') === 'buy';
        $event = ['add_to_cart', [
            'artifact' => $lines[$index]['artifact'],
            'variant'  => $lines[$index]['variant'],
            'quantity' => $added,
            'value'    => round((float) $lines[$index]['price'] * $added, 2),
            'currency' => (string) $site['business']['currency_code'],
        ]];

        return $this->cartAnswer($businessSlug, $site, lang('Business.cart.added'), $lines[$index],
            $buy ? base_url(rawurlencode($businessSlug) . '/checkout') : null, [$event]);
    }

    /**
     * Takes a line out of the cart: line (its id)
     * @param string $businessSlug
     * @return ResponseInterface
     */
    public function removeFromCart(string $businessSlug): ResponseInterface
    {
        $site  = $this->site($businessSlug, $this->request->getLocale());
        $id    = $this->postText('line');
        $lines = $this->cartLines($businessSlug);
        $kept  = array_values(array_filter($lines, static fn (array $line): bool => $line['id'] !== $id));
        if (count($kept) === count($lines)) {
            return $this->cartFailure($businessSlug, $site, new \DomainException(lang('Business.cart.line-gone')));
        }
        $this->saveCart($businessSlug, $kept);

        return $this->cartAnswer($businessSlug, $site, lang('Business.cart.removed'));
    }

    /**
     * Changes a line of the cart: line (its id), and either or both of
     * - quantity: of a product, within its stock; seats of a group session, within those left (an appointment is
     *   always one). Only more than the line had is checked with the API.
     * - participants[]: the names of whom a booking is for, one a seat; what is not sent stays as it was
     * @param string $businessSlug
     * @return ResponseInterface
     */
    public function updateCart(string $businessSlug): ResponseInterface
    {
        $locale = $this->request->getLocale();
        $site   = $this->site($businessSlug, $locale);
        $lines  = $this->cartLines($businessSlug);
        $id     = $this->postText('line');
        $index  = array_search($id, array_column($lines, 'id'), true);
        if ($index === false) {
            return $this->cartFailure($businessSlug, $site, new \DomainException(lang('Business.cart.line-gone')));
        }
        $line = $lines[$index];
        try {
            if ($this->postText('quantity') !== '' && $line['kind'] !== 'A') {
                $quantity = $this->postQuantity();
                if ($quantity > $line['quantity']) {
                    $line['max'] = $line['kind'] === 'P'
                        ? $this->productStock($businessSlug, $line['artifact'], $line['variant'], $quantity, $locale)
                        : $this->sessionSeats($businessSlug, $line['variant'], $line['session'], $quantity, $locale);
                }
                $line['quantity'] = $quantity;
            }
            $names = $this->request->getPost('participants');
            if ($line['kind'] !== 'P') {
                $line['participants'] = $this->participants(is_array($names) ? $names : $line['participants'], $line['quantity']);
            }
        } catch (\DomainException|RuntimeException $e) {
            return $this->cartFailure($businessSlug, $site, $e, $lines[$index]);
        }
        $lines[$index] = $line;
        $this->saveCart($businessSlug, $lines);

        return $this->cartAnswer($businessSlug, $site, lang('Business.cart.updated'), $line);
    }

    /**
     * Empties the cart
     * @param string $businessSlug
     * @return ResponseInterface
     */
    public function clearCart(string $businessSlug): ResponseInterface
    {
        $site = $this->site($businessSlug, $this->request->getLocale());
        $this->saveCart($businessSlug, []);

        return $this->cartAnswer($businessSlug, $site, lang('Business.cart.cleared'));
    }

    /**
     * The checkout starts: the whole cart is checked again with v1/mart/quote (stock, times, seats, prices) and every
     * participant must be named; then on to the checkout, else back to the cart with what changed
     * @param string $businessSlug
     * @return ResponseInterface
     */
    public function checkoutInit(string $businessSlug): ResponseInterface
    {
        $locale = $this->request->getLocale();
        $site   = $this->site($businessSlug, $locale);
        $lines  = $this->cartLines($businessSlug);
        $cart   = $this->businessUrl($businessSlug, 'cart');
        if ($lines === []) {
            return redirect()->to($cart);
        }
        $missing = $this->cartSummary($businessSlug, (string) $site['business']['currency_code'], $locale)['missing'];
        if ($missing > 0) {
            return redirect()->to($cart)->with('cart_error', lang('Business.cart.names-needed', [$missing]));
        }
        [$code, $quote] = $this->quoteCart($businessSlug, $lines, $this->checkoutState($businessSlug)['choice'], $locale);
        if ($code !== 200) {
            return redirect()->to($cart)->with('cart_error', lang('Business.cart.try-again'));
        }
        if (($back = $this->backToCart($businessSlug, $lines, $quote)) !== null) {
            return $back;
        }

        return redirect()->to($this->businessUrl($businessSlug, 'checkout'));
    }

    /**
     * A change on the checkout page, priced again: shipping, pickup_store, discount, payment (each kept for the page,
     * as are the customer's details and the address, sent too so a reload keeps them). JSON { ok, checkout, cart_url }:
     * ok false with cart_url when the cart can no longer be had as it is.
     * @param string $businessSlug
     * @return ResponseInterface
     */
    public function checkoutQuote(string $businessSlug): ResponseInterface
    {
        $locale = $this->request->getLocale();
        $site   = $this->site($businessSlug, $locale);
        $lines  = $this->cartLines($businessSlug);
        $state  = $this->rememberCheckout($businessSlug);
        [$code, $quote] = $lines === [] ? [200, null] : $this->quoteCart($businessSlug, $lines, $state['choice'], $locale);
        if ($code !== 200) {
            return $this->response->setStatusCode(503)->setJSON(['ok' => false, 'message' => lang('Business.cart.try-again')]);
        }
        if ($quote === null || $this->backToCart($businessSlug, $lines, $quote) !== null) {
            return $this->response->setJSON(['ok' => false, 'cart_url' => $this->businessUrl($businessSlug, 'cart')]);
        }
        $this->dropBadDiscount($businessSlug, $quote);

        return $this->response->setJSON(['ok' => true, 'checkout' => $this->checkoutView($quote, $lines, (string) $site['business']['currency_code'], $locale)]);
    }

    /**
     * Place the order (v1/mart/order) with what the checkout page sent: first_name, last_name, email, telephone;
     * shipping, pickup_store, line_1, line_2, city, subdivision_code, postal_code, pickup_point; discount; payment.
     * Placed: the cart is emptied and the browser keeps the order's key, then on to its payment. Not placed: back to
     * the checkout with why, or to the cart when a line can no longer be had.
     * @param string $businessSlug
     * @return ResponseInterface
     */
    public function checkoutConfirm(string $businessSlug): ResponseInterface
    {
        $locale   = $this->request->getLocale();
        $this->site($businessSlug, $locale);
        $lines    = $this->cartLines($businessSlug);
        $back     = $this->businessUrl($businessSlug, 'checkout');
        if ($lines === []) {
            return redirect()->to($this->businessUrl($businessSlug, 'cart'));
        }
        $state  = $this->rememberCheckout($businessSlug);
        $errors = $this->checkoutErrors($state);
        if ($errors !== []) {
            return redirect()->to($back)->with('checkout_errors', $errors);
        }

        [$code, $body] = $this->postApi('v1/mart/order', $this->quoteBody($businessSlug, $lines, $state['choice'], $locale, true) + [
            'customer'     => $state['customer'],
            'delivery'     => array_diff_key($state['delivery'], ['pickup_point' => true]),
            'pickup_point' => $state['delivery']['pickup_point'],
        ]);
        if ($code === 201 && isset($body['data']['order']['order_number'], $body['data']['order']['key'])) {
            $order  = $body['data']['order'];
            $orders = session()->get('orders') ?? [];
            // the newest few orders of each business, for their payment pages
            // by the order number, the only one the customer sees
            $orders[$businessSlug] = array_slice([$order['order_number'] => $order['key']] + ($orders[$businessSlug] ?? []), 0, 20, true);
            session()->set('orders', $orders);
            $this->saveCart($businessSlug, []);
            $this->forgetCheckout($businessSlug);

            return redirect()->to($this->businessUrl($businessSlug, 'payment/' . rawurlencode($order['order_number'])))
                ->with('order_placed', $order['order_number'])
                ->with('tracker_events', [['checkout_confirm', [
                    'order'    => $order['order_number'],
                    'items'    => array_sum(array_column($order['lines'], 'quantity')),
                    'value'    => (float) $order['totals']['total'],
                    'currency' => (string) $order['currency'],
                ]]]);
        }
        if ($code === 409 && is_array($body['data']['quote'] ?? null)) {
            if (($body['data']['reason'] ?? '') === 'problems' && ($cart = $this->backToCart($businessSlug, $lines, $body['data']['quote'], true)) !== null) {
                return $cart;
            }

            return redirect()->to($back)->with('checkout_errors', $this->apiErrors($body['errors'] ?? []));
        }
        if ($code === 422) {
            return redirect()->to($back)->with('checkout_errors', $this->apiErrors($body['errors'] ?? []));
        }

        return redirect()->to($back)->with('checkout_error', lang('Business.cart.try-again'));
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

        return [
            'locale'           => $locale,
            'business_slug'    => $businessSlug,
            'css'              => $css,
            'google_fonts_url' => $fontsUrl,
            'cart'             => $this->cartFlyout($businessSlug, (string) $site['business']['currency_code'], $locale),
        ];
    }

    /**
     * What the header's cart dropdown shows: every line, the totals, and where its links and remove buttons go
     * @return array{lines: list<array>, summary: array, urls: array{cart: string, checkout: string, remove: string}}
     */
    private function cartFlyout(string $businessSlug, string $currency, string $locale): array
    {
        $base = base_url(rawurlencode($businessSlug));

        return [
            'lines'   => $this->cartViews($businessSlug, $currency, $locale),
            'summary' => $this->cartSummary($businessSlug, $currency, $locale),
            'urls'    => ['cart' => $base . '/cart', 'checkout' => $base . '/checkout-init', 'remove' => $base . '/remove-from-cart'],
        ];
    }

    /** Every line of the cart as the pages show it */
    private function cartViews(string $businessSlug, string $currency, string $locale): array
    {
        return array_map(fn (array $line): array => $this->cartView($line, $currency, $locale), $this->cartLines($businessSlug));
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

    /** Lines a cart holds at most */
    private const CART_LINES = 50;

    /** Characters of a participant's name */
    private const NAME_LENGTH = 100;

    /**
     * The cart of a business in the customer's session, one per business
     * @param string $businessSlug
     * @return list<array> lines: {id, kind: P|A|G, artifact, variant, name, image, url, tags: list<string>, price,
     *                     compare, quantity, max (stock or seats left at the last check; null: no limit), and
     *                     A: store, starts_at, ends_at, staff: list<string>; G: session, starts_at, ends_at;
     *                     A and G: participants: list<string>, one a seat}
     */
    private function cartLines(string $businessSlug): array
    {
        $carts = session()->get('carts');

        return array_values(is_array($carts[$businessSlug] ?? null) ? $carts[$businessSlug] : []);
    }

    private function saveCart(string $businessSlug, array $lines): void
    {
        $carts = session()->get('carts');
        $carts = is_array($carts) ? $carts : [];
        if ($lines === []) {
            unset($carts[$businessSlug]);
        } else {
            $carts[$businessSlug] = array_values($lines);
        }
        session()->set('carts', $carts);
    }

    /**
     * A product's line put in the cart, or the quantity added to the line of its variant
     * @return int the line's index
     * @throws \DomainException|RuntimeException
     */
    private function addProductLine(string $businessSlug, array &$lines, string $artifactId, string $variantId, int $quantity, string $locale): int
    {
        $data    = $this->cartArtifact($businessSlug, $artifactId, $locale);
        $variant = current(array_filter($data['variants'], static fn (array $v): bool => $v['public_id'] === $variantId)) ?: null;
        if ($variant === null || $data['artifact']['type'] === 'S') {
            throw new \DomainException(lang('Business.cart.not-found'));
        }
        $index = null;
        foreach ($lines as $i => $line) {
            if ($line['kind'] === 'P' && $line['variant'] === $variantId) {
                $index = $i;
            }
        }
        $inCart = $index === null ? 0 : (int) $lines[$index]['quantity'];
        $total  = $quantity + $inCart;
        $max    = $this->stockFor($variant, $total, $inCart);
        $tags  = [];
        foreach ($data['options'] as $option) {
            foreach ($option['values'] as $value) {
                if (in_array(['attribute_public_id' => $option['public_id'], 'value_public_id' => $value['public_id']], $variant['attribute_values'], false)) {
                    $tags[] = $option['name'] . ': ' . $value['value'];
                }
            }
        }
        $image = null;
        foreach ($data['images'] as $picture) {
            if ($picture['variant_public_id'] === $variantId) {
                $image = $picture['url'];
                break;
            }
        }
        $line = [
            'id'       => $index === null ? bin2hex(random_bytes(6)) : $lines[$index]['id'],
            'kind'     => 'P',
            'artifact' => $data['artifact']['public_id'],
            'variant'  => $variantId,
            'name'     => $data['artifact']['name'],
            'image'    => $image ?? $data['images'][0]['url'] ?? $data['artifact']['cover_url'] ?? null,
            'url'      => base_url(rawurlencode($businessSlug) . '/product/' . rawurlencode($data['artifact']['public_id'])),
            'tags'     => $tags,
            'price'    => $variant['price']['active'],
            'compare'  => $variant['price']['compare'],
            'quantity' => $total,
            'max'      => $max,
        ];

        return $this->placeLine($lines, $line, $index);
    }

    /**
     * An appointment's line put in the cart, while v1/mart/slots still has its time free with the same people
     * @param list<string> $staff
     * @return int the line's index
     * @throws \DomainException|RuntimeException
     */
    private function addAppointmentLine(string $businessSlug, array &$lines, string $variantId, string $storeId, string $startsAt, array $staff, string $locale): int
    {
        $query = ['business_slug' => $businessSlug, 'lang' => $locale, 'variant' => $variantId, 'store' => $storeId, 'date' => substr($startsAt, 0, 10)];
        if (count($staff) === 1) {
            $query['staff'] = $staff[0];
        }
        [$code, $body] = $this->fetchApi('v1/mart/slots', $query);
        if (in_array($code, [400, 404, 422], true)) {
            throw new \DomainException(lang('Business.cart.slot-taken'));
        }
        if ($code !== 200 || ! is_array($body['data'] ?? null)) {
            throw new RuntimeException(lang('Business.load-failed'));
        }
        $data   = $body['data'];
        $wanted = $staff;
        sort($wanted);
        $slot = null;
        foreach ($data['slots'] as $free) {
            $people = $free['staff'];
            sort($people);
            if ($free['starts_at'] === $startsAt && $free['store_public_id'] === $storeId && $people === $wanted) {
                $slot = $free;
                break;
            }
        }
        if ($slot === null) {
            throw new \DomainException(lang('Business.cart.slot-taken'));
        }
        $people = array_values(array_filter($data['staff'], static fn (array $person): bool => in_array($person['public_id'], $staff, true)));
        $this->requireAcknowledged($people);
        foreach ($lines as $line) {
            if ($line['kind'] !== 'A') {
                continue;
            }
            if ($line['variant'] === $variantId && $line['starts_at'] === $startsAt && $line['staff'] === $wanted) {
                throw new \DomainException(lang('Business.cart.already'));
            }
            $shared = array_intersect($line['staff'], $wanted);
            if ($shared !== [] && strtotime($line['starts_at']) < strtotime($slot['ends_at']) && strtotime($slot['starts_at']) < strtotime($line['ends_at'])) {
                $names = array_column(array_filter($people, static fn (array $p): bool => in_array($p['public_id'], $shared, true)), 'name');
                throw new \DomainException(lang('Business.cart.clash', [implode(', ', $names)]));
            }
        }
        $service = $data['service'];
        $store   = current(array_filter($data['stores'], static fn (array $s): bool => $s['public_id'] === $storeId)) ?: null;
        $line    = [
            'id'           => bin2hex(random_bytes(6)),
            'kind'         => 'A',
            'artifact'     => $service['public_id'],
            'variant'      => $variantId,
            'name'         => $service['name'],
            'image'        => $service['cover_url'] ?? null,
            'url'          => base_url(rawurlencode($businessSlug) . '/service/' . rawurlencode($service['public_id'])),
            'tags'         => array_values(array_filter([
                $service['variant']['label'] ?? '',
                $store === null ? '' : $store['name'],
                lang('Business.cart.with', [implode(', ', array_column($people, 'name'))]),
            ], static fn (string $tag): bool => $tag !== '')),
            'price'        => $service['variant']['price']['active'],
            'compare'      => $service['variant']['price']['compare'],
            'quantity'     => 1,
            'max'          => 1,
            'store'        => $storeId,
            'starts_at'    => $slot['starts_at'],
            'ends_at'      => $slot['ends_at'],
            'staff'        => $wanted,
            'participants' => [''],
        ];

        return $this->placeLine($lines, $line, null);
    }

    /**
     * A seat of a group session put in the cart, or one more seat on its line, while v1/mart/sessions has it open
     * with a seat left
     * @return int the line's index
     * @throws \DomainException|RuntimeException
     */
    private function addSessionLine(string $businessSlug, array &$lines, string $variantId, string $sessionId, string $locale): int
    {
        $index = null;
        foreach ($lines as $i => $line) {
            if ($line['kind'] === 'G' && $line['session'] === $sessionId) {
                $index = $i;
            }
        }
        $seats = 1 + ($index === null ? 0 : (int) $lines[$index]['quantity']);
        [$data, $session] = $this->bookableSession($businessSlug, $variantId, $sessionId, $locale);
        $this->seatsFor($session, $seats);
        if ($index === null) {
            $this->requireAcknowledged(array_values(array_filter($data['staff'], static fn (array $person): bool => in_array($person['public_id'], $session['staff'], true))));
        }
        $service = $data['service'];
        $store   = current(array_filter($data['stores'], static fn (array $s): bool => $s['public_id'] === $session['store_public_id'])) ?: null;
        $line    = [
            'id'           => $index === null ? bin2hex(random_bytes(6)) : $lines[$index]['id'],
            'kind'         => 'G',
            'artifact'     => $service['public_id'],
            'variant'      => $variantId,
            'name'         => $service['name'],
            'image'        => $service['cover_url'] ?? null,
            'url'          => base_url(rawurlencode($businessSlug) . '/service/' . rawurlencode($service['public_id'])),
            'tags'         => array_values(array_filter([
                $service['variant']['label'] ?? '',
                (string) $session['name'],
                $store === null ? '' : $store['name'],
            ], static fn (string $tag): bool => $tag !== '')),
            'price'        => $service['variant']['price']['active'],
            'compare'      => $service['variant']['price']['compare'],
            'quantity'     => $seats,
            'max'          => (int) $session['seats_available'],
            'session'      => $sessionId,
            'starts_at'    => $session['starts_at'],
            'ends_at'      => $session['ends_at'],
            'participants' => $this->participants($index === null ? [] : $lines[$index]['participants'], $seats),
        ];

        return $this->placeLine($lines, $line, $index);
    }

    /**
     * @return int the line's index
     * @throws \DomainException When a new line finds the cart full
     */
    private function placeLine(array &$lines, array $line, ?int $index): int
    {
        if ($index !== null) {
            $lines[$index] = $line;

            return $index;
        }
        if (count($lines) >= self::CART_LINES) {
            throw new \DomainException(lang('Business.cart.cart-full'));
        }
        $lines[] = $line;

        return count($lines) - 1;
    }

    /**
     * v1/mart/artifact for a line
     * @throws \DomainException|RuntimeException
     */
    private function cartArtifact(string $businessSlug, string $artifactId, string $locale): array
    {
        [$code, $body] = $this->fetchApi('v1/mart/artifact', ['business_slug' => $businessSlug, 'public_id' => $artifactId, 'lang' => $locale]);
        if ($code === 404 || $code === 400) {
            throw new \DomainException(lang('Business.cart.not-found'));
        }
        if ($code !== 200 || ! is_array($body['data'] ?? null)) {
            throw new RuntimeException(lang('Business.load-failed'));
        }

        return $body['data'];
    }

    /**
     * The stock of a product's variant, checked for a quantity
     * @return int|null the stock; null when no store keeps count of it
     * @throws \DomainException|RuntimeException
     */
    private function productStock(string $businessSlug, string $artifactId, string $variantId, int $quantity, string $locale): ?int
    {
        $data    = $this->cartArtifact($businessSlug, $artifactId, $locale);
        $variant = current(array_filter($data['variants'], static fn (array $v): bool => $v['public_id'] === $variantId)) ?: null;
        if ($variant === null) {
            throw new \DomainException(lang('Business.cart.not-found'));
        }

        return $this->stockFor($variant, $quantity);
    }

    /**
     * @param array $variant  v1/mart/artifact's
     * @param int   $quantity what the cart would hold
     * @param int   $inCart   what it holds already, for the reason
     * @return int|null its stock; null when no store keeps count of it
     * @throws \DomainException When there is not enough of it
     */
    private function stockFor(array $variant, int $quantity, int $inCart = 0): ?int
    {
        $stock = $variant['stock'] === null ? null : max(0, (int) $variant['stock']);
        if ($stock === 0) {
            throw new \DomainException(lang('Business.cart.out-of-stock'));
        }
        if ($quantity > ($stock ?? self::MAX_QUANTITY)) {
            throw new \DomainException($inCart > 0
                ? lang('Business.cart.only-left-in-cart', [$stock ?? self::MAX_QUANTITY, $inCart])
                : lang('Business.cart.only-left', [$stock ?? self::MAX_QUANTITY]));
        }

        return $stock;
    }

    /**
     * A session v1/mart/sessions still has to book: open, with a seat left, and its booking not closed
     * @return array{0: array, 1: array} [the answer's data, the session]
     * @throws \DomainException|RuntimeException
     */
    private function bookableSession(string $businessSlug, string $variantId, string $sessionId, string $locale): array
    {
        [$code, $body] = $this->fetchApi('v1/mart/sessions', ['business_slug' => $businessSlug, 'lang' => $locale, 'variant' => $variantId]);
        if (in_array($code, [400, 404, 422], true)) {
            throw new \DomainException(lang('Business.cart.session-gone'));
        }
        if ($code !== 200 || ! is_array($body['data'] ?? null)) {
            throw new RuntimeException(lang('Business.load-failed'));
        }
        foreach ($body['data']['sessions'] as $session) {
            if ($session['public_id'] === $sessionId) {
                return [$body['data'], $session];
            }
        }

        throw new \DomainException(lang('Business.cart.session-gone'));
    }

    /**
     * The seats left of a session, checked for a number of seats
     * @throws \DomainException|RuntimeException
     */
    private function sessionSeats(string $businessSlug, string $variantId, string $sessionId, int $seats, string $locale): int
    {
        return $this->seatsFor($this->bookableSession($businessSlug, $variantId, $sessionId, $locale)[1], $seats);
    }

    /**
     * @throws \DomainException When the session has fewer seats left
     */
    private function seatsFor(array $session, int $seats): int
    {
        $left = (int) $session['seats_available'];
        if ($left < 1) {
            throw new \DomainException(lang('Business.cart.session-full'));
        }
        if ($seats > $left) {
            throw new \DomainException(lang('Business.cart.seats-left', [$left]));
        }

        return $left;
    }

    /**
     * Whom a booking holds that do not serve everyone must have been told to the customer, who ticks acknowledged
     * @param list<array> $people {restrictions}
     * @throws \DomainException
     */
    private function requireAcknowledged(array $people): void
    {
        foreach ($people as $person) {
            if ($this->restrictionLines($person['restrictions'] ?? null) !== [] && $this->postText('acknowledged') !== '1') {
                throw new \DomainException(lang('Business.cart.acknowledge'));
            }
        }
    }

    /**
     * Names of the participants, one a seat: trimmed, cut to NAME_LENGTH, and as many as the seats
     * @return list<string>
     */
    private function participants(array $names, int $seats): array
    {
        $names = array_map(static fn ($name): string => is_string($name) ? mb_substr(trim(preg_replace('/\s+/u', ' ', $name)), 0, self::NAME_LENGTH) : '', array_values($names));

        return array_pad(array_slice($names, 0, $seats), $seats, '');
    }

    /** A posted field, trimmed; '' when it is missing or not text */
    private function postText(string $name): string
    {
        $value = $this->request->getPost($name);

        return is_string($value) ? trim($value) : '';
    }

    /**
     * A posted public_id
     * @throws \DomainException When it is missing or cannot be one
     */
    private function postId(string $name): string
    {
        $value = $this->postText($name);
        if (! preg_match('/^[A-Za-z0-9_-]{1,32}$/', $value)) {
            throw new \DomainException(lang('Business.cart.invalid'));
        }

        return $value;
    }

    /**
     * The posted quantity: a whole number from 1 to MAX_QUANTITY
     * @throws \DomainException
     */
    private function postQuantity(): int
    {
        $value = $this->postText('quantity');
        if (! ctype_digit($value) || (int) $value < 1 || (int) $value > self::MAX_QUANTITY) {
            throw new \DomainException(lang('Business.cart.invalid-quantity', [self::MAX_QUANTITY]));
        }

        return (int) $value;
    }

    /**
     * A line as the cart page shows it
     * @return array{id: string, kind: string, name: string, image: string|null, url: string, tags: list<string>,
     *               when: string|null, price: string, compare: string|null, discount: int|null, quantity: int,
     *               max: int, total: string, participants: list<string>|null}
     */
    private function cartView(array $line, string $currency, string $locale): array
    {
        $price   = (float) $line['price'];
        $compare = $line['compare'] === null ? null : (float) $line['compare'];
        $onSale  = $compare !== null && $compare > $price;

        return [
            'id'           => $line['id'],
            'kind'         => $line['kind'],
            'name'         => $line['name'],
            'image'        => $line['image'],
            'url'          => $line['url'],
            'tags'         => $line['tags'],
            'when'         => isset($line['starts_at']) ? $this->span($line['starts_at'], $line['ends_at'] ?? null, $locale) : null,
            'price'        => $this->money($price, $currency, $locale),
            'compare'      => $onSale ? $this->money($compare, $currency, $locale) : null,
            'discount'     => $onSale ? (int) round(100 * ($compare - $price) / $compare) : null,
            'quantity'     => (int) $line['quantity'],
            // a line may hold more than is left now; it is checked again at the checkout
            'max'          => $line['kind'] === 'A' ? 1 : max((int) $line['quantity'], min(self::MAX_QUANTITY, $line['max'] ?? self::MAX_QUANTITY)),
            'total'        => $this->money($price * (int) $line['quantity'], $currency, $locale),
            'participants' => $line['kind'] === 'P' ? null : $line['participants'],
        ];
    }

    /**
     * The cart's totals, and whether it can go to the checkout: every participant named
     * @return array{lines: int, items: int, items_label: string, subtotal: string, missing: int, missing_label: string, ready: bool}
     */
    private function cartSummary(string $businessSlug, string $currency, string $locale): array
    {
        $lines    = $this->cartLines($businessSlug);
        $items    = 0;
        $subtotal = 0.0;
        $missing  = 0;
        foreach ($lines as $line) {
            $items    += (int) $line['quantity'];
            $subtotal += (float) $line['price'] * (int) $line['quantity'];
            $missing  += count(array_filter($line['participants'] ?? [], static fn (string $name): bool => $name === ''));
        }

        return [
            'lines'         => count($lines),
            'items'         => $items,
            'items_label'   => lang('Business.cart.items', [$items]),
            'subtotal'      => $this->money($subtotal, $currency, $locale),
            'missing'       => $missing,
            'missing_label' => $missing > 0 ? lang('Business.cart.names-needed', [$missing]) : '',
            'ready'         => $lines !== [] && $missing === 0,
        ];
    }

    /**
     * What a cart endpoint did: JSON to a script, else a redirect (to the cart, or $redirect) with the message
     */
    private function cartAnswer(string $businessSlug, array $site, string $message, ?array $line = null, ?string $redirect = null, array $events = []): ResponseInterface
    {
        $locale   = $this->request->getLocale();
        $currency = (string) $site['business']['currency_code'];
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'ok'       => true,
                'message'  => $message,
                'line'     => $line === null ? null : $this->cartView($line, $currency, $locale),
                'summary'  => $this->cartSummary($businessSlug, $currency, $locale),
                'lines'    => $this->cartViews($businessSlug, $currency, $locale),
                'redirect' => $redirect,
                'track'    => $events,
            ]);
        }

        return redirect()->to($redirect ?? base_url(rawurlencode($businessSlug) . '/cart'))->with('cart_message', $message)->with('tracker_events', $events);
    }

    /**
     * Why a cart endpoint did nothing: 422 when it was turned down, 503 when the API could not be reached; a form post
     * goes back where it came from with the reason
     * @param array|null $line the line as it stays, for the page to put back
     */
    private function cartFailure(string $businessSlug, array $site, \Throwable $error, ?array $line = null): ResponseInterface
    {
        $locale   = $this->request->getLocale();
        $currency = (string) $site['business']['currency_code'];
        $message  = $error instanceof \DomainException ? $error->getMessage() : lang('Business.cart.try-again');
        if ($this->request->isAJAX()) {
            return $this->response->setStatusCode($error instanceof \DomainException ? 422 : 503)->setJSON([
                'ok'      => false,
                'message' => $message,
                'line'    => $line === null ? null : $this->cartView($line, $currency, $locale),
                'summary' => $this->cartSummary($businessSlug, $currency, $locale),
                'lines'   => $this->cartViews($businessSlug, $currency, $locale),
            ]);
        }

        // back to the page the form was on, when it is one of this site's
        $referrer = $this->request->getServer('HTTP_REFERER');
        $back     = is_string($referrer) && str_starts_with($referrer, base_url()) ? $referrer : base_url(rawurlencode($businessSlug) . '/cart');

        return redirect()->to($back)->with('cart_error', $message);
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

    // --- the checkout ---------------------------------------------------------------------------------------------

    /** What the checkout page keeps between requests, per business: the choices, the customer and the address */
    private const CHECKOUT_FIELDS = [
        'choice'   => ['shipping' => 32, 'pickup_store' => 32, 'discount' => 30, 'payment' => 32],
        'customer' => ['first_name' => 100, 'last_name' => 100, 'email' => 64, 'telephone' => 24],
        'delivery' => ['line_1' => 255, 'line_2' => 255, 'city' => 128, 'subdivision_code' => 8, 'postal_code' => 16, 'pickup_point' => 500],
    ];

    /** A page of the business */
    private function businessUrl(string $businessSlug, string $path): string
    {
        return base_url(rawurlencode($businessSlug) . ($path === '' ? '' : '/' . $path));
    }

    /** The checkout as the customer left it, every field there and a text */
    private function checkoutState(string $businessSlug): array
    {
        $stored = session()->get('checkout')[$businessSlug] ?? [];
        $state  = [];
        foreach (self::CHECKOUT_FIELDS as $group => $fields) {
            foreach ($fields as $field => $max) {
                $value                  = $stored[$group][$field] ?? '';
                $state[$group][$field] = is_string($value) ? $value : '';
            }
        }

        return $state;
    }

    /** The checkout with what was posted, kept for the next request */
    private function rememberCheckout(string $businessSlug): array
    {
        $state = $this->checkoutState($businessSlug);
        foreach (self::CHECKOUT_FIELDS as $group => $fields) {
            foreach ($fields as $field => $max) {
                $value = $this->request->getPost($field);
                if (is_string($value)) {
                    $value = $field === 'pickup_point' ? trim($value) : trim(preg_replace('/\s+/u', ' ', $value));
                    $state[$group][$field] = mb_substr($value, 0, $max);
                }
            }
        }
        $all                = session()->get('checkout') ?? [];
        $all[$businessSlug] = $state;
        session()->set('checkout', $all);

        return $state;
    }

    /** A discount code the quote turned down is not kept, so it is not sent with the order; the quote still says why */
    private function dropBadDiscount(string $businessSlug, array $quote): void
    {
        $all = session()->get('checkout') ?? [];
        if (($quote['discount_error'] ?? null) !== null && isset($all[$businessSlug]['choice']['discount'])) {
            $all[$businessSlug]['choice']['discount'] = '';
            session()->set('checkout', $all);
        }
    }

    private function forgetCheckout(string $businessSlug): void
    {
        $all = session()->get('checkout') ?? [];
        unset($all[$businessSlug]);
        session()->set('checkout', $all);
    }

    /** What the checkout page checks before sending the order: the customer's details */
    private function checkoutErrors(array $state): array
    {
        $errors = [];
        foreach ($state['customer'] as $field => $value) {
            if ($value === '') {
                $errors['customer.' . $field] = lang('Business.checkout.required');
            }
        }
        if ($state['customer']['email'] !== '' && filter_var($state['customer']['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors['customer.email'] = lang('Business.checkout.invalid-email');
        }
        if ($state['customer']['telephone'] !== '' && ! preg_match('/^\+?[0-9 ()-]{6,24}$/', $state['customer']['telephone'])) {
            $errors['customer.telephone'] = lang('Business.checkout.invalid-telephone');
        }
        if ($state['choice']['payment'] === '') {
            $errors['payment'] = lang('Business.checkout.payment-required');
        }

        return $errors;
    }

    /**
     * The body of v1/mart/quote and v1/mart/order for the cart
     * @param bool $withPeople with the names of the participants (for the order)
     */
    private function quoteBody(string $businessSlug, array $lines, array $choice, string $locale, bool $withPeople = false): array
    {
        return ['business_slug' => $businessSlug, 'lang' => $locale, 'lines' => array_map(static function (array $line) use ($withPeople): array {
            $out = ['key' => $line['id'], 'kind' => $line['kind'], 'variant' => $line['variant'], 'quantity' => (int) $line['quantity']];
            if ($line['kind'] === 'A') {
                $out += ['store' => $line['store'], 'starts_at' => $line['starts_at'], 'staff' => $line['staff']];
            } elseif ($line['kind'] === 'G') {
                $out['session'] = $line['session'];
            }
            if ($withPeople && $line['kind'] !== 'P') {
                $out['participants'] = $line['participants'];
            }

            return $out;
        }, $lines)] + $choice;
    }

    /**
     * v1/mart/quote for the cart
     * @return array{int, array|null} [http status, the quote]
     */
    private function quoteCart(string $businessSlug, array $lines, array $choice, string $locale): array
    {
        [$code, $body] = $this->postApi('v1/mart/quote', $this->quoteBody($businessSlug, $lines, $choice, $locale));
        if ($code !== 200 || ! is_array($body['data']['lines'] ?? null)) {
            return [$code === 200 ? 503 : $code, null];
        }

        return [200, $body['data']];
    }

    /**
     * The cart brought in line with a quote: each line's price as it is now (the checkout told of a change). When a
     * line can no longer be had as it is, or the products cannot be sent at all, the redirect to the cart that says
     * why; null when the checkout can go on.
     * @param bool $placing The order was refused as it was placed: the cart page says so
     */
    private function backToCart(string $businessSlug, array $lines, array $quote, bool $placing = false): ?ResponseInterface
    {
        $byKey    = array_column($quote['lines'], null, 'key');
        $problems = [];
        $notices  = [];
        $changed  = false;
        foreach ($lines as &$line) {
            $priced = $byKey[$line['id']] ?? null;
            if ($priced === null) {
                continue;
            }
            if ($priced['problem'] !== null) {
                $problems[$line['id']] = $this->problemMessage($priced['problem']);
            }
            if ((float) $priced['unit_price'] !== (float) $line['price'] || ($priced['compare_price'] === null) !== ($line['compare'] === null)) {
                $line['price']   = $priced['unit_price'];
                $line['compare'] = $priced['compare_price'];
                $changed         = true;
                $notices[]       = lang('Business.checkout.price-changed', [$line['name']]);
            }
        }
        unset($line);
        if ($changed) {
            $this->saveCart($businessSlug, $lines);
        }
        if ($problems === [] && ($quote['shipping_error'] ?? null) !== 'none') {
            if ($notices !== []) {
                session()->setFlashdata('checkout_notices', $notices);
            }

            return null;
        }

        return redirect()->to($this->businessUrl($businessSlug, 'cart'))
            ->with('cart_problems', $problems)
            ->with('cart_error', $problems === [] ? lang('Business.checkout.no-shipping') : lang($placing ? 'Business.checkout.changed-placing' : 'Business.checkout.changed'));
    }

    /** Why a line of the cart can no longer be had, from v1/mart/quote's problem */
    private function problemMessage(array $problem): string
    {
        $available = (int) ($problem['available'] ?? 0);

        return match ($problem['code'] ?? '') {
            'out_of_stock' => lang('Business.cart.out-of-stock'),
            'only_left'    => lang('Business.cart.only-left', [$available]),
            'slot_taken'   => lang('Business.cart.slot-taken'),
            'session_gone' => lang('Business.cart.session-gone'),
            'seats_left'   => $available > 0 ? lang('Business.cart.seats-left', [$available]) : lang('Business.cart.session-full'),
            'session_full' => lang('Business.cart.session-full'),
            default        => lang('Business.cart.not-found'),
        };
    }

    /** v1/mart/order's errors (field => code) as the checkout page shows them */
    private function apiErrors(array $errors): array
    {
        $out = [];
        foreach ($errors as $field => $code) {
            $field = (string) $field;
            $code  = (string) $code;
            if (str_starts_with($field, 'lines.') && str_ends_with($field, '.participants')) {
                $out['lines'] = lang('Business.checkout.names-missing');
            } elseif (str_starts_with($field, 'lines.')) {
                $out['lines'] = lang('Business.checkout.changed');
            } elseif ($field === 'shipping') {
                $out['shipping'] = $this->shippingError($code);
            } elseif ($field === 'discount') {
                $out['discount'] = $this->discountError(['code' => $code]);
            } elseif ($field === 'payment') {
                $out['payment'] = lang('Business.checkout.payment-required');
            } else {
                $out[$field] = $code === 'required' ? lang('Business.checkout.required') : lang('Business.checkout.invalid');
            }
        }

        return $out;
    }

    private function shippingError(?string $code): ?string
    {
        return match ($code) {
            null            => null,
            'pickup_store'  => lang('Business.checkout.pickup-store-required'),
            'stock'         => lang('Business.checkout.unavailable-stock'),
            'weight'        => lang('Business.checkout.unavailable-weight'),
            'none'          => lang('Business.checkout.no-shipping'),
            default         => lang('Business.checkout.shipping-required'),
        };
    }

    private function discountError(?array $error, string $currency = '', string $locale = ''): ?string
    {
        return match ($error['code'] ?? null) {
            null             => null,
            'expired'        => lang('Business.checkout.discount-expired'),
            'used_up'        => lang('Business.checkout.discount-used-up'),
            'not_applicable' => lang('Business.checkout.discount-not-applicable'),
            'min_subtotal'   => lang('Business.checkout.discount-min', [$currency === '' ? (string) $error['amount'] : $this->money((float) $error['amount'], $currency, $locale)]),
            default          => lang('Business.checkout.discount-not-found'),
        };
    }

    /**
     * v1/mart/quote as the checkout page shows it
     * @return array{lines: list<array>, needs_shipping: bool, shipping_methods: list<array>, shipping: string,
     *               pickup_store: string, shipping_error: string|null, discount: array|null, discount_error: string|null,
     *               payment_methods: list<array>, payment: string, totals: array}
     */
    private function checkoutView(array $quote, array $lines, string $currency, string $locale): array
    {
        $money  = fn (string $amount): string => $this->money((float) $amount, $currency, $locale);
        $images = array_column($lines, 'image', 'id');
        $totals = $quote['totals'];

        return [
            'lines' => array_map(fn (array $line): array => [
                'id'       => $line['key'],
                'kind'     => $line['kind'],
                'name'     => $line['name'],
                'label'    => $line['label'],
                'image'    => $images[$line['key']] ?? null,
                'quantity' => $line['quantity'],
                'total'    => $money($line['subtotal']),
                'when'     => $line['starts_at'] === null ? null : $this->span($line['starts_at'], $line['ends_at'], $locale),
                'store'    => $line['kind'] === 'P' ? null : ($line['store']['name'] ?? null),
            ], $quote['lines']),
            'needs_shipping'   => (bool) $quote['needs_shipping'],
            'shipping_methods' => array_map(fn (array $m): array => [
                'public_id'     => $m['public_id'],
                'type'          => $m['type'],
                'name'          => $m['name'],
                'description'   => $m['description'],
                'eta'           => $m['eta_days_max'] === null ? null : lang('Business.checkout.eta', [$m['eta_days_min'] ?? $m['eta_days_max'], $m['eta_days_max']]),
                'fee'           => (float) $m['fee'] === 0.0 ? lang('Business.checkout.free') : $money($m['fee']),
                'available'     => $m['available'],
                'unavailable'   => $m['available'] ? null : $this->shippingError($m['unavailable']),
                'store'         => $m['store']['name'] ?? null,
                'pickup_stores' => $m['pickup_stores'] === null ? null : array_map(fn (array $store): array => [
                    'public_id' => $store['public_id'],
                    'name'      => $store['name'],
                    'address'   => implode(', ', array_filter([$store['address']['line_1'] ?? null, $store['address']['line_2'] ?? null, $store['address']['city'] ?? null])),
                ], $m['pickup_stores']),
            ], $quote['shipping_methods']),
            'shipping'        => $quote['shipping']['public_id'] ?? '',
            'shipping_type'   => $quote['shipping']['type'] ?? null,
            'pickup_store'    => $quote['shipping']['pickup_store']['public_id'] ?? '',
            'shipping_error'  => in_array($quote['shipping_error'] ?? null, [null, 'none'], true) ? null : $this->shippingError($quote['shipping_error']),
            'discount'        => $quote['discount'] === null ? null : [
                'code'        => $quote['discount']['code'],
                'description' => $quote['discount']['description'],
                'amount'      => $money($quote['discount']['amount']),
            ],
            'discount_error'  => $this->discountError($quote['discount_error'], $currency, $locale),
            'payment_methods' => array_map(static fn (array $p): array => [
                'public_id'   => $p['public_id'],
                'name'        => $p['name'],
                'handler'     => $p['handler_code'],
                'instruction' => $p['instruction'],
            ], $quote['payment_methods']),
            'payment' => $quote['payment'] ?? '',
            'totals'  => [
                'items'        => lang('Business.cart.items', [array_sum(array_column($quote['lines'], 'quantity'))]),
                'subtotal'     => $money($totals['subtotal']),
                'discount'     => (float) $totals['discount'] > 0 ? '−' . $money($totals['discount']) : null,
                'shipping'     => ! $quote['needs_shipping'] ? null
                    : ($quote['shipping'] === null ? lang('Business.checkout.shipping-choose') : ((float) $totals['shipping'] === 0.0 ? lang('Business.checkout.free') : $money($totals['shipping']))),
                'tax_added'    => (float) $totals['tax_added'] > 0 ? $money($totals['tax_added']) : null,
                'tax_included' => (float) $totals['tax_included'] > 0 ? lang('Business.checkout.tax-included', [$money($totals['tax_included'])]) : null,
                'total'        => $money($totals['total']),
            ],
        ];
    }

    /** v1/mart/order's order as the payment page shows it */
    private function orderView(array $order, string $currency, string $locale): array
    {
        $money    = fn (?string $amount): string => $this->money((float) $amount, $currency, $locale);
        $totals   = $order['totals'];
        $shipping = $order['shipping'];
        $address  = static fn (?array $a): ?string => $a === null ? null : implode(', ', array_filter([$a['line_1'] ?? null, $a['line_2'] ?? null, $a['city'] ?? null,
            isset($a['subdivision_code']) ? (listSupportedSubdivisions(substr((string) $a['subdivision_code'], 0, 2))[$a['subdivision_code']] ?? $a['subdivision_code']) : null,
            $a['postal_code'] ?? null], static fn ($part): bool => $part !== null && $part !== ''));

        return [
            'number'    => $order['order_number'],
            'status'    => $order['status'],
            'customer'  => $order['customer'],
            'lines'     => array_map(fn (array $line): array => [
                'kind'         => $line['kind'],
                'name'         => $line['name'],
                'label'        => $line['label'],
                'quantity'     => $line['quantity'],
                'total'        => $money($line['total']),
                'when'         => $line['starts_at'] === null ? null : $this->span($line['starts_at'], $line['ends_at'], $locale),
                'store'        => $line['kind'] === 'P' ? null : ($line['store']['name'] ?? null),
                'participants' => $line['participants'],
            ], $order['lines']),
            'shipping'  => $shipping === null ? null : [
                'type'    => $shipping['type'],
                'name'    => $shipping['name'],
                'where'   => match ($shipping['type']) {
                    'H'     => $address($shipping['address']),
                    'L'     => $shipping['pickup_point'],
                    default => $shipping['pickup_store'] === null ? null : trim($shipping['pickup_store']['name'] . ', ' . $address($shipping['pickup_store']['address']), ', '),
                },
            ],
            'payment'   => $order['payment'],
            'discount_code' => $order['discount_code'],
            'totals'    => [
                'subtotal'     => $money($totals['subtotal']),
                'discount'     => (float) $totals['discount'] > 0 ? '−' . $money($totals['discount']) : null,
                'shipping'     => $shipping === null ? null : ((float) $totals['shipping'] === 0.0 ? lang('Business.checkout.free') : $money($totals['shipping'])),
                'tax_added'    => (float) $totals['tax_added'] > 0 ? $money($totals['tax_added']) : null,
                'tax_included' => (float) $totals['tax_included'] > 0 ? lang('Business.checkout.tax-included', [$money($totals['tax_included'])]) : null,
                'total'        => $money($totals['total']),
                'total_number' => $totals['total'],
            ],
        ];
    }
}
