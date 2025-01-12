<?php
use Condiment\Evaluables\Evaluator;

require __DIR__ . '/vendor/autoload.php';

$conditionsArray = json_decode(file_get_contents('test_condition.json'), TRUE);


function sanitize_regex(string $regex): string
{
    return preg_replace_callback("/[#-.]|[[-^]|[?|{}]/", fn($match) => "\\$match[0]", $regex);
}

$evaluator = new Evaluator();

$evaluator->addEvaluablesFromArray($conditionsArray);


if ($evaluator->evaluate()) {
    echo "<h1>You are worth</h1>";
}else {
    echo "<h1>You deserve nothing, muggle</h1>";
}
