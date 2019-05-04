[![Build Status](https://travis-ci.org/rioastamal/porsi-haji-checker.svg?branch=master)](https://travis-ci.org/rioastamal/terbilang)
[![Latest Stable Version](https://poser.pugx.org/rioastamal/porsi-haji-checker/v/stable)](https://packagist.org/packages/rioastamal/porsi-haji-checker)
[![License](https://poser.pugx.org/rioastamal/porsi-haji-checker/license)](https://packagist.org/packages/rioastamal/porsi-haji-checker)
[![codecov](https://codecov.io/gh/rioastamal/porsi-haji-checker/branch/master/graph/badge.svg)](https://codecov.io/gh/rioastamal/porsi-haji-checker)

## Tentang

Porsi Haji Checker adalah sebuah pustaka sederhana sekaligus utilitas berbasis Command-Line Interface (CLI) untuk melakukan pengecekan nomor porsi haji. Output yang dihasilkan berupa data JSON. Utilitas ini dapat dianggap adalah versi CLI dari halaman website [https://haji.kemenag.go.id/v3/node/955358](https://haji.kemenag.go.id/v3/node/955358).

Berikut adalah cara penggunaan Porsi Haji Checker sebagai utilitas CLI.

```bash
$ php bin/porsi-haji-checker.php 3000837XXX
{
    "nomor_porsi": "3000837XXX",
    "nama": "HAMBA ALLAH INDONESIA",
    "kabupaten_kota": "KOTA SURABAYA",
    "provinsi": "JAWA TIMUR",
    "kuota": "12345",
    "posisi_porsi_kuota": "12233",
    "perkiraan_tahun_berangkat_hijriah": "1444",
    "perkiraan_tahun_berangkat_masehi": "2023"
}
```

## Instalasi

Untuk instalasi Porsi Haji Checker dapat digunakan Composer atau download tarball pada halaman Github.


### Composer

Pastikan `composer` sudah terinstal pada sistem anda. Lalu jalankan perintah berikut untuk menginstal.

```
$ composer create-project -vvv rioastamal/porsi-haji-checker porsi-haji-checker
```

Jika ingin menggunakan versi production tambahkan opsi `--no-dev`.

```
$ composer create-project -vvv --no-dev rioastamal/porsi-haji-checker porsi-haji-checker
```

### Manual

Instalasi manual dapat menggunakan Git atau download tarball dari halaman Github.

```
$ git clone https://github.com/rioastamal/porsi-haji-checker.git
$ cd porsi-haji-checker
```

## Penggunaan

Porsi Haji Checker dapat digunakan sebagai sebuah utilitas atau sebuah pustaka.

### Sebagai Utilitas CLI

Lokasi utilitas Porsi Haji Checker ada pada direktori bin/. Utilitas ini membutuhkan arguman berupa nomor porsi haji. Berikut contohnya.

```
$ php bin/porsi-haji-checker.php 3000837XXX
{
    "nomor_porsi": "3000837XXX",
    "nama": "HAMBA ALLAH INDONESIA",
    "kabupaten_kota": "KOTA SURABAYA",
    "provinsi": "JAWA TIMUR",
    "kuota": "12345",
    "posisi_porsi_kuota": "12233",
    "perkiraan_tahun_berangkat_hijriah": "1444",
    "perkiraan_tahun_berangkat_masehi": "2023"
}
```

Selain lewat argumen anda dapat juga mensuplai nomor porsi haji lewat STDIN stream. Berikut contohnya.

```
$ echo "3000837XXX" | php bin/porsi-haji-checker.php
{
    "nomor_porsi": "3000837XXX",
    "nama": "HAMBA ALLAH INDONESIA",
    "kabupaten_kota": "KOTA SURABAYA",
    "provinsi": "JAWA TIMUR",
    "kuota": "12345",
    "posisi_porsi_kuota": "12233",
    "perkiraan_tahun_berangkat_hijriah": "1444",
    "perkiraan_tahun_berangkat_masehi": "2023"
}
```

### Sebagai Pustaka

Berikut adalah contoh menggunakan pustaka Porsi Haji Checker pada sebuah script PHP. Asumsi bahwa anda menggunakan Autoloader dari Composer.

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

// Jika tidak menggunakan autoload cukup require dua script berikut
// require __DIR__ . '/src/NomorHajiScraper.php
// require __DIR__ . '/src/NomorHajiParser.php

use RioAstamal\Kemenag\NomorHajiScraper;
use RioAstamal\Kemenag\NomorHajiParser;

$nomorPorsi = '3000837XXX';
$scrapper = NomorHajiScraper::create($nomorPorsi);
$parser = NomorHajiParser::create($scrapper);

$jsonInfoPorsi = $parser->parse();
print_r( json_decode($jsonInfoPorsi), JSON_OBJECT_AS_ARRAY );

/* output
Array
(
    [nomor_porsi] => 3000837XXX
    [nama] => HAMBA ALLAH INDONESIA
    [kabupaten_kota] => KOTA SURABAYA
    [provinsi] => JAWA TIMUR
    [kuota] => 12345
    [posisi_porsi_kuota] => 12233
    [perkiraan_tahun_berangkat_hijriah] => 1444
    [perkiraan_tahun_berangkat_masehi] => 2023
)
*/
```

## Menjalankan Unit Test

Pastikan komponen untuk development telah terinstal dengan menjalankan perintah composer berikut.

```
$ composer install -vvv
```

Perintah tersebut akan menginstal komponen yang diperlukan dalam development. Kemudian unit test dapat dijalankan dengan perintah berikut.

```
$ ./vendor/bin/phpunit --debug
PHPUnit 6.5.14 by Sebastian Bergmann and contributors.

Test 'RioAstamal\Kemenag\Test\NomorHajiParserTest::testReturnJsonSuccess' started
Test 'RioAstamal\Kemenag\Test\NomorHajiParserTest::testReturnJsonSuccess' ended
Test 'RioAstamal\Kemenag\Test\NomorHajiParserTest::testReturnJsonButEmpty' started
Test 'RioAstamal\Kemenag\Test\NomorHajiParserTest::testReturnJsonButEmpty' ended
Test 'RioAstamal\Kemenag\Test\NomorHajiParserTest::testScraperReturnError' started
Test 'RioAstamal\Kemenag\Test\NomorHajiParserTest::testScraperReturnError' ended


Time: 49 ms, Memory: 4.00MB

OK (3 tests, 15 assertions)
```

## Penulis

Pustaka Porsi Haji Checker ditulis oleh Rio Astamal <rio@rioastamal.net>

## Lisensi

Pustaka ini menggunakan lisensi MIT [http://opensource.org/licenses/MIT](http://opensource.org/licenses/MIT).