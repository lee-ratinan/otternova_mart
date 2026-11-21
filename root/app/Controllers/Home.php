<?php

namespace App\Controllers;

class Home extends BaseController
{

    /**
     * OtterNovaMart's own home page: what the mart is, what customers can do at a shop, how to get back to an
     * order, and a link to the OtterNova site for business owners
     * TODO: a list of featured shops, once the API can list the businesses
     * @return string
     */
    public function index(): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale'           => $locale,
            'slug'             => 'home',
            'css'              => listDefaultThemeVariables($locale),
            'meta_description' => lang('System.home.meta-description'),
            'www_url'          => rtrim((string) (getenv('LINK_WWW') ?: 'https://otternova.com/'), '/') . '/home',
        ];
        return view('home', $data);
    }

    /**
     * The businesses found for /search?q=...&page=..., by name (in any language it is named in) or by tag, from
     * LINK_INTERNAL_API v1/mart/search, best matches first, a page at a time. With no q the page asks for one; when
     * the API cannot be reached it says so, rather than that nothing was found.
     * @return string
     */
    public function searchResults(): string
    {
        $locale = $this->request->getLocale();
        $q      = $this->request->getGet('q');
        $q      = is_string($q) ? mb_substr(trim($q), 0, 100) : '';
        $page   = filter_var($this->request->getGet('page'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;

        $results = null;
        $failed  = false;
        if ($q !== '') {
            [$code, $body] = $this->fetchApi('v1/mart/search', ['q' => $q, 'lang' => $locale, 'page' => $page]);
            if ($code === 200 && is_array($body['data'] ?? null)) {
                $results = $body['data'];
            } else {
                $failed = true;
            }
        }

        return view('searchResults', [
            'locale'           => $locale,
            'slug'             => 'search',
            'css'              => listDefaultThemeVariables($locale),
            'page_title'       => $q === '' ? lang('System.page.search') : lang('System.search.title-for', [$q]),
            'meta_description' => lang('System.home.meta-description'),
            'search_query'     => $q,
            'results'          => $results,
            'failed'           => $failed,
            'page_url'         => static fn (int $n): string => base_url('search') . '?' . http_build_query(['q' => $q] + ($n > 1 ? ['page' => $n] : [])),
        ]);
    }

    /**
     * TODO: write terms
     * @return string
     */
    public function termsOfService(): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
            'slug'   => 'terms-of-service',
            'css'    => listDefaultThemeVariables()
        ];
        return view('termsOfService', $data);
    }

    /**
     * TODO: write privacy policy, also stated the collection of data for tracking/marketing purposes, completely anonymized
     * @return string
     */
    public function privacyPolicy(): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
            'slug'   => 'privacy-policy',
            'css'    => listDefaultThemeVariables()
        ];
        return view('privacyPolicy', $data);
    }
}
