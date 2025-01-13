<?php
use Condiment\Evaluables\Evaluator;

require __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);

$conditionsArray = json_decode(file_get_contents('test_condition.json'), TRUE);


function sanitize_regex(string $regex): string
{
    return preg_replace_callback("/[#-.]|[[-^]|[?|{}]/", fn($match) => "\\$match[0]", $regex);
}
try {
    $evaluator = new Evaluator();
    $evaluator->addEvaluablesFromArray($conditionsArray);
    
    if ($evaluator->evaluate()) {
        echo "<h1>You are worth</h1>";
    }else {
        echo "<h1>You deserve nothing, muggle</h1>";
    }
} catch (\Throwable $th) {
    echo '<p style="color: red;">'.$th->getMessage().'</p>';
}


