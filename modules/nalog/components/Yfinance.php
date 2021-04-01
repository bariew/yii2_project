<?php
/**
 * Yfinance class file
 */

namespace app\modules\nalog\components;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use GuzzleHttp\Client;

/**
 * Class Yfinance
 * @package app\modules\nalog\components
 */
class Yfinance
{
    const CODE_APPLE = 'AAPL';
    const CODE_GOOGLE = 'GOOG';

    public static function call()
    {
        $client = ApiClientFactory::createApiClient();
//        $searchResult = $client->search("Apple");
//        $historicalData = $client->getHistoricalQuoteData(
//            "AAPL",
//            ApiClient::INTERVAL_1_DAY,
//            new \DateTime("-14 days"),
//            new \DateTime("today")
//        );
//        $dividendData = $client->getHistoricalDividendData(
//            "AAPL",
//            new \DateTime("-5 years"),
//            new \DateTime("today")
//        );
//        $splitData = $client->getHistoricalSplitData(
//            "AAPL",
//            new \DateTime("-5 years"),
//            new \DateTime("today")
//        );
//        $exchangeRate = $client->getExchangeRate("USD", "EUR");
//        $exchangeRates = $client->getExchangeRates([
//            ["USD", "EUR"],
//            ["EUR", "USD"],
//        ]);
//        $quote = $client->getQuote("AAPL");
        $quotes = $client->getQuotes(["AAPL", "GOOG"]);
        $a =1;
    }
}