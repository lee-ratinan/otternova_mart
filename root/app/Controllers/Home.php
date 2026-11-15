<?php

namespace App\Controllers;

class Home extends BaseController
{

    /**
     * Home page
     * TODO: Still no idea what to do
     * @return string
     */
    public function index(): string
    {
        // still no real idea what I should do with this page
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
            'slug'   => 'home',
            'css'    => listDefaultThemeVariables()
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
