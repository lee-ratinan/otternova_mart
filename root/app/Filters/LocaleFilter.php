<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Resolves the locale for the current request.
 *
 * Priority:
 *   1. `?hl=xx` in the query string (manual switch) - also remembered in a cookie
 *   2. The remembered cookie from a previous manual switch
 *   3. Browser negotiation (Accept-Language), already done by CodeIgniter
 */
class LocaleFilter implements FilterInterface
{
    public const QUERY_KEY   = 'hl';
    public const COOKIE_NAME = 'hl';
    public const COOKIE_TTL  = 31536000; // 1 year

    public function before(RequestInterface $request, $arguments = null)
    {
        if (! $request instanceof IncomingRequest) {
            return null;
        }

        $fromQuery  = $this->match($request->getGet(self::QUERY_KEY));
        $fromCookie = $this->match($request->getCookie(self::COOKIE_NAME));
        $locale     = $fromQuery ?? $fromCookie;

        if ($locale === null) {
            // No manual choice yet - keep the negotiated locale.
            return null;
        }

        if ($fromQuery !== null && $fromQuery !== $fromCookie) {
            service('response')->setCookie(self::COOKIE_NAME, $fromQuery, self::COOKIE_TTL);
        }

        $request->setLocale($locale);
        service('language')->setLocale($locale);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    /**
     * Returns the supported locale matching $value (case-insensitive), or null.
     */
    private function match(mixed $value): ?string
    {
        if (! is_string($value) || $value === '') {
            return null;
        }

        foreach (config('App')->supportedLocales as $supported) {
            if (strcasecmp($supported, $value) === 0) {
                return $supported;
            }
        }

        return null;
    }
}
