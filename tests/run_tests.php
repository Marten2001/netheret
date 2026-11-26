<?php
require __DIR__ . '/../includes/functions.php';

$passed = 0;
$failed = 0;

function check(string $name, $expected, $actual): void
{
    global $passed, $failed;
    if ($expected === $actual){
        $passed++;
        echo "PASS = $name\n";
    }else {
        $failed++;
        echo "FAIL = $name (expected " . var_export($expected, true) . ", got " . var_export($actual, true) . ")\n";
    }
}

//calclulateResponseTime
check('45 minutes', 45.0, calculateResponseTime(new DateTime('2026-10-19 09:00:00'), new DateTime('2026-10-19 09:45:00')));
check('same time is 0 minutes', 0.0, calculateResponseTime(new DateTime('2026-10-29 09:00:00'), new DateTime('2026-10-19 09:00:00')));

// isValidPostcode
check('valid postcode', true, isValidPostcode('BR8 7RE'));
check('no space, lower case', true, isValidPostcode('br87re'));
check('word is not a postcode', false, isValidPostcode('hello'));
check('empty is not a postcode', false, isValidPostcode(''));

//validateTicketInput
$ok = ['title' => str_repeat('a', 100), 'description' => 'Printer offline', 'priority' => 'low'];
check('title of exactly 100 is valid', [], validateTicketInput($ok));
$bad = ['title' => str_repeat('a', 101), 'description' => 'x', 'priority' => 'low'];
check('title of 101 gives 1 error', 1, count(validateTicketInput($bad)));
$bad = ['title' => 'Test', 'description' => 'x', 'priority' => 'banana'];
check('bad priority gives 1 error', 1, count(validateTicketInput($bad)));

echo "\n$passed passed, $failed failed\n";
exit($failed > 0 ? 1 : 0);