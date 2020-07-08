<?php

namespace CurrencyConverter\Provider;

use CurrencyConverter\Exception\UnsupportedCurrencyException;
use GuzzleHttp\Client;

/**
 * Fetch rates from https://apilayer.net
 *
 */
class CurrencyLayer implements ProviderInterface
{
    /**
     * Base url of currency api
     *
     * @var string
     */
    const API_BASEPATH = 'apilayer.net/api/';

    /**
     * The currency access key
     *
     * @var string
     */
    protected $accessKey;

    /**
     * The currency endpoint
     *
     * @var string
     */
    protected $endpoint;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * Flag to switch http(s) usage. required as apilayer.net enables https api endpoints for paid accounts only
     * @var bool
     */
    protected $useHttps = false;

    /**
     * FixerApi constructor.
     * @param string $accessKey
     * @param Client|null $httpClient
     * @param bool $useHttps defaults to false
     */
    public function __construct($config, $httpClient = null, $useHttps = false)
    {
        $this->accessKey = $config['key'];
        $this->endpoint = $config['endpoint'];
        $this->httpClient = new Client(['verify' => false]);
        $this->useHttps = (bool)$useHttps;
    }

    /**
     * {@inheritdoc}
     */
    public function getRate($fromCurrency, $toCurrency)
    {
        $path = sprintf(
            '%s%s%s?access_key=%s&currencies=%s&source=%s',
            ($this->useHttps) ? 'https://' : 'http://',
            self::API_BASEPATH,
            $this->endpoint,
            $this->accessKey,
            $toCurrency,
            $fromCurrency
        );

        $result = json_decode($this->httpClient->get($path)->getBody(), true);
        $result = array_values($result['quotes'])[0];

        if (!isset($result)) {
            throw new UnsupportedCurrencyException(sprintf('Undefined rate for "%s" currency.', $toCurrency));
        }

        return $result;
    }
}
