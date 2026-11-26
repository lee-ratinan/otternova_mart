<?php

namespace App\Controllers;

use CodeIgniter\Exceptions\PageNotFoundException;

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
     * Terms of Service page: the marketplace's version in effect, or the earlier one named by $label
     * (/terms-of-service/v/2025-12-01)
     * @param string $label
     * @return string
     */
    public function termsOfService(string $label = ''): string
    {
        return $this->legalPage('T', 'terms-of-service', $label);
    }

    /**
     * Privacy policy page: the marketplace's version in effect, or the earlier one named by $label
     * @param string $label
     * @return string
     */
    public function privacyPolicy(string $label = ''): string
    {
        return $this->legalPage('P', 'privacy-policy', $label);
    }

    /**
     * A Terms of Service or Privacy Policy page of the marketplace (audience M), from LINK_INTERNAL_API
     * v1/legal/document: one version in the customer's language where it has one, cut into its sections, and every
     * version that was in effect. A label the API does not know is not found; when the API cannot be reached the
     * page says so and links to the OtterNova site's copy.
     * @param string $type  T or P
     * @param string $slug  terms-of-service or privacy-policy
     * @param string $label '' for the version in effect
     * @return string
     */
    private function legalPage(string $type, string $slug, string $label): string
    {
        $locale = $this->request->getLocale();
        [$code, $body] = $this->fetchApi('v1/legal/document', array_filter(['type' => $type, 'label' => $label, 'audience' => 'M'], 'strlen'));
        if ($code === 404 && $label !== '') {
            throw PageNotFoundException::forPageNotFound();
        }
        $document = $code === 200 && is_array($body['data']['document'] ?? null) ? $body['data']['document'] : null;
        $versions = $document === null ? [] : (array) ($body['data']['versions'] ?? []);
        if ($document === null) {
            log_message('error', 'Legal document ' . $type . ' not loaded: HTTP ' . $code);
        }

        return view('legalDocument', [
            'locale'   => $locale,
            'slug'     => $slug,
            'css'      => listDefaultThemeVariables($locale),
            'document' => $document,
            'sections' => $document === null ? [] : self::legalSections((string) ($document['body'] ?? '')),
            'versions' => $versions,
            'www_url'  => rtrim((string) (getenv('LINK_WWW') ?: 'https://otternova.com/'), '/') . '/' . $slug,
        ]);
    }

    /**
     * A document's Markdown cut at its "## " headings, each a card of the page: the text before the first heading
     * comes first, with no heading. A heading's own number ("## 3. Payments") is left out, as the card numbers itself.
     * @param string $markdown
     * @return list<array{heading: string|null, body: string}>
     */
    public static function legalSections(string $markdown): array
    {
        $sections = [['heading' => null, 'body' => '']];
        $fenced   = false;
        foreach (preg_split('/\r\n|\n|\r/', $markdown) as $line) {
            if (preg_match('/^\s*(```|~~~)/', $line)) {
                $fenced = ! $fenced;
            }
            if (! $fenced && preg_match('/^##\s+(.+?)\s*#*\s*$/', $line, $match)) {
                $sections[] = ['heading' => preg_replace('/^\d+[.)]\s+/', '', $match[1]), 'body' => ''];
                continue;
            }
            $sections[count($sections) - 1]['body'] .= $line . "\n";
        }
        foreach ($sections as $i => $section) {
            $sections[$i]['body'] = trim($section['body']);
        }

        return array_values(array_filter($sections, static fn (array $s): bool => $s['heading'] !== null || $s['body'] !== ''));
    }
}
