<?php

error_reporting(0);

use Alfred\Workflows\Workflow;

use Algolia\AlgoliaSearch\SearchClient as Algolia;
use Algolia\AlgoliaSearch\Support\UserAgent as AlgoliaUserAgent;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/functions.php';

$query = $argv[1];
$version = isset($argv[2]) ? $argv[2] : 'v4';

$workflow = new Workflow;

if ($version === 'v1') {
    $application_id = 'BH4D9OD16A';
    $api_key = 'cec0554d960fa30b4b0b610f372a8636';
    $index_name = 'livewire-framework';
} elseif ($version === 'v2') {
    $application_id = 'BH4D9OD16A';
    $api_key = 'cec0554d960fa30b4b0b610f372a8636';
    $index_name = 'livewire-framework';
} else {
    // v3 and v4 use the same index
    $application_id = '418WMK58D6';
    $api_key = '4c5d415abd4c0c167f4368e679076c09';
    $index_name = 'livewire-framework-3';
}

$algolia = Algolia::create($application_id, $api_key);

AlgoliaUserAgent::addCustomUserAgent('Livewire Alfred Workflow', '1.0.0');

$results = getResults($algolia, $index_name, $query, $version);

if (empty($results)) {
    $workflow->result()
        ->title('No matches')
        ->icon('google.png')
        ->subtitle('No match found in the docs. Search Google for: "Laravel+Livewire+{$query}"')
        ->arg('https://www.google.com/search?q=laravel+livewire+' . $query)
        ->quicklookurl('https://www.google.com/search?q=laravel+livewire+' . $query)
        ->valid(true);

    echo $workflow->output();

    exit;
}

foreach ($results as $hit) {
    list($title, $titleLevel) = getTitle($hit);

    if ($title === null) {
        continue;
    }

    $title = html_entity_decode($title);

    $workflow->result()
        ->uid($hit['objectID'])
        ->title($title)
        ->autocomplete($title)
        ->subtitle(html_entity_decode(getSubtitle($hit, $titleLevel)))
        ->arg($hit['url'])
        ->quicklookurl($hit['url'])
        ->valid(true);
}

echo $workflow->output();
