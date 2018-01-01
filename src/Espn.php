<?php
namespace FF;

use Cake\Core\InstanceConfigTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\RequestOptions;

class Espn
{
    use InstanceConfigTrait;

    protected $client = null;
    protected $apiKey = null;
    protected $cookies = null;

    protected $_defaultConfig = [
        'apiKeyUrl' => 'https://registerdisney.go.com/jgc/v5/client/ESPN-FANTASYLM-PROD/api-key?langPref=en-US',
        'cookieUrl' => 'https://ha.registerdisney.go.com/jgc/v5/client/ESPN-FANTASYLM-PROD/guest/login?langPref=en-US',
        'cookieDomain' => '.espn.com',
        'auth' => [
            'username' => null,
            'password' => null,
        ],
        'leagueId' => null,
        'seasonId' => null,
        'guzzle' => [
            'base_uri' => 'http://games.espn.com/ffl/api/v2/',
            'headers' => [
                'User-Agent' => 'kevinquinnyo/EspnFF API Client',
            ]
        ],
    ];

    public function __construct(array $config = [])
    {
        $this->config($config);
        $this->setCookies();
    }

    public function getClient()
    {
        $config = $this->getConfig();

        $options = $config['guzzle'];
        $options['cookies'] = $this->cookies;

        if ($this->client === null) {
            $this->client = new Client($options);
        }

        return $this->client;
    }

    protected function getApiKey()
    {
        $apiKeyUrl = $this->config('apiKeyUrl');

        $client = new Client();
        $responseHeaders = $client->post($apiKeyUrl)->getHeaders();

        if (isset($responseHeaders['api-key']) === false) {
            throw RuntimeException('Unable to fetch API key.');
        }

        return $responseHeaders['api-key'][0];
    }

    protected function setCookies()
    {
        $config = $this->getConfig();
        $this->apiKey = $this->getApiKey();

        $cookieUrl = $config['cookieUrl'];
        $username = $config['auth']['username'];
        $password = $config['auth']['password'];

        $data = [
            RequestOptions::JSON => [
                'loginValue' => $username,
                'password' => $password,
            ],
            RequestOptions::HEADERS => [
               'authorization' => sprintf('%s %s', 'APIKEY', $this->apiKey),
            ],
        ];

        $client = new Client();
        $response = $client->post($cookieUrl, $data);
        $json = json_decode($response->getBody());

        if (isset($json->data) === false) {
            throw new RuntimeException('Unable to extract authorization cookies.');
        }

        $cookies = [
            'SWID' => $json->data->profile->swid,
            'espn_s2' => $json->data->s2,
        ];

        $cookieDomain = $config['cookieDomain'];
        $this->cookies = CookieJar::fromArray($cookies, $cookieDomain);

        return $this;
    }
}
