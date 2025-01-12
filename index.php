<?php
use Condiment\Evaluables\Evaluator;

require __DIR__ . '/vendor/autoload.php';

function sanitize_regex(string $regex): string
{
    return preg_replace_callback("/[#-.]|[[-^]|[?|{}]/", fn($match) => "\\$match[0]", $regex);
}


$evaluator = new Evaluator();

// $evaluator->addEvaluablesFromArray(
//     json_decode('[{"condition":"equals","arguments":["123","hola"],"negate":false,"logicalOperator":"and"},{"condition":"match","arguments":["Hola","la$"],"negate":false,"logicalOperator":"or"},{"condition":"startswith","arguments":["Dionicio","{"],"negate":false,"logicalOperator":"and"}]', true)
// );
$data = [
    'name' => 'Dionicio',
    'lastName' => 'Acevedo'
];

// Guessing that a condition can have arguments that depends on certain values,
// An approach it could be resolving on execution time those arguments

// $evaluator->argumentResolver(function ($argumentName, $condition) use ($data){
//     if (key_exists($argumentName, $data)) {
//         return $data[$argumentName];
//     }

//     return "";
// });
// Other scenario is resolving manually the arguments before the user put the values
// sleep(1);
// $user = (object)[

//     'id' => 1,
//     'email' => 'user@dummy.com',
//     'full_name' => 'User Dummy 1',
//     'role' => [
//         'id' => 1,
//         'name' => 'Admin'
//     ],
//     'degree' => "programmer"
// ];

$user = (object)[

    'id' => 3,
    'email' => 'user@accounting.com',
    'full_name' => 'User Accounting 1',
    'role' => [
        'id' => 3,
        'name' => 'Admin'
    ],
    'degree' => "accounting"
];

$evaluator
    ->match($user->email, "@dummy.com")
    // ->match("path/to/file.js", ".js$")
    // ->equals("abc", "abc")    
    // ->notMatch("This is a test", "test")
    ->group(function ($evaluator2) use ($user){
        $evaluator2->equals($user->role['id'], 1)
            // ->notMatch("Como estas?","^Hola")
            // ->orEquals("b", "b")
        ;
    })
    ->orGroup(function ($evaluator) use($user) {
        $evaluator->equals($user->degree, 'programmer')->equals($user->id, 1);
    })
    // ->match("This is a test", "test")
;

// $evaluator->evaluate();


if ($evaluator->evaluate()) {
    echo "<h1>You are worth</h1>";
}else {
    echo "<h1>You deserve nothing, muggle</h1>";
}
