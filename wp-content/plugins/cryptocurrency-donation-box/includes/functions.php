<?php 

/**
 * Get the value of a settings field
 *
 * @param string $option settings field name
 * @param string $section the section name this field belongs to
 * @param string $default default text if it's not found
 *
 * @return mixed
 */
function CDBBC_get_option($option, $section, $default = '')
{

  $options = get_option($section);

  if (isset($options[$option])) {
    return $options[$option];
  }

  return $default;
}


// fetch settings array
function CDBBC_get_option_arr( $section, $default = '')
{
  $options = get_option($section);
  if (isset($options)&& is_array($options)) {
    return $options;
  }

  return $default;
}

// supported donation coin array
function CDBBC_supported_coins(){
    return  $coins= array(
        'bitcoin' => 'Bitcoin(BTC)',       
        'ethereum' => 'Ethereum(ETH)',
        'metamask'=> 'MetaMask',
        'tether' => 'Tether(USDT)',
        'cardano' => 'Cardano(ADA)',
        'xrp'=>'XRP ',		
        'polkadot'=>'Polkadot(DOT)',
        'binance-coin' => 'Binance Coin(BNB)',         
        'litecoin' => 'Litecoin(LTC)',
        'chainlink'=>'Chainlink(LINK)',
        'stellar' => 'Stellar(XLM)',   
        'bitcoin-cash' => 'Bitcoin Cash(BCH)', 
        'dogecoin'=>'Dogecoin(DOGE)',
        'usdcoin'=>'USD COIN(USDC)',
        'aave'=>'Aave(AAVE)',
        'uniswap'=>'Uniswap(UNI)',
        'wrappedbitcoin'=>'Wrapped Bitcoin(WBTC)',
        'avalanche'=>'Avalanche(AVAX)',
        'bitcoin-sv' => 'Bitcoin SV(BSV)' ,  
        'eos' => 'EOS',        
        'nem' => 'NEM(XEM)',
        'tron' => 'Tron(TRX)',
        'cosmos'=>'Cosmos(ATOM)',
        'monero' => 'Monero(XMR)',
        'tezos'=>'Tezos(XTZ)', 
        'elrond'=>'Elrond(EGLD)',       
        'iota' => 'IOTA(MIOTA)',        
        'theta'=>'THETA(THETA)',
        'synthetix'=>'Synthetix(SNX)',
        'dash' => 'Dash',
        'maker'=>'Maker(MKR)',
        'dai'=>'Dai(DAI)',             
        'ethereum-classic' => 'Ethereum Classic(ETC)',
        'lisk' => 'Lisk',        
        'neo' => 'NEO',
        'vechain' => 'VeChain(VET)',
        'qtum' => 'Qtum',       
        'omisego' => 'OmiseGO',
        'icon' => 'ICON(ICX)',        
        'nano' => 'Nano',
        'verge' => 'Verge',
        'bytecoin-bcn' => 'Bytecoin',
        'zcash' => 'Zcash(ZEC)',          
        'ontology' => 'Ontology(ONT)',
        'aeternity' => 'Aeternity',
        'steem' => 'Steem',
        
      );
}