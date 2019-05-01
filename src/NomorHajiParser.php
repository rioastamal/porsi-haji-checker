<?php
/**
 * Class untuk melakukan pengecekan nomor porsi haji dengan melakukan
 * web scrapping dari https://haji.kemenag.go.id/v3/node/955358.
 *
 * <code>
 *   <?php
 *   use RioAstamal\Kemenag\NomorHajiChecker;
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
            'nomor_porsi' => '//span[@class="views-label views-label-text-1"]/following-sibling::strong',
            'nama' => '//span[@class="views-label views-label-text"]/following-sibling::strong',
            'kabupaten_kota' => '//span[@class="views-label views-label-text-2"]/following-sibling::strong',
            'provinsi' => '//span[@class="views-label views-label-text-3"]/following-sibling::strong',
            'kuota' => '//span[@class="views-label views-label-text-4"]/following-sibling::strong',
            'posisi_porsi_kuota' => '//span[@class="views-label views-label-text-5"]/following-sibling::strong',
            'perkiraan_tahun_berangkat_hijriah' => '//span[@class="views-label views-label-text-6"]/following-sibling::strong',
            'perkiraan_tahun_berangkat_masehi' => '//span[@class="views-label views-label-text-7"]/following-sibling::strong'
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


        $dom = new DomDocument();
        $dom->loadHtml($contents, LIBXML_NOERROR);
        $xpath = new DomXPath($dom);

        foreach ($queries as $key => $query) {
            $json[$key] = '';

            $element = $xpath->query($query);
            if ($element->length === 0) {
                continue;
            }

            $json[$key] = $element[0]->nodeValue;
        }

        return json_encode($json, JSON_PRETTY_PRINT);
    }
}