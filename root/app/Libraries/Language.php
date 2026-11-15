<?php

namespace App\Libraries;

use CodeIgniter\Language\Language as BaseLanguage;

/**
 * The framework's Language, which also gives the business's words (App\Libraries\Terms) to the lines that name them:
 * a line with {staff_one}, {Participant_other} and the like gets them beside its own arguments, so lang() needs no
 * more than it did. Until a page sets them (useTerms(), once v1/mart/site is in), the lines get the defaults.
 * The words are arguments, never part of the pattern, so nothing in them can break a line's format.
 */
class Language extends BaseLanguage
{
    /** @var array<string, string>|null Terms::fromSite(), or null for the defaults */
    private ?array $terms = null;

    /**
     * The words for the rest of the request
     * @param array<string, string> $terms Terms::fromSite()
     */
    public function useTerms(array $terms): static
    {
        $this->terms = $terms;

        return $this;
    }

    protected function formatMessage($message, array $args = [])
    {
        if (is_string($message) && preg_match('/\{(?:staff|Staff|participant|Participant)_(?:one|other)\b/', $message)) {
            // the defaults are lines without any of these, so this does not come back here
            $args += $this->terms ?? Terms::fromSite([], $this->locale);
        }

        return parent::formatMessage($message, $args);
    }
}
