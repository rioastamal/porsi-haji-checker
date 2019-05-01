<?php
/**
 * Class untuk melakukan scrapping nomor porsi haji pada
 * url https://haji.kemenag.go.id/v3/node/955358
 *
 * Langkah yang harus dilakukan untuk melakukan scrapping adalah:
 *
 * 1. GET request ke https://haji.kemenag.go.id/v3/node/955358
 * 2. Parse value dari "form_build_id"
 * 3. Lakukan request ke halaman baru yang didapat dari header 'Location'
 * 4. Untuk parsing diserahkan ke class NomorHajiParser
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
        'url' => 'https://haji.kemenag.go.id/v3/node/955358',
        // Chrome
        'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36',
        'use_cookie' => false,
        'cookie_dir' => __DIR__ . '/tmp',         // Direktori untuk penyimpanan cookie
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

        if ($this->config['use_cookie']) {
            if (!file_exists($this->config['cookie_dir'])) {
                mkdir($this->config['cookie_dir']);
            }
            $cookieFile = $this->config['cookie_dir'] . '/haji.cookie';
            curl_setopt($this->curl, CURLOPT_COOKIEFILE, $cookieFile);
            curl_setopt($this->curl, CURLOPT_COOKIEJAR, $cookieFile);
        }

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
        $postData = $this->openFormPage();
        $porsiPageUrl = $this->postFormData($postData);

        curl_setopt($this->curl, CURLOPT_HTTPGET, 1);
        curl_setopt($this->curl, CURLOPT_POST, 0);
        curl_setopt($this->curl, CURLOPT_URL, $porsiPageUrl);
        curl_setopt($this->curl, CURLOPT_HEADER, 0);

        $response = curl_exec($this->curl);
        if ($error = curl_error($this->curl)) {
            throw new \RuntimeException(sprintf('Error fetching %s. Message: %s', $porsiPageUrl, $error));
        }

        return $response;
    }

    /**
     * @param array $postData
     * @return string
     */
    protected function postFormData($postData)
    {
        curl_setopt($this->curl, CURLOPT_HTTPGET, 0);
        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        curl_setopt($this->curl, CURLOPT_REFERER, $this->config['url']);

        $response = curl_exec($this->curl);
        if ($error = curl_error($this->curl)) {
            throw new \RuntimeException(sprintf('Error doing post to %s. Message: %s', $this->config['url'], $error));
        }

        $location = '';
        preg_match('/Location:\s(.*)/', $response, $matches);
        if (!$matches) {
            throw new \RuntimeException('Error: Can not find Location header.');
        }

        return trim($matches[1]);
    }

    /**
     * @return array
     */
    protected function openFormPage()
    {
        curl_setopt($this->curl, CURLOPT_URL, $this->config['url']);
        curl_setopt($this->curl, CURLOPT_HTTPGET, 1);
        curl_setopt($this->curl, CURLOPT_POST, 0);

        $response = curl_exec($this->curl);
        if ($error = curl_error($this->curl)) {
            throw new \RuntimeException(sprintf('Error fetching %s. Message: %s', $this->config['url'], $error));
        }

        $formBuildId = null;
        $doc = new DOMDocument();
        $doc->loadHtml($response, LIBXML_NOERROR);
        $xpath = new DOMXPath($doc);

        $formBuildId = $xpath->query('//form[@id="webform-client-form-955358"]//input[@name="form_build_id"]');
        if ($formBuildId->length === 0) {
            throw new \RuntimeException('Error: Can not find "form_build_id" element.');
        }

        $postData = [
            'submitted' => [
                'nomor_porsi' => $this->porsi,
            ],
            'details' => [
                'sid' => '',
                'page_num' => '1',
                'page_count' => '1',
                'finished' => '0'
            ],
            'op' => 'Cari',
            'form_build_id' => $formBuildId[0]->getAttribute('value'),
            'form_id' => 'webform_client_form_955358'
        ];

        return $postData;
    }
}