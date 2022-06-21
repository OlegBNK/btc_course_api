<?php declare(strict_types=1);

namespace App\Service\API;

use App\Service\DateConversion;

class Api
{
    /**
     * @var DateConversion
     */
    private $dateConversion;

    private const API_KEY = '479cf5de35930b8371a2f6325528417c57d5d264f72b756cc9a5e3bb565d1716';

    private const LIMIT = 23;

    private const API_LINK_HIST_HOUR = "https://min-api.cryptocompare.com/data/v2/histohour?";

    private const CURRENCY_FROM = 'BTC';

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct(DateConversion $dateConversion, \GuzzleHttp\Client $client)
    {
        $this->dateConversion = $dateConversion;
        $this->client = $client;
    }

    /**
     * @param string $currencyTo
     * @param \DateTimeImmutable|null $showTo
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $currencyTo, ?\DateTimeImmutable $showTo = null): array
    {
        $response = $this->client->request('GET', $this->buildUrl($currencyTo, $showTo));
        return json_decode((string)$response->getBody(), true)['Data']['Data'];
    }

    /**
     * @param string $currencyTo
     * @param \DateTimeImmutable|null $showTo
     * @return string
     * @throws \Exception
     */
    private function buildUrl(string $currencyTo, ?\DateTimeImmutable $showTo = null): string
    {
        $url = sprintf(
            "%sfsym=%s&tsym=%s&limit=%s&api_key=%s",
            self::API_LINK_HIST_HOUR,
            self::CURRENCY_FROM,
            $currencyTo,
            self::LIMIT,
            self::API_KEY
        );

        if ($showTo) {
            $timestamp = $this->dateConversion->dateTimeToTimestamp($showTo->sub(new \DateInterval('P0Y0M0DT1H0M0S')));
            $url = sprintf("%s&toTs=%s", $url, $timestamp);
        }

        return $url;
    }
}