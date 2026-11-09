<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('home', $data);
    }

    public function searchResults(): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('searchResults', $data);
    }

    public function termsOfService(): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('termsOfService', $data);
    }

    public function privacyPolicy(): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('privacyPolicy', $data);
    }
}
