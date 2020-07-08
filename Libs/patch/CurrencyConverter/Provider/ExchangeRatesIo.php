<?php
namespace CurrencyConverter\Provider;

use CurrencyConverter\Exception\UnsupportedCurrencyException;
use GuzzleHttp\Client;

/**
 * Get exchange rates from https://exchangeratesapi.io/
 */
class ExchangeRatesIo implements ProviderInterface
{
    /**
     * Base url of fixer api
     *
     * @var string
     */
    const API_BASEPATH = 'https://exchangeratesapi.io/api/latest';

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * FixerApi constructor.
     * @param Client|null $httpClient
     */
    public function __construct($config, Client $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new Client(['verify' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function getRate($fromCurrency, $toCurrency)
    {
        $path = sprintf(
            self::API_BASEPATH . '?symbols=%s&base=%s',
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