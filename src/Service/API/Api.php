<?php declare(strict_types=1);

namespace App\Service\API;

use App\Service\DateConversion;

class Api
{
    private DateConversion $dateConversion;

    private const API_KEY = '479cf5de35930b8371a2f6325528417c57d5d264f72b756cc9a5e3bb565d1716';

    private const LIMIT = 2000;

    public const ONE_DAY_LIMIT = 23;

    private const API_LINK_HIST_HOUR = "https://min-api.cryptocompare.com/data/v2/histohour?";

    private const CURRENCY_FROM = 'BTC';

    private \GuzzleHttp\Client $client;

    public function __construct(DateConversion $dateConversion, \GuzzleHttp\Client $client)
    {
        $this->dateConversion = $dateConversion;
        $this->client = $client;
    }

    public function get(string $currency, ?\DateTimeImmutable $showTo = null, ?int $limit = null): array
    {
        $response = $this->client->request('GET', $this->buildUrl($currency, $showTo, $limit));
        return json_decode((string)$response->getBody(), true)['Data']['Data'];
    }

    private function buildUrl(string $currency, ?\DateTimeImmutable $showTo = null, ?int $limit = null): string
    {
        $url = sprintf(
            "%sfsym=%s&tsym=%s&limit=%s&api_key=%s",
            self::API_LINK_HIST_HOUR,
            self::CURRENCY_FROM,
            $currency,
            $limit ?: self::LIMIT,
            self::API_KEY
        );

        if ($showTo) {
            $timestamp = $this->dateConversion->dateTimeToTimestamp($showTo->sub(new \DateInterval('P0Y0M0DT1H0M0S')));
            $url = sprintf("%s&toTs=%s", $url, $timestamp);
        }

        return $url;
    }
}