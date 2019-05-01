<?php
namespace RioAstamal\Kemenag\Test;

use PHPUnit\Framework\TestCase;
use RioAstamal\Kemenag\NomorHajiParser;
use RioAstamal\Kemenag\NomorHajiScraper;

class NomorHajiParserTest extends TestCase
{
    protected $scraper = null;

    public function setUp()
    {
        $sampleResult = file_get_contents(__DIR__ . '/sample/result.20190501.html');
        $this->scraper = $this->createMock(NomorHajiScraper::class);
        $this->scraper->method('getContents')
             ->willReturn($sampleResult);
    }

    public function testReturnJsonSuccess()
    {
        $parser = NomorHajiParser::create($this->scraper);
        $json = $parser->parse();
        $jsonDecoded = json_decode($json, JSON_OBJECT_AS_ARRAY);

        $this->assertNotNull($jsonDecoded);
        $this->assertTrue(strpos($json, 'HAMBA ALLAH') >= 0);

        $expected = [
            'nomor_porsi' => '300012345',
            'nama' => 'ABDULLAH HAMBA ALLAH',
            'kabupaten_kota' => 'KOTA SURABAYA',
            'provinsi' => 'JAWA TIMUR',
            'kuota' => '15123',
            'posisi_porsi_kuota' => '86123',
            'perkiraan_tahun_berangkat_hijriah' => '1444',
            'perkiraan_tahun_berangkat_masehi' => '2023'
        ];

        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $jsonDecoded[$key]);
        }
    }

    public function testReturnJsonButEmpty()
    {
        $this->scraper = $this->createMock(NomorHajiScraper::class);
        $this->scraper->method('getContents')->willReturn('nothing');

        $parser = NomorHajiParser::create($this->scraper);
        $json = $parser->parse();
        $jsonDecoded = json_decode($json, JSON_OBJECT_AS_ARRAY);

        $this->assertNotNull($jsonDecoded);
        $this->assertTrue(strpos($json, 'HAMBA ALLAH') === false);
    }

    public function testScrapperReturnError()
    {
        $this->scraper = $this->createMock(NomorHajiScraper::class);
        $this->scraper->method('getContents')
             ->will($this->throwException(new \RuntimeException('Error on testing')));

        $parser = NomorHajiParser::create($this->scraper);
        $json = $parser->parse();
        $jsonDecoded = json_decode($json, JSON_OBJECT_AS_ARRAY);

        $this->assertNotNull($jsonDecoded);
        $this->assertTrue(strpos($json, 'error') >= 0);
        $this->assertEquals('Error on testing', $jsonDecoded['error']);
    }
}