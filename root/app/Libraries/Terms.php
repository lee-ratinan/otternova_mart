<?php

namespace App\Libraries;

/**
 * The words a business calls its people by, for the language lines that name them.
 *
 * A business may call its staff "therapists" (a spa) or "teachers" (a school), and the people a booking is for
 * "clients" or "students". It sets them for each of its website languages, and v1/mart/site gives the ones of its
 * answer's language as `terms`:
 *     { staff: {one, other}, participant: {one, other} }, every word a string or null when not set
 * The words are written as in the middle of a sentence ("therapist", "therapists"); a line wanting a capital uses
 * the capitalised name. A word the business has not set is the language's own default (Business.terms.*).
 *
 * A language line names a word with an ICU argument, beside its usual {0}, {1}:
 *     {staff_one}       {staff_other}       "therapist"   "therapists"
 *     {Staff_one}       {Staff_other}       "Therapist"   "Therapists"
 *     {participant_one} {participant_other} "student"     "students"
 *     {Participant_one} {Participant_other} "Student"     "Students"
 * App\Libraries\Language passes them to every line that has one, so views go on calling lang() as they do.
 */
class Terms
{
    /** The terms, and the API's longest word; a longer one is cut */
    public const NAMES      = ['staff', 'participant'];
    public const MAX_LENGTH = 40;

    /**
     * The arguments for the language lines, from v1/mart/site: the business's words when they are in the visitor's
     * language, the defaults otherwise
     * @param array  $site   v1/mart/site data
     * @param string $locale the request's locale
     * @return array<string, string> {staff_one, staff_other, Staff_one, Staff_other, participant_one, ...}
     */
    public static function fromSite(array $site, string $locale): array
    {
        $given = self::sameLanguage((string) ($site['language'] ?? ''), $locale) && is_array($site['terms'] ?? null) ? $site['terms'] : [];
        $args  = [];
        foreach (self::NAMES as $name) {
            $one   = self::clean($given[$name]['one'] ?? null);
            $other = self::clean($given[$name]['other'] ?? null);
            // a business that set one form only has it for both, rather than its word beside the default
            $words = $one === null && $other === null ? [] : ['one' => $one ?? $other, 'other' => $other ?? $one];
            foreach (['one', 'other'] as $form) {
                $word = $words[$form] ?? lang('Business.terms.' . $name . '.' . $form);
                $args[$name . '_' . $form]           = $word;
                $args[ucfirst($name) . '_' . $form]  = self::capitalise($word);
            }
        }

        return $args;
    }

    /** Whether the business's answer is in the language the page is: "th" and "th", or "zh-TW" and "zh-TW" */
    private static function sameLanguage(string $answered, string $locale): bool
    {
        if ($answered === '' || $locale === '') {
            return false;
        }
        if (strcasecmp($answered, $locale) === 0) {
            return true;
        }
        // en and en-US are one language; zh-SG and zh-TW are not (they are written differently)
        [$a] = explode('-', strtolower($answered));
        [$b] = explode('-', strtolower($locale));

        return $a === $b && $a !== 'zh';
    }

    /**
     * A word as the pages may show it, or null for none: one line, without the characters that would be markup (the
     * views print language lines as they are), at most MAX_LENGTH characters
     */
    private static function clean(mixed $word): ?string
    {
        if (! is_string($word)) {
            return null;
        }
        $word = str_replace(['<', '>', '&', '"', '`'], '', strip_tags($word));
        $word = trim((string) preg_replace(['/\p{C}/u', '/\s+/u'], ['', ' '], (string) preg_replace('/\s/u', ' ', $word)));

        return $word === '' ? null : mb_substr($word, 0, self::MAX_LENGTH);
    }

    /**
     * Every word with a capital first letter, as the pages' headings and labels are ("massage therapist" →
     * "Massage Therapist"); a word that has a capital already stays as written ("iPad coach" → "iPad Coach"), and so
     * do scripts without capitals (Thai, Japanese, Chinese)
     */
    private static function capitalise(string $phrase): string
    {
        return implode(' ', array_map(static function (string $word): string {
            if ($word === '' || preg_match('/\p{Lu}/u', $word)) {
                return $word;
            }

            return mb_strtoupper(mb_substr($word, 0, 1)) . mb_substr($word, 1);
        }, explode(' ', $phrase)));
    }
}
