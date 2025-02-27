<?php

namespace Condiment\Evaluables\Conditions;


trait Regexable
{
    /**
     * @param string $input
     *
     * @return string
     */
    public function sanitizeRegex(string $input): string
    {
        return preg_replace_callback("/[#-.]|[[-^]|[?|{}]/", fn($match) => "\\$match[0]", $input);
    }

    public function regex(string $regex, $flags = ""): string
    {
        return sprintf("/%s/%s", $regex, $flags);
    }

    public function matchInput(string $subject, string $regex, string $regexFlags ="", $matchAll = false): int
    {
        $matchFunction = $matchAll ? 'preg_match_all' : 'preg_match';

        $matchCount = call_user_func_array(
            $matchFunction,
            [
                $this->regex($regex),
                $subject,
            ]
        );

        return $matchCount === false ? 0 : $matchCount;
    }
}
