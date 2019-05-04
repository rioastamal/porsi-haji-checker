<?php
/**
 * Class untuk melakukan pengecekan nomor porsi haji dengan melakukan
 * web scrapping dari https://haji.kemenag.go.id/v3/node/955358.
 *
 * <code>
 *   <?php
 *   use RioAstamal\Kemenag\NomorHajiParser;
 *
 *   $scraper = NomorHajiscraper::create($config);
 *   $nomor = NomorHajiChecker::create($scraper);
 *   $nomor->parse();
 *   // Output
 *   {
 *       "porsi": "3000837xxx",
 *       "nama": "Rio Astamal Tester",
 *       "kabupaten_kota": "Kota Surabaya",
 *       "provinsi": "Jawa Timur",
 *       "kuota": 15660,
 *       "posisi_porsi_kuota": 86863,
 *       "perkiraan_tahun_berangkat_hijriah": 1444,
 *       "perkiraan_tahun_berangkat_masehi": 2023
 *   }
 * </code>
 *
 * @author      Rio Astamal <rio@rioastamal.net>
 * @copyright   2019 Rio Astamal <rio@rioastamal.net>
 * @category    Library
 * @license     MIT License <https://opensource.org/licenses/MIT>
 */
namespace RioAstamal\Kemenag;
use DomDocument;
use DomXPath;

class NomorHajiParser
{
    /**
     * Versi dari pustaka.
     *
     * @var string
     */
    const VERSION = '1.0';

    /**
     * Objek scraper yang digunakan untuk melakukan fetch data pada
     * website kemenag.
     *
     * @var RioAstamal\Kemenag\NomorHajiscraper
     */
    protected $scraper = null;

    /**
     * Constructor
     *
     * @param RioAstamal\Kemenag\NomorHajiscraper
     * @return void
     */
    public function __construct(NomorHajiScraper $scraper)
    {
        $this->scraper = $scraper;
    }

    /**
     * @return NomorHajiParser
     */
    public static function create(NomorHajiScraper $scraper)
    {
        return new static($scraper);
    }

    /**
     * @return string JSON
     */
    public function parse()
    {
        $queries = [
            'nomor_porsi' => '#Nomor Porsi</span>\s\s+?<strong class="field-content">([0-9]+)</strong>#',
            //span[@class="views-label views-label-text-1"]/following-sibling::strong',
            'nama' => '#Nama: </span>\s\s+?<strong class="field-content">(.*)</strong>#',
            //span[@class="views-label views-label-text"]/following-sibling::strong',
            'kabupaten_kota' => '#Kabupaten/Kota: </span>\s\s+?<strong class="field-content">(.*)</strong>#',
            // '//span[@class="views-label views-label-text-2"]/following-sibling::strong',
            'provinsi' => '#Provinsi: </span>\s\s+?<strong class="field-content">(.*)</strong>#',
            // '//span[@class="views-label views-label-text-3"]/following-sibling::strong',
            'kuota' => '#Kuota Provinsi/Kab/Kota/Khusus: </span>\s\s+<strong class="field-content">(.*)</strong>#',
            // '//span[@class="views-label views-label-text-4"]/following-sibling::strong',
            'posisi_porsi_kuota' => '#Posisi Porsi Pada Kuota Provinsi/Kab/Kota/Khusus: </span>\s\s+?<strong class="field-content">([0-9]+)</strong>#',
            // '//span[@class="views-label views-label-text-5"]/following-sibling::strong',
            'perkiraan_tahun_berangkat_hijriah' => '#Perkiraan Berangkat Hijriah: </span>\s\s+<strong class="field-content">([0-9]+)</strong>#',
            // '//span[@class="views-label views-label-text-6"]/following-sibling::strong',
            'perkiraan_tahun_berangkat_masehi' => '#Perkiraan Berangkat Tahun Masehi: </span>\s\s+<strong class="field-content">([0-9]+)</strong>#'
            // '//span[@class="views-label views-label-text-7"]/following-sibling::strong'
        ];

        $json = [];
        try {
            $contents = $this->scraper->getContents();
        } catch (\Exception $e) {
            $json = [
                'error' => $e->getMessage()
            ];

            return json_encode($json);
        }

        foreach ($queries as $key => $regex) {
            $json[$key] = '';

            preg_match($regex, $contents, $matches);
            if (!isset($matches[1])) {
                continue;
            }

            $json[$key] = trim($matches[1]);
        }

        return json_encode($json, JSON_PRETTY_PRINT);
    }
}