<?php

require_once "../../../vendor/autoload.php";

\vendor\pagseguro\Library::initialize();

$options = [
    'initial_date' => '2016-05-01T14:55',
    'final_date' => '2016-05-10T09:55', //Optional
    'page' => 1, //Optional
    'max_per_page' => 20, //Optional
];

try {
    $response = \vendor\pagseguro\Services\PreApproval\Search\Date::search(
        \vendor\pagseguro\Configuration\Configure::getAccountCredentials(),
        $options
    );

    echo "<pre>";
    print_r($response);
} catch (Exception $e) {
    die($e->getMessage());
}
