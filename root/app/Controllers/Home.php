<?php

namespace App\Controllers;

class Home extends BaseController
{

    /**
     * OtterNovaMart's own home page: what the mart is, what customers can do at a shop, how to get back to an
     * order, and a link to the Otternova site for business owners
     * TODO: a list of featured shops, once the API can list the businesses that chose to be listed
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
     * Search result, don't do it now, wait until there are more businesses so we can get customers to search
     * TODO: implement the search feature when there are more businesses onboarded
     * @return string
     */
    public function searchResults(): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
            'slug'   => 'search',
            'css'    => listDefaultThemeVariables()
        ];
        return view('searchResults', $data);
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
