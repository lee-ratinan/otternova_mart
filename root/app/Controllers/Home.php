<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // still no real idea what i should do with this page
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
            'slug'   => 'home'
        ];
        return view('home', $data);
    }

    public function searchResults(): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
            'slug'   => 'search'
        ];
        return view('searchResults', $data);
    }

    public function termsOfService(): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
            'slug'   => 'terms-of-service'
        ];
        return view('termsOfService', $data);
    }

    public function privacyPolicy(): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
            'slug'   => 'privacy-policy'
        ];
        return view('privacyPolicy', $data);
    }
}
