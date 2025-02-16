<?php

namespace CurrencyConverter\Provider;

use CurrencyConverter\Exception\UnsupportedCurrencyException;
use GuzzleHttp\Client;

/**
 * Fetch rates from https://fixer.io
 *
 */
class FixerApi implements ProviderInterface
{
    /**
     * Base url of fixer api
     *
     * @var string
     */
    const API_BASEPATH = 'data.fixer.io/api/latest';

    /**
     * The fixer access key
     *
     * @var string
     */
    protected $accessKey;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * Flag to switch http(s) usage. required as fixer.io enables https api endpoints for paid accounts only
     * @var bool
     */
    protected $useHttps = false;

    /**
     * FixerApi constructor.
     * @param string $accessKey
     * @param Client|null $httpClient
     * @param bool $useHttps defaults to false
     */
    public function __construct($config, Client $httpClient = null, $useHttps = false)
    {
        $this->accessKey = $config['key'];
        $this->httpClient = $httpClient ?: new Client(['verify' => false]);
        $this->useHttps = (bool)$useHttps;
    }

    /**
     * {@inheritdoc}
     */
    public function getRate($fromCurrency, $toCurrency)
    {
        $path = sprintf(
            '%s%s?access_key=%s&symbols=%s&base=%s',
            ($this->useHttps) ? 'https://' : 'http://',
            self::API_BASEPATH,
            $this->accessKey,
            $toCurrency,
            $fromCurrency
        );
        $result = json_decode($this->httpClient->get($path)->getBody(), true);

        if (!isset($result['rates'][$toCurrency])) {
            throw new UnsupportedCurrencyException(sprintf('Undefined rate for "%s" currency.', $toCurrency));
        }

        return $result['rates'][$toCurrency];
    }
}
