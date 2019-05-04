<?php
/**
 * Class untuk melakukan scrapping nomor porsi haji pada
 * url https://haji.kemenag.go.id/v3/basisdata/xml/NOMOR
 *
 * @author      Rio Astamal <rio@rioastamal.net>
 * @copyright   2019 Rio Astamal <rio@rioastamal.net>
 * @category    Library
 * @license     MIT License <https://opensource.org/licenses/MIT>
 */
namespace RioAstamal\Kemenag;
use DomDocument;
use DomXpath;

class NomorHajiScraper
{
    /**
     * Konfigurasi
     *
     * @var array
     */
    protected $config = [
        // URL halaman pengecekan porsi haji
        'url' => 'https://haji.kemenag.go.id/v3/basisdata/xml',
        // Chrome
        'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
        'verify_ssl' => false,                   // verify ssl cert?
        'timeout'    => 5
    ];

    /**
     * Curl instance
     *
     * @var Resource
     */
    protected $curl = null;

    /**
     * Nomor porsi yang akan dicek
     *
     * @var string
     */
    protected $porsi = '';

    /**
     * Constructor
     *
     * @param string $porsi
     * @param array $config
     * @return void
     */
    public function __construct($porsi, array $config = [])
    {
        $this->porsi = $porsi;

        $this->config = $config + $this->config;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_USERAGENT, $this->config['user_agent']);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_FAILONERROR, 1);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->config['timeout']);

        // It seem *.kemenag.go.id certificate is not fully chained
        // Quick and dirty solution is to disable SSL certificate verification
        if ($this->config['verify_ssl'] === false) {
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
        }
    }

    /**
     * @param string $porsi
     * @param array $config
     * @return this
     */
    public static function create($porsi, array $config = [])
    {
        return new static($porsi, $config);
    }

    /**
     * @return string
     */
    public function getContents()
    {
        $porsiPageUrl = $this->config['url'] . '/' . $this->porsi;

        curl_setopt($this->curl, CURLOPT_HTTPGET, 1);
        curl_setopt($this->curl, CURLOPT_POST, 0);
        curl_setopt($this->curl, CURLOPT_URL, $porsiPageUrl);
        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLINFO_HEADER_OUT, 1);

        $response = curl_exec($this->curl);
        if ($error = curl_error($this->curl)) {
            throw new \RuntimeException(sprintf('Error fetching %s. Message: %s', $porsiPageUrl, $error));
        }

        return $response;
    }
}