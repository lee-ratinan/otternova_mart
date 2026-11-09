<?php

use App\Filters\LocaleFilter;

if (! function_exists('locale_switch_url')) {
    /**
     * URL of the current page with `?hl=<locale>` set, for the language dropdown.
     * Other query parameters are kept.
     */
    function locale_switch_url(string $locale): string
    {
        helper('url');

        // Clone: current_url(true) returns the request's own URI object.
        return (string) (clone current_url(true))
            ->addQuery(LocaleFilter::QUERY_KEY, $locale);
    }
}
