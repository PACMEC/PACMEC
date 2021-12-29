<?php

// Crypto Helper
class pl8app_Cryptocurrencies {

	public static function get() {
        // id, name, round_precision, icon_filename, refresh_time, symbol, has_hd, has_autopay, needs_confirmations, erc20contract
		$cryptoArray = array(
            'pl8app' => new pl8app_Cryptocurrency('pl8app', 'pl8app', 7, pl8app_PLUGIN_DIR . '/assets/img/pl8app_logo.png', 60, 'PL8APP', false, true, true, '0xb77178a0fdead814296eae631be8e8171c02592b'),
        );

        return apply_filters('pl8ap_bep20_custom_crypto', $cryptoArray);
	}

    public static function get_hd() {
        $cryptos = self::get();
        $privacyCryptos = [];

        foreach ($cryptos as $crypto) {
            if ($crypto->has_hd()) {
                $privacyCryptos[] = $crypto;
            }
        }

        return $privacyCryptos;
    }

    public static function get_erc20_tokens() {
        $cryptos = self::get();
        $erc20Tokens = [];

        foreach ($cryptos as $crypto) {            
            if ($crypto->is_erc20_token()) {                
                $erc20Tokens[$crypto->get_id()] = $crypto;
            }
        }

        return $erc20Tokens;
    }

    public static function get_non_erc20_tokens() {
        $cryptos = self::get();
        $nonErc20Tokens = [];

        foreach ($cryptos as $crypto) {
            if (!$crypto->is_erc20_token()) {
                $nonErc20Tokens[$crypto->get_id()] = $crypto;
            }
        }

        return $nonErc20Tokens;
    }

    public static function is_erc20_token($cryptoId) {

        if (array_key_exists($cryptoId, pl8app_Cryptocurrencies::get_erc20_tokens())) {
            return true;
        }

        return false;
    }

    public static function get_erc20_contract($cryptoId) {
        $erc20Tokens = pl8app_Cryptocurrencies::get_erc20_tokens();
        
        foreach ($erc20Tokens as $token) {
            if ($token->get_id() === $cryptoId) {                
                return $token->get_erc20_contract();
            }
        }

        return '';
    }


    public static function get_alpha() {
        $cryptoArray = pl8app_Cryptocurrencies::get();
        $pl8app_token = array(
            'pl8app' => $cryptoArray['pl8app']
        );
        unset($cryptoArray['pl8app']);
        
        $keys = array_map(function($val) {
                return $val->get_id();
            }, $cryptoArray);
        array_multisort($keys, $cryptoArray);
        $cryptoArray = array_merge($pl8app_token, $cryptoArray);
        return $cryptoArray;
    }

    // Php likes to convert numbers to scientific notation, so this handles displaying small amounts correctly
    public static function get_price_string($cryptoId, $amount) {
        $cryptos = self::get();
        $crypto = $cryptos[$cryptoId];

        // Round based on smallest unit of crypto
        $roundedAmount = round($amount, $crypto->get_round_precision(), PHP_ROUND_HALF_UP);

        // Forces displaying the number in decimal format, with as many zeroes as possible to display the smallest unit of crypto
        $formattedAmount = number_format($roundedAmount, $crypto->get_round_precision(), '.', '');

        // We probably have extra 0's on the right side of the string so trim those
        $amountWithoutZeroes = rtrim($formattedAmount, '0');

        // If it came out to an round whole number we have a dot on the right side, so take that off
        $amountWithoutTrailingDecimal = rtrim($amountWithoutZeroes, '.');

        return $amountWithoutTrailingDecimal;
    }

	public static function is_valid_wallet_address($cryptoId, $address) {
	    try{
            return preg_match('/^bnb[a-zA-Z0-9]{37,48}/', $address) || preg_match('/^0x[a-fA-F0-9]{40,42}/', $address);
        }
        catch (Exception $e){
            pl8app_Util::log(__FILE__, __LINE__, 'Invalid cryptoId, contact plug-in developer.');
            throw new Exception('Invalid cryptoId, contact plug-in developer.');
        }
    }

    public static function is_valid_token_contract_address($contract_address){

	    try{
            $url = 'https://api.pancakeswap.info/api/v2/tokens/'.$contract_address;
            $response = wp_remote_get($url);

            if ( is_wp_error( $response ) || $response['response']['code'] !== 200) {
                throw new \Exception('No cryptocurrency exchanges could be reached, please try again.');
            }

            $responseBody = json_decode( $response['body'] );
            $pancakeswapPrice = isset($responseBody->data->price_BNB)? (float) $responseBody->data->price_BNB: 0;

            if($pancakeswapPrice == 0){
                #throw new \Exception('No cryptocurrency exchanges could be reached, please try again.');
            }

            return true;
        }
        catch (Exception $e){
	        return false;
        }

    }
}

?>