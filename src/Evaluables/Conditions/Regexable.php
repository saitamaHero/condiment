<?php

namespace Condiment\Evaluables\Conditions;


trait Regexable
{
    /**
     * Sanitize a value to avoid character to be interpreted on a regex
     *
     * For example a input "condi[]ment" it will be escaped to "condi\[\]ment"
     *
     * @param string $input
     *
     * @return string
     */
    public function sanitizeRegex(string $input): string
    {
        return preg_replace_callback("/[#-.]|[[-^]|[?|{}]/", fn($match) => "\\$match[0]", $input);
    }

    /**
     * Creates a regex with flags
     *
     * @param string $regex
     * @param string $flags
     *
     * @return string
     */
    public function regex(string $regex, $flags = ""): string
    {
        return sprintf("/%s/%s", $regex, $flags);
    }

    /**
     * @param string $subject
     * @param string $regex
     * @param  string
     * @param bool $matchAll
     *
     * @return int
     */
    public function matchInput(string $subject, string $regex, string $regexFlags ="", $matchAll = false): int
    {
        $matchFunction = $matchAll ? 'preg_match_all' : 'preg_match';

        $matchCount = call_user_func_array(
            $matchFunction,
            [
                $this->regex($regex, $regexFlags),
                $subject,
            ]
        );

        return $matchCount === false ? 0 : $matchCount;
    }
}
