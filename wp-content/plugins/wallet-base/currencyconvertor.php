<?php

namespace Ethereumico\EthereumWallet;

class CurrencyConvertor {

    /**
     * The source currency code.
     *
     * @var string
     */
    private $source;

    /**
     * The destination currency code.
     *
     * @var string
     */
    private $destination;

    /**
     * The https://min-api.cryptocompare.com API Key
     *
     * @var string
     */
    private $cryptocompareApiKey;

    /**
     * The cached rate value
     * 
     * @var number Cached rate value
     */
    private $rate;

    /**
     * Construct the class. Store the source and destination.
     *
     * @param string $source       The source currency code.
     * @param string $destination  The destination currency code.
     */
    public function __construct($source, $destination, $cryptocompareApiKey) {
        $this->source = $source;
        $this->destination = $destination;
        $this->cryptocompareApiKey = $cryptocompareApiKey;
    }

    /**
     * Convert a price from source to destination.
     *
     * @param  float $price  The price to convert (in source currency).
     *
     * @return float         The converted price (in destination currency).
     */
    public function convert($price) {
        $rate = $this->get_exchange_rate();
        return apply_filters(
                'ethereum_wallet_converted_price',
                $price * $rate,
                $this->source,
                $price,
                $this->destination
        );
    }

    /**
     * Retrieve the current exchange rate for this currency combination.
     *
     * Caches the value in a transient for 30 minutes (filterable), if no
     * cached value available then calls out to API to retrieve current value.
     *
     * @return float  The exchange rate.
     */
    public function get_exchange_rate() {
        if (isset($this->rate) && !is_null($this->rate)) {
            return $this->rate;
        }
        $transient_key = 'epg_exchange_rate_exchange_rate_' . $this->source . '_' . $this->destination;
        // Check for a cached rate first. Use it if present.
        $rate = get_transient($transient_key);
        if (false !== $rate) {
            $this->rate = apply_filters('ethereum_wallet_exchange_rate', (float) $rate);
            return $this->rate;
        }
        try {
            $rate = $this->get_rate_from_api();
        } catch (\Exception $ex) {
            if (false !== strpos($ex->getMessage(), "fsym " . $this->source . " does not exist")) {
                // no direct conversion from source to destination.
                // try the reverted one
                $rate = 1.0 / $this->get_rate_from_api($this->destination, $this->source);
            }
        }
        set_transient($transient_key, $rate, apply_filters('ethereum_wallet_exchange_rate_cache_duration', 1800));
        $this->rate = apply_filters('ethereum_wallet_exchange_rate', (float) $rate);
        return $this->rate;
    }

    /**
     * Retrieve the exchange rate from the API.
     *
     * @throws \Exception    Throws exception on error.
     *
     * @return float  The exchange rate.
     */
    private function get_rate_from_api($source = null, $destination = null) {
        global $wp_version;
        if (is_null($source)) {
            $source = $this->source;
        }
        if (is_null($destination)) {
            $destination = $this->destination;
        }
        // https://min-api.cryptocompare.com/
        // fsym  REQUIRED The cryptocurrency symbol of interest [Max character length: 10]
        // tsyms REQUIRED Comma separated cryptocurrency symbols list to convert into [Max character length: 500]
        // extraParams The name of your application (we recommend you send it) [Max character length: 50]
        $appName = urlencode(substr(home_url(), 0, 50));
        $url = 'https://min-api.cryptocompare.com/data/price?fsym=' . $source . '&tsyms=' . $destination . '&extraParams=' . $appName;
        if (!empty($this->cryptocompareApiKey)) {
            $url .= '&api_key=' . $this->cryptocompareApiKey;
        }
        $args = array(
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.1',
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url(),
            'blocking' => true,
            'headers' => array("Accept" => "*/*"),
            'cookies' => array(),
            'body' => null,
            'compress' => false,
            'decompress' => true,
            'sslverify' => true,
            'stream' => false,
            'filename' => null
        );
        $response = wp_remote_get($url, $args);
        if (is_wp_error($response) || 200 !== $response['response']['code']) {
            throw new \Exception('Could not fetch BNB pricing');
        }
        $body = json_decode($response['body']);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Could not convert BNB pricing - JSON error.');
        }
        if (!isset($body->{$destination})) {
            if (isset($body->{"Message"})) {
                throw new \Exception('Could not convert BNB pricing - ' . $body->{"Message"});
            }
            throw new \Exception('Could not convert BNB pricing - missing value after decoding.');
        }
        return (float) $body->{$destination};
    }

}
