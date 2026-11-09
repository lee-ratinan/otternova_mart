<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Business extends BaseController
{

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

    public function systemHome(string $businessSlug): string
    {
        $locale = $this->request->getLocale();
        $data   = [
            'locale' => $locale,
        ];
        return view('systemHome', $data);
    }

    public function showBusinessPages(string $businessSlug, string $pagePublicId): string
    {
        $locale = $this->request->getLocale();
        return 'Page for ' . $pagePublicId;
    }

    public function ownHome(string $businessSlug, string $pagePublicId): string
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

}