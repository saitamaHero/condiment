<?php

namespace Condiment\Evaluables\Conditions\Definitions;

use Condiment\Evaluables\Conditions\ConditionDefinition;

class MatchPattern extends ConditionDefinition
{
    const DELIMITER = "/";

    public static function getRegisterName()
    {
        return 'regex';
    }

    protected function execute(): bool
    {
        list($subject, $regex) = $this->arguments;

        // echo "'$a' match regex /$regex/i\n";
        // if (strpos($regex, self::DELIMITER) !== 0) {
        //     $regex = self::DELIMITER . $regex;
        // }

        // if (strpos($regex, self::DELIMITER, -1) === false) {
        //     $regex .= self::DELIMITER . $regex;
        // }

        return preg_match("/$regex/i", $subject) > 0;
    }
}
