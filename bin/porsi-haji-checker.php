<?php
require __DIR__ . '/../vendor/autoload.php';

use RioAstamal\Kemenag\NomorHajiScraper;
use RioAstamal\Kemenag\NomorHajiParser;

$argv = $_SERVER['argv'];

function help()
{
    echo <<<EOF
Penggunaan:
 {$_SERVER['argv'][0]} [NOMOR_PORSI]

 - atau -

 echo "NOMOR_PORSI" | {$_SERVER['argv'][0]}

EOF;
}

$porsi = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
if (!$porsi) {
    $porsi = trim(file_get_contents('php://stdin'));
}

if ($porsi === 'help') {
    help();
}

$scrapper = NomorHajiScraper::create($porsi);
$parser = NomorHajiParser::create($scrapper);
echo $parser->parse();