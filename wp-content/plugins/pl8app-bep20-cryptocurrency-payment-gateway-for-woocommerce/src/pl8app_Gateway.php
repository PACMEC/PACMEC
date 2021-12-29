<?php

class pl8app_Gateway extends WC_Payment_Gateway {
    private $cryptos;
    private $gapLimit;

    public function __construct() {


        $cryptoArray = pl8app_Cryptocurrencies::get();

        $pl8appSettings = new pl8app_Settings(get_option(pl8app_REDUX_ID));

        $this->cryptos = $cryptoArray;
        $this->gapLimit = 2;

        $this->id = 'pl8apppro_gateway';
        $this->title = $pl8appSettings->get_customer_gateway_message();
        $this->has_fields = true;
        $this->method_title = 'pl8app Crypto Payments';
        $this->method_description = 'Allow customers to pay using cryptocurrency';
        $this->init_settings();

        add_action('woocommerce_thankyou_' . $this->id, array($this, 'thank_you_page'));
        add_filter('woocommerce_enqueue_styles', array($this, 'enqueue_styles'), 1, 1);
        add_action( 'wp_footer', array($this, 'checkout_custom_script'), 20 );
    }
    public function enqueue_styles($styles){

        $styles['woocommerce-general-additional']  = $styles['woocommerce-general'];
        return $styles;

    }

    public function checkout_custom_script(){

        ?>
        <script type="text/javascript" src="https://unpkg.com/web3@1.5.2/dist/web3.min.js"></script>
        <script>

            jQuery(function ($) {

                $(document.body).on('click', '.clipboard', function(){
                    var $temp = $("<input>");
                    $("body").append($temp);
                    $temp.val($(this).attr('data-value').trim()).select();
                    document.execCommand("copy");
                    $temp.remove();
                    $("<span class='tooltip'>Copied to Clipboard!</span>").insertAfter(this);
                    setTimeout(function() {
                        $('.tooltip').remove();
                    }, 1000);

                });

                $(document.body).on('click', '.Pay-metamask-button', async function(){

                    var storeWalletAddress = $('span.storewalletaddress').data('value').toString().trim();
                    var contractaddress = $('span.tokenaddress').data('value').toString().trim();
                    var amount = $('span.walletamount').data('value').toString().trim();

                    try {
                        await ethereum.request({
                            method: 'wallet_switchEthereumChain',
                            params: [{ chainId: '0x38' }],
                        });
                    } catch (switchError) {
                        // This error code indicates that the chain has not been added to MetaMask.
                        if (switchError.code === 4902) {
                            try {
                                await ethereum.request({
                                    method: 'wallet_addEthereumChain',
                                    params: [{
                                        chainId: '0x38',
                                        chainName: 'Binance Smart Chain',
                                        nativeCurrency: {
                                            name: 'Binance Coin',
                                            symbol: 'BNB',
                                            decimals: 18
                                        },
                                        rpcUrls: ['https://bsc-dataseed.binance.org/'],
                                        blockExplorerUrls: ['https://bscscan.com']
                                    }]
                                })
                            } catch (addError) {
                                alert('Can\'t change network to Binance Smart chain automatically, Please change network Manually!');
                            }
                        }
                        // handle other "switch" errors
                        alert(switchError.message);
                    }


                    try{
                        const accounts = await ethereum.request({method: 'eth_requestAccounts'});
                    }
                    catch (e) {
                        if(e.message == 'ethereum is not defined'){
                            alert('Please install MetaMask Chrome extension in your browser!');
                        }
                        else{
                            alert('Error: ' + e.message);
                        }
                        return;
                    }

                    try {

                        if(!storeWalletAddress) throw new Error('Error: Empty Store WalletAddress!');
                        if(!contractaddress) throw new Error('Error: Empty Token Contract Address!');
                        if(!amount) throw new Error('Error: Empty Token Amount!');

                        const result = await bnbLib.encodeABI(contractaddress, ethereum.selectedAddress, storeWalletAddress, amount);

                        if(!result.data || !result.gas || !result.gasPrice) throw new Error('Error: Undefined Data!');

                        const transactionParameters = {
                            to: contractaddress, // Required except during contract publications.
                            from: ethereum.selectedAddress, // must match user's active address.
                            chainId: '0x38',
                            gasPrice: result.gasPrice,
                            gas: result.gas,
                            data: result.data
                        };

                        const transactionHash = await ethereum.request({
                            method: 'eth_sendTransaction',
                            params: [transactionParameters],
                        });
                        // Handle the result
                       alert('Transaction is success!');

                    } catch (error) {
                        alert('Error: ' + error.message);
                    }

                });

                const bnbLib = (function(){
                        async function encodeABI(contractAddress, from, to, amount){
                            return new Promise(async (resolve, reject)=>{
                                try{
                                    const bscProviderUrl = 'https://bsc-dataseed.binance.org/';
                                    const web3 = new Web3(bscProviderUrl);
                                    const contractABI = [{"inputs":[{"internalType":"string","name":"_NAME","type":"string"},{"internalType":"string","name":"_SYMBOL","type":"string"},{"internalType":"uint256","name":"_DECIMALS","type":"uint256"},{"internalType":"uint256","name":"_supply","type":"uint256"},{"internalType":"uint256","name":"_txFee","type":"uint256"},{"internalType":"uint256","name":"_lpFee","type":"uint256"},{"internalType":"uint256","name":"_MAXAMOUNT","type":"uint256"},{"internalType":"uint256","name":"SELLMAXAMOUNT","type":"uint256"},{"internalType":"address","name":"routerAddress","type":"address"},{"internalType":"address","name":"tokenOwner","type":"address"}],"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"minTokensBeforeSwap","type":"uint256"}],"name":"MinTokensBeforeSwapUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"previousOwner","type":"address"},{"indexed":true,"internalType":"address","name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"tokensSwapped","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"ethReceived","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"tokensIntoLiqudity","type":"uint256"}],"name":"SwapAndLiquify","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"bool","name":"enabled","type":"bool"}],"name":"SwapAndLiquifyEnabledUpdated","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"inputs":[],"name":"_liquidityFee","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"_maxTxAmount","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"_owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"_taxFee","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"claimTokens","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"decimals","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"subtractedValue","type":"uint256"}],"name":"decreaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"tAmount","type":"uint256"}],"name":"deliver","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"excludeFromFee","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"excludeFromReward","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"geUnlockTime","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"includeInFee","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"includeInReward","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"addedValue","type":"uint256"}],"name":"increaseAllowance","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"isExcludedFromFee","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"account","type":"address"}],"name":"isExcludedFromReward","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"time","type":"uint256"}],"name":"lock","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"numTokensSellToAddToLiquidity","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"owner","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"tAmount","type":"uint256"},{"internalType":"bool","name":"deductTransferFee","type":"bool"}],"name":"reflectionFromToken","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"renounceOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"liquidityFee","type":"uint256"}],"name":"setLiquidityFeePercent","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"maxTxPercent","type":"uint256"}],"name":"setMaxTxPercent","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"swapNumber","type":"uint256"}],"name":"setNumTokensSellToAddToLiquidity","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"bool","name":"_enabled","type":"bool"}],"name":"setSwapAndLiquifyEnabled","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"uint256","name":"taxFee","type":"uint256"}],"name":"setTaxFeePercent","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"swapAndLiquifyEnabled","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","name":"rAmount","type":"uint256"}],"name":"tokenFromReflection","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalFees","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"stateMutability":"nonpayable","type":"function"},{"inputs":[{"internalType":"address","name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"stateMutability":"nonpayable","type":"function"},{"inputs":[],"name":"uniswapV2Pair","outputs":[{"internalType":"address","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"uniswapV2Router","outputs":[{"internalType":"contract IUniswapV2Router02","name":"","type":"address"}],"stateMutability":"view","type":"function"},{"inputs":[],"name":"unlock","outputs":[],"stateMutability":"nonpayable","type":"function"},{"stateMutability":"payable","type":"receive"}]
                                    const contract = new web3.eth.Contract(contractABI, contractAddress);
                                    const decimals = await contract.methods.decimals().call();
                                    const value = Number.parseFloat(amount).toFixed(decimals).replace('.','');
                                    // calculate erc20 token amount
                                    const method = contract.methods.transfer(to, web3.utils.toBN(value));
                                    const data = method.encodeABI();
                                    // const gasPrice = await web3.eth.getGasPrice();
                                    // const gas = await method.estimateGas({from: from});

                                    const gasPrice = 5000000000;
                                    const gas = 800000;

                                    resolve({
                                        data, gasPrice: gasPrice.toString(16), gas: gas.toString(16)
                                    });

                                }
                                catch (e) {
                                    reject(e)
                                }
                            });
                        }
                    return {encodeABI};

                })();

            });
        </script>
        <?php
    }


    // This runs when the user hits the checkout page
    // We load our crypto select with valid crypto currencies
    public function payment_fields() {

        $pl8appSettings = new pl8app_Settings(get_option(pl8app_REDUX_ID));

        $validCryptos = $pl8appSettings->get_valid_selected_cryptos();

        foreach ($validCryptos as $crypto) {
            $cryptoId = $crypto->get_id();

            if ($pl8appSettings->hd_enabled($cryptoId)) {

                $mpk = $pl8appSettings->get_mpk($cryptoId);
                $hdMode = $pl8appSettings->get_hd_mode($cryptoId);
                $hdRepo = new pl8app_Hd_Repo($cryptoId, $mpk, $hdMode);

                $count = $hdRepo->count_ready();

                if ($count < 1) {
                    try {
                        pl8app_Hd::force_new_address($cryptoId, $mpk, $hdMode);
                    }
                    catch ( \Exception $e) {
                        pl8app_Util::log(__FILE__, __LINE__, 'UNABLE TO GENERATE HD ADDRESS FOR ' . $crypto->get_name() . ' ADMIN MUST BE NOTIFIED. REMOVING CRYPTO FROM PAYMENT OPTIONS' . $e->getTraceAsString());
                        unset($validCryptos[$cryptoId]);
                    }
                }
            }
        }

        $selectOptions = $this->get_select_options_for_valid_cryptos($validCryptos);

        woocommerce_form_field(
            'pl8app_currency_id', array(
                'type'     => 'select',
                'label'    => 'Choose a cryptocurrency',
                'required' => true,
                'default' => 'pl8pp',
                'options'  => $selectOptions,
            )
        );
    }


    // This is called when the user clicks Place Order, after validate_fields
    public function process_payment($order_id) {
        $order = new WC_Order($order_id);

        $selectedCryptoId = sanitize_text_field($_POST['pl8app_currency_id']);
        WC()->session->set('chosen_crypto_id', $selectedCryptoId);

        return array(
                      'result' => 'success',
                      'redirect'  => $this->get_return_url( $order ),
                    );
    }

    // This is called after process payment, when the customer places the order
    public function thank_you_page($order_id) {
        $cssPath = pl8app_PLUGIN_DIR . '/assets/css/pl8app-thank-you-page.css';
        $fontcssPath = pl8app_PLUGIN_DIR . '/assets/css/all.min.css';
        wp_enqueue_style('pl8app-styles', $cssPath);
        wp_enqueue_style('pl8app-fontawesome-styles', $fontcssPath);


        try {
            $orderAddressExists = get_post_meta($order_id, 'wallet_address');

            // if we already set this then we are on a page refresh, so handle refresh
            if (count($orderAddressExists) > 0) {

                $this->handle_thank_you_refresh(
                    get_post_meta($order_id, 'crypto_type_id', true),
                    get_post_meta($order_id, 'wallet_address', true),
                    get_post_meta($order_id, 'crypto_amount', true),
                    $order_id);

                return;
            }

            $pl8appSettings = new pl8app_Settings(get_option(pl8app_REDUX_ID));

            $order = new WC_Order($order_id);

            $chosenCryptoId = WC()->session->get('chosen_crypto_id');
            $crypto = $this->cryptos[$chosenCryptoId];
            $cryptoId = $crypto->get_id();

            update_post_meta($order_id, 'crypto_type_id', $cryptoId);


            // handle different woocommerce currencies and get the order total in USD
            $curr = get_woocommerce_currency();

            $cryptoMarkupPercent = $pl8appSettings->get_markup($cryptoId);

            if (!is_numeric($cryptoMarkupPercent)) {
                $cryptoMarkupPercent = 0.0;
            }

            // get current price of crypto
            $total = (float)$order->get_total();


            if($curr == $cryptoId){
                $pl8app_cryptoTotal = $total;
            }
            else{
                $bnb_flat_price = pl8app_Exchange::get_bnb_flat_price($curr, 60);

                $pl8app_bnb_rate = pl8app_Exchange::get_pl8app_bnb_price($cryptoId, $crypto->get_update_interval());

                $pl8app_cryptoTotal = $total / $bnb_flat_price / $pl8app_bnb_rate;
            }

            $cryptoMarkup = $cryptoMarkupPercent / 100.0;
            $cryptoPriceRatio = 1.0 + $cryptoMarkup;
            $cryptoTotalPreMarkup = round($pl8app_cryptoTotal, $crypto->get_round_precision(), PHP_ROUND_HALF_UP);
            $cryptoTotal = $cryptoTotalPreMarkup * $cryptoPriceRatio;


            //error_log('cryptoTotal post-dust: ' . $cryptoTotal);

            // format the crypto amount based on crypto
            $formattedCryptoTotal = pl8app_Cryptocurrencies::get_price_string($cryptoId, $cryptoTotal);

            update_post_meta($order_id, 'crypto_amount', $formattedCryptoTotal);

            pl8app_Util::log(__FILE__, __LINE__, 'Crypto total: ' . $cryptoTotal . ' Formatted Total: ' . $formattedCryptoTotal);

            // if hd is enabled we have stuff to do
            if ($pl8appSettings->hd_enabled($cryptoId)) {
                $mpk = $pl8appSettings->get_mpk($cryptoId);
                $hdMode = $pl8appSettings->get_hd_mode($cryptoId);
                $hdRepo = new pl8app_Hd_Repo($cryptoId, $mpk, $hdMode);

                // get fresh hd wallet
                $orderWalletAddress = $hdRepo->get_oldest_ready();

                // if we couldnt find a fresh one, force a new one
                if (!$orderWalletAddress) {

                    try {
                        pl8app_Hd::force_new_address($cryptoId, $mpk, $hdMode);
                        $orderWalletAddress = $hdRepo->get_oldest_ready();
                    }
                    catch ( \Exception $e) {
                        throw new \Exception('Unable to get payment address for order. This order has been cancelled. Please try again or contact the site administrator... Inner Exception: ' . $e->getMessage());
                    }

                }

                // set hd wallet address to get later
                WC()->session->set('hd_wallet_address', $orderWalletAddress);

                // update the database
                $hdRepo->set_status($orderWalletAddress, 'assigned');
                $hdRepo->set_order_id($orderWalletAddress, $order_id);
                $hdRepo->set_order_amount($orderWalletAddress, $formattedCryptoTotal);

                $orderNote = sprintf(
                    'Privacy Mode (HD wallet) address %s is awaiting payment of %s %s.',
                    $orderWalletAddress,
                    $formattedCryptoTotal,
                    $cryptoId);

            }
            // HD is not enabled, just handle static wallet or carousel mode
            else {

                $orderWalletAddress = $pl8appSettings->get_next_carousel_address($cryptoId);

                // handle payment verification feature
                if ($pl8appSettings->autopay_enabled($cryptoId)) {
                    $paymentRepo = new pl8app_Payment_Repo();

                    $paymentRepo->insert($orderWalletAddress, $cryptoId, $order_id, $formattedCryptoTotal, 'unpaid');
                }

                $orderNote = sprintf(
                    'Awaiting payment of %s %s to payment address %s.',
                    $formattedCryptoTotal,
                    $cryptoId,
                    $orderWalletAddress);
            }

            // For email
            WC()->session->set($cryptoId . '_amount', $formattedCryptoTotal);

            // For customer reference and to handle refresh of thank you page            
            update_post_meta($order_id, 'wallet_address', $orderWalletAddress);


            // Emails are fired once we update status to on-hold, so hook additional email details here
            add_action('woocommerce_email_order_details', array( $this, 'additional_email_details' ), 10, 4);

            $order->update_status('wc-on-hold', $orderNote);

            // Output additional thank you page html
            $this->output_thank_you_html($crypto, $orderWalletAddress, $formattedCryptoTotal, $order_id);
        }
        catch ( \Exception $e ) {
            $order = new WC_Order($order_id);

            // cancel order if something went wrong
            $order->update_status('wc-failed', 'Error Message: ' . $e->getMessage());
            pl8app_Util::log(__FILE__, __LINE__, 'Something went wrong during checkout: ' . $e->getMessage());

            echo '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">';
            echo '<ul class="woocommerce-error">';
            echo '<li>';
            echo 'Something went wrong.<br>';
            echo esc_html($e->getMessage());
            echo '</li>';
            echo '</ul>';
            echo '</div>';

        }
    }

    public function additional_email_details($order, $sent_to_admin, $plain_text, $email) {
        $chosenCrypto = WC()->session->get('chosen_crypto_id');
        $crypto =  $this->cryptos[$chosenCrypto];
        $orderCryptoTotal = WC()->session->get($crypto->get_id() . '_amount');
        $orderWalletAddress = get_post_meta($order->get_id(), 'wallet_address', true);
        $contract_address = pl8app_Cryptocurrencies::get_erc20_contract($crypto->get_id());
        $qrCode = $this->get_qr_code($crypto, $orderWalletAddress, $orderCryptoTotal);

        ?>
        <h2>Additional Details</h2>
        <p>QR Code Payment: </p>
        <?php if($qrCode) {?>
        <div style="margin-bottom:12px;">
            <img  src=<?php echo esc_attr($qrCode); ?> />
        </div>
        <?php }?>
        <p>
            Send Payment to this Wallet Address: <?php echo esc_html($orderWalletAddress); ?>
        </p>
        <p>
            <?php echo esc_html($crypto->get_name());?> Token Address: <?php echo esc_html($contract_address); ?>
        </p>
        <p>
            Currency: <?php echo '<img src="' . esc_attr($crypto->get_logo_file_path()) . '" alt="" />' . esc_html($crypto->get_name()); ?>
        </p>
        <p>
            Total:
            <?php
                if ($crypto->get_symbol() === '') {
                    echo esc_html(pl8app_Cryptocurrencies::get_price_string($crypto->get_id(), $orderCryptoTotal) . ' ' . $crypto->get_id());
                }
                else {
                    echo esc_html($crypto->get_symbol() . pl8app_Cryptocurrencies::get_price_string($crypto->get_id(), $orderCryptoTotal));
                }
            ?>
        </p>
        <?php

    }

    // convert array of cryptos to option array
    private function get_select_options_for_valid_cryptos() {
        $selectOptionArray = array();

        $pl8appSettings = new pl8app_Settings(get_option(pl8app_REDUX_ID));

        foreach (pl8app_Cryptocurrencies::get_alpha() as $crypto) {

            if ($pl8appSettings->crypto_selected_and_valid($crypto->get_id())) {
                $selectOptionArray[$crypto->get_id()] = $crypto->get_name();
            }
        }

        // handle different woocommerce currencies and get the order total in USD
        $curr = get_woocommerce_currency();

        if(isset($selectOptionArray[$curr])){
            return array( $curr => $selectOptionArray[$curr]);
        }

        return $selectOptionArray;
    }

    private function get_qr_prefix($crypto) {
        return strtolower(str_replace(' ', '', $crypto->get_name()));
    }

    private function get_qr_code($crypto, $walletAddress, $cryptoTotal) {

        $pl8appSettings = get_option(pl8app_REDUX_ID, array());
        $qr_code_option = isset($pl8appSettings['qr_code'])?$pl8appSettings['qr_code'] : 0;

        if(!$qr_code_option || isset($qr_code_option['no_selected'])){
            return false;
        }

        $dirWrite = pl8app_ABS_PATH . '/assets/img/';

        $formattedName = $this->get_qr_prefix($crypto);

        if(isset($qr_code_option['receiver']) && isset($qr_code_option['info'])){
            $qrData = $formattedName . ':' . $walletAddress . '?amount=' . $cryptoTotal;
        }
        else if(isset($qr_code_option['receiver'])){
            $qrData = $walletAddress;
        }
        else if(isset($qr_code_option['info'])){
            $qrData = $formattedName . '?amount=' . $cryptoTotal;
        }

        try {
            QRcode::png($qrData, $dirWrite . 'tmp_qrcode.png', QR_ECLEVEL_H);
        }
        catch (\Exception $e) {
            pl8app_Util::log(__FILE__, __LINE__, 'QR code generation failed, falling back...');
            $endpoint = 'https://api.qrserver.com/v1/create-qr-code/?data=';
            return $endpoint . $qrData;
        }
        $dirRead = pl8app_PLUGIN_DIR . '/assets/img/';
        return $dirRead . 'tmp_qrcode.png';
    }

    private function output_thank_you_html($crypto, $orderWalletAddress, $cryptoTotal, $orderId) {
        $formattedPrice = pl8app_Cryptocurrencies::get_price_string($crypto->get_id(), $cryptoTotal);
        $pl8appSettings = new pl8app_Settings(get_option(pl8app_REDUX_ID));

        $customerMessage = apply_filters('pl8app_customer_message', $pl8appSettings->get_customer_payment_message($crypto), $crypto, $orderId, $formattedPrice, $orderWalletAddress);
        $contract_address = pl8app_Cryptocurrencies::get_erc20_contract($crypto->get_id());
        $qrCode = $this->get_qr_code($crypto, $orderWalletAddress, $cryptoTotal);

        echo esc_html($customerMessage);
        ?>

        <h2>Cryptocurrency payment details</h2>
        <ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
            <li class="woocommerce-order-overview__qr-code">
                <?php if($qrCode) {?>
                <p style="word-wrap: break-word;">QR Code payment:</p>
                <div class="qr-code-container">
                    <img style="margin-top:3px;" src=<?php echo esc_attr($qrCode); ?> />
                </div>
                <?php } ?>
                <div class="Pay-metamask-button"></div>
            </li>
            <li>
                <p style="word-wrap: break-word;">Send Payment to this Wallet Address:
                    <strong>
                        <span class="woocommerce-Price-amount amount">
                            <?php echo '<span class="all-copy ">' . esc_html($orderWalletAddress) . ' <span class="storewalletaddress clipboard far fa-copy" title="Copy to clipboard" data-value="' . esc_html($orderWalletAddress) . ' "></span></span>' ?>
                        </span>
                    </strong>
                </p>
                <p>Currency:
                    <strong>
                        <?php
                        echo '<img style="display:inline;height:23px;width:23px;vertical-align:middle;" src="' . esc_attr($crypto->get_logo_file_path()) . '" />';
                        ?>
                        <span style="padding-left: 4px; vertical-align: middle;" class="woocommerce-Price-amount amount" style="vertical-align: middle;">
                            <?php echo esc_html($crypto->get_name()); ?>
                        </span>
                    </strong>
                </p>

                <p style="word-wrap: break-word;">Total:
                    <strong>
                        <span class="woocommerce-Price-amount amount">
                            <?php
                            if ($crypto->get_symbol() === '') {
                                echo '<span class="all-copy">' . esc_html($formattedPrice) . '</span><span class="no-copy">&nbsp;' . esc_html($crypto->get_id()) . '</span> <span class="walletamount clipboard far fa-copy" title="Copy to clipboard" data-value="' . esc_html($formattedPrice) . '"></span>';
                            }
                            else {
                                echo '<span class="no-copy">' . esc_html($crypto->get_symbol()) . '</span>' . '<span class="all-copy">&nbsp;' . esc_html($formattedPrice) . ' <span class="walletamount clipboard far fa-copy" title="Copy to clipboard" data-value="' . esc_html($formattedPrice) . '"></span></span>';
                            }
                            ?>
                        </span>
                    </strong>
                </p>

                <p style="word-wrap: break-word;"><?php echo esc_html($crypto->get_name());?> Token Address:
                    <strong>
                        <span class="woocommerce-Price-amount amount">
                            <?php echo '<span class="all-copy">' . esc_html($contract_address) . ' <span class="tokenaddress clipboard far fa-copy" title="Copy to clipboard" data-value="' . esc_html($contract_address) . ' "></span></span>' ?>
                        </span>
                    </strong>
                </p>
            </li>
            <?php
    }

    private function handle_thank_you_refresh($chosenCrypto, $orderWalletAddress, $cryptoTotal, $orderId) {
        $this->output_thank_you_html($this->cryptos[$chosenCrypto], $orderWalletAddress, $cryptoTotal, $orderId);
    }

    // this function hits all the crypto exchange APIs that the user selected, then averages them and returns a conversion rate for USD
    // if the user has selected no exchanges to fetch data from it instead takes the average from all of them
    private function get_crypto_value_in_usd($cryptoId, $updateInterval) {

        $prices = array();
        $reduxSettings = get_option(pl8app_REDUX_ID);
        if (!array_key_exists('selected_price_apis', $reduxSettings)) {
            throw new \Exception('No price API selected. Please contact plug-in support.');
        }

        $selectedPriceApis = $reduxSettings['selected_price_apis'];

        if (in_array('0', $selectedPriceApis)) {
            $ccPrice = pl8app_Exchange::get_cryptocompare_price($cryptoId, $updateInterval);

            if ($ccPrice > 0) {
                $prices[] = $ccPrice;
            }
        }

        if (in_array('1', $selectedPriceApis)) {
            $hitbtcPrice = pl8app_Exchange::get_hitbtc_price($cryptoId, $updateInterval);

            if ($hitbtcPrice > 0) {
                $prices[] = $hitbtcPrice;
            }
        }

        if (in_array('2', $selectedPriceApis)) {
            $gateioPrice = pl8app_Exchange::get_gateio_price($cryptoId, $updateInterval);

            if ($gateioPrice > 0) {
                $prices[] = $gateioPrice;
            }
        }

        if (in_array('3', $selectedPriceApis)) {
            $bittrexPrice = pl8app_Exchange::get_bittrex_price($cryptoId, $updateInterval);

            if ($bittrexPrice > 0) {
                $prices[] = $bittrexPrice;
            }
        }

        if (in_array('4', $selectedPriceApis)) {
            $poloniexPrice = pl8app_Exchange::get_poloniex_price($cryptoId, $updateInterval);

            // if there were no trades do not use this pricing method
            if ($poloniexPrice > 0) {
                $prices[] = $poloniexPrice;
            }
        }

        $sum = 0;
        $count = count($prices);

        if ($count === 0) {
            throw new \Exception('No cryptocurrency exchanges could be reached, please try again.');
        }

        foreach ($prices as $price) {
            $sum += $price;
        }

        $average_price = $sum / $count;

        return $average_price;
    }
}

?>