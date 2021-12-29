<?php
function pl8app_get_crypto_select_values() {
    $cryptoSelect = [];
    $cryptos = pl8app_Cryptocurrencies::get_alpha();

    foreach ($cryptos as $crypto) {
        $cryptoSelect[$crypto->get_id()] = $crypto->get_name() . ' (' . $crypto->get_id() . ')';
    }

    return $cryptoSelect;
}


/**
 * Add the menu of Crypto Currencies
 */
add_action( 'admin_menu',  'pl8app_crypto_admin_menu');

function pl8app_crypto_admin_menu(){
    add_submenu_page('edit.php?post_type=pl8app_custom_crypto',  __( 'Setting', 'pl8app' ), __( 'Crypto Payments Setting', 'pl8app' ), 'manage_options', 'pl8app_crypto_payment_settings', 'pl8app_crypto_payment_gateway' );
}

/**
 * Add the CUSTOM tokens to pl8app_Cryptocurrencies list
 */

add_filter('pl8ap_bep20_custom_crypto', 'add_pl8app_custom_crypto_to_list');

function add_pl8app_custom_crypto_to_list($cryptoArray){


    //Get the pl8app_custom_cryptos
    $all_pl8app_custom_tokens = get_posts([
        'post_type' => 'pl8app_custom_crypto',
        'showposts' => -1
    ]);
    foreach( $all_pl8app_custom_tokens as $s_token ){
        $post_id = $s_token->ID;
        $crypto_name = $s_token->post_title;
        if($crypto_name == 'pl8app') continue;
        $crypto_Id = $s_token->post_name;
        $contract_address = get_post_meta( $post_id, 'contract_address', true );

        if(!empty($crypto_Id) && !empty($contract_address)){
            $cryptoArray[$crypto_Id] =
                new pl8app_Cryptocurrency(
                        $crypto_Id,
                        $crypto_name,
                        7, get_the_post_thumbnail_url($post_id),
                        60, '', false, false, true,
                        $contract_address
                );
        }
    }

    return $cryptoArray;

}

function pl8app_crypto_payment_gateway(){

    $options = get_option('pl8apppro_redux_options', array());

    $wallet_addresses = isset($options['pl8app_addresses']) && is_array($options['pl8app_addresses'])?$options['pl8app_addresses']:array();
    $selected_cryptos = isset($options['crypto_select']) && is_array($options['crypto_select'])?$options['crypto_select']:array();
    ?>
    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
        <?php wp_nonce_field( 'pl8app_bep20_crypto_currencies' );?>
        <input type="hidden" name="action" value="pl8app_bep20_crypto_currencies">
        <div class="main">
            <h2><?php echo __('BEP20 Cryptocurrencies','pl8app'); ?></h2>

            <table class="form-table" role="presentation">
                <tbody>

                <tr>
                    <th scope="row">
                        <div class="redux_field_th">
                            <?php echo __('Markup/Markdown %', 'pl8app')?>
                            <span class="description">
                                <?php echo __('This only increases the crypto amount owed, the original fiat value will still be displayed to the customer.', 'pl8app')?>
                            </span>
                        </div>
                    </th>
                    <td>
                        <fieldset id="pl8apppro_redux_options-pl8app_markup"
                                  data-id="pl8app_markup">
                            <div id="pl8app_markup-spinner" class="redux_spinner" rel="pl8app_markup">
                                <span class="spinner-wrpr">
                                    <input type="number"name="pl8apppro_redux_options[pl8app_markup]"
                                           id="pl8app_markup" value="<?php echo isset($options['pl8app_markup'])?esc_attr($options['pl8app_markup']):0.0; ?>" step="0.01">
                                </span>
                            </div>
                            <div class="description field-desc">
                                <?php echo __('This will increase/decrease the amount of cryptocurrency
                                the customer will owe for the order.<br>(4.8 = 4.8% markup, -10.0 = 10% markdown)', 'pl8app')?>
                            </div>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <div class="redux_field_th">
                            <?php echo __('Order Expiry Time(Minute)', 'pl8app')?>
                            <span class="description">
                                <?php echo __('This only works for orders which were paid by Cryptocurrencies. Customer should pay for the order within the expiry time. Otherwise order will be canceled!', 'pl8app')?>
                            </span>
                        </div>
                    </th>
                    <td>
                        <div id="pl8app_markup-spinner" class="redux_spinner" rel="order_expire">
                            <span class="spinner-wrpr">
                                <input type="number"name="pl8apppro_redux_options[order_expire]"
                                       id="order_expire" value="<?php echo !empty($options['order_expire'])?esc_attr($options['order_expire']):30; ?>">
                                (min)
                            </span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <div class="redux_field_th">
                            <?php echo __('Wallet Addresses', 'pl8app');?>
                        </div>
                    </th>
                    <td>
                        <fieldset id="pl8apppro_redux_options-pl8app_addresses">
                            <ul id="pl8app_addresses-ul">
                                <?php foreach($wallet_addresses as $wallet_addresse) { ?>
                                <li>
                                    <input type="text" name="pl8apppro_redux_options[pl8app_addresses][]"
                                           value="<?php echo esc_attr($wallet_addresse); ?>" class="regular-text">
                                    <a href="javascript:void(0);" class="deletion">Remove</a>
                                </li>
                                <?php } ?>
                            </ul>
                            <span style="clear:both;display:block;height:0;"></span>
                            <a href="javascript:void(0);"
                               class="button button-primary" id="create_new_wallet">Add More</a>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <div class="redux_field_th">
                            <?php echo __('Qr Code', 'pl8app');?>
                        </div>
                    </th>
                    <td>
                        <fieldset id="pl8apppro_redux_options-pl8app_cryptocurrencies">
                            <ul id="pl8app_cryptocurrencies-ul">

                                <li>
                                    <input type="hidden" name="pl8app_settings[qr_code]" value="0">
                                </li>
                                <li>
                                    <input name="pl8apppro_redux_options[qr_code][no_selected]"
                                           id="pl8apppro_redux_options[qr_code][no_selected]" class="" type="checkbox" value="1"
                                        <?PHP echo !empty($options['qr_code']['no_selected']) ? checked(1, $options['qr_code']['no_selected'], false) : ''; ?>>&nbsp;
                                    <label for="pl8apppro_redux_options[qr_code][no_selected]">No QR Code</label>
                                </li>
                                <li>
                                    <input name="pl8apppro_redux_options[qr_code][receiver]"
                                           id="pl8apppro_redux_options[qr_code][receiver]" class=""
                                           type="checkbox"
                                           value="1" <?PHP echo !empty($options['qr_code']['receiver']) ? checked(1, $options['qr_code']['receiver'], false) : ''; ?>>&nbsp;
                                    <label for="pl8apppro_redux_options[qr_code][receiver]">QR Code for Receiver Wallet</label>
                                </li>
                                <li>
                                    <input name="pl8apppro_redux_options[qr_code][info]" id="pl8apppro_redux_options[qr_code][info]"
                                           class="" type="checkbox" value="1"
                                        <?PHP echo !empty($options['qr_code']['info']) ? checked(1, $options['qr_code']['info'], false) : ''; ?>>&nbsp;
                                    <label for="pl8apppro_redux_options[qr_code][info]">QR Code for Token Type and
                                        Amount</label>
                                </li>
                            </ul>
                            <span style="clear:both;display:block;height:0;"></span>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <div class="redux_field_th">
                            <?php echo __('Selected Cryptocurrencies', 'pl8app');?>
                        </div>
                    </th>
                    <td>
                        <fieldset id="pl8apppro_redux_options-pl8app_cryptocurrencies">
                            <ul id="pl8app_cryptocurrencies-ul">
                                <?php foreach(pl8app_get_crypto_select_values() as $crypto_id => $crypto_name) {
                                    ?>
                                    <li>
                                        <input type="checkbox"
                                               name="pl8apppro_redux_options[crypto_select][<?php echo esc_attr($crypto_id); ?>]"
                                               value="<?php echo esc_attr($crypto_id); ?>"
                                               class="regular-text" <?php echo in_array($crypto_id, $selected_cryptos) ? 'checked' : ''; ?> <?php echo $crypto_id === 'pl8app' ? 'disabled' : ''; ?>>
                                        <label for="pl8apppro_redux_options[crypto_select][<?php echo esc_attr($crypto_id); ?>]"><?php echo esc_attr($crypto_name); ?></label>
                                    </li>
                                <?php } ?>
                            </ul>
                            <span style="clear:both;display:block;height:0;"></span>
                        </fieldset>
                    </td>
                </tr>

                </tbody>
            </table>
            <?php echo submit_button();?>
        </div>
    </form>
    <?php
}

add_action("admin_post_pl8app_bep20_crypto_currencies", '_save_pl8app_crypto_form_changes');

function _save_pl8app_crypto_form_changes(){


    if ( isset( $_POST['action'] )&& isset( $_POST['_wpnonce'] ) &&
        wp_verify_nonce( $_POST['_wpnonce'], $_POST['action'] )
    )
    {
        $old_options = get_option(pl8app_REDUX_ID, array());
        $new_options = $old_options;
        $default_crypto_key = 'pl8app';


        if(isset($_POST[pl8app_REDUX_ID]['pl8app_addresses'])) {
            $new_options['pl8app_addresses'] = $_POST[pl8app_REDUX_ID]['pl8app_addresses'];
        }
        else{
            $new_options['pl8app_addresses'] = [];
        }
        if(isset($_POST[pl8app_REDUX_ID]['qr_code'])) {
            $new_options['qr_code'] = $_POST[pl8app_REDUX_ID]['qr_code'];
        }


        if(isset($_POST[pl8app_REDUX_ID]['pl8app_markup'])) {
            $new_options['pl8app_markup'] = sanitize_text_field($_POST[pl8app_REDUX_ID]['pl8app_markup']);
        }
        else{
            $new_options['pl8app_markup'] = 0;
        }

        if(isset($_POST[pl8app_REDUX_ID]['poocoin_widget_show'])) $new_options['poocoin_widget_show'] = sanitize_text_field($_POST[pl8app_REDUX_ID]['poocoin_widget_show']);


        /*check the validation of Addresses*/
        $invalidAddressKeys = [];
        $hasValidWalletAddress = false;
        $errorMessages = [];


        foreach ($new_options['pl8app_addresses'] as $k => $address) {

            if (!pl8app_Cryptocurrencies::is_valid_wallet_address('pl8app', sanitize_text_field($address))) {
                if ($address !== '') {
                    $invalidAddressKeys[] = $k;
                    $errorMessages[] = 'Invalid BEP-20 address: ' . $address;
                }
                else {
                    $invalidAddressKeys[] = $k;
                }
                continue;
            }
            $hasValidWalletAddress = true;
        }

        foreach ($invalidAddressKeys as $k) {
            if ($k > 0) {
                unset($new_options['pl8app_addresses'][$k]);
            }
            else {
                $new_options['pl8app_addresses'][$k] = '';
            }
        }

        if (! $hasValidWalletAddress) {
            $errorMessages[] = 'No valid wallet addresses.';

            foreach ($errorMessages as $msg) {
                pl8app_add_flash_notice($msg);
            }

            wp_redirect(admin_url("edit.php?post_type=pl8app_custom_crypto&page=pl8app_crypto_payment_settings"));
            die();
        }


        $old_selected_cryptos = isset($old_options['crypto_select']) && is_array($old_options['crypto_select'])?$old_options['crypto_select']:array();
        $new_selected_cryptos = array($default_crypto_key);

        if(isset($_POST[pl8app_REDUX_ID]['crypto_select']) && is_array($_POST[pl8app_REDUX_ID]['crypto_select'])){
            foreach($_POST[pl8app_REDUX_ID]['crypto_select'] as $crypto_Id => $value){
                $crypto_Id_sant = sanitize_text_field($crypto_Id);
                array_push($new_selected_cryptos, $crypto_Id_sant);
                $new_options[$crypto_Id_sant.'_addresses'] = $new_options['pl8app_addresses'];
                $new_options[$crypto_Id_sant.'_mode'] = '1';
                $new_options[$crypto_Id_sant.'_markup'] = $new_options['pl8app_markup'];
            }
        }

        $remove_list = array_diff($old_selected_cryptos, $new_selected_cryptos);
        foreach ($remove_list as $item){
            unset($new_options[$item.'_addresses']);
            unset($new_options[$item.'_mode']);
            unset($new_options[$item.'_markup']);
        }

        $new_options['crypto_select'] = $new_selected_cryptos;

        /**
         * Update the Crypto Currencies options
         */



        if(!empty($errorMessages)){
            foreach ($errorMessages as $msg) {
                pl8app_add_flash_notice($msg);
            }
        }
        else{
            pl8app_add_flash_notice('Saved changes successfully!', 'success');
        }

        $newSettings = new pl8app_Settings($new_options);

        foreach (pl8app_Cryptocurrencies::get() as $crypto) {

            $carouselAddresses = [];
            $cryptoId = $crypto->get_id();
            $addresses = $newSettings->get_addresses($default_crypto_key);

            foreach ($addresses as $ind => $address) {
                if (pl8app_Cryptocurrencies::is_valid_wallet_address($default_crypto_key, $address)) {
                    $carouselAddresses[] = trim($address);
                }
            }

            $carouselRepo = new pl8app_Carousel_Repo();
            $carouselRepo->set_buffer($cryptoId, $carouselAddresses);
        }

        $result = update_option(pl8app_REDUX_ID, $new_options);

    }

    wp_redirect(admin_url("edit.php?post_type=pl8app_custom_crypto&page=pl8app_crypto_payment_settings"));
    die();
}

/**
 * Add the meta boxes
 *
 */
add_action('add_meta_boxes', 'pl8app_crypto_payment_meta_boxes');


function pl8app_crypto_payment_meta_boxes(){
    remove_meta_box( 'slugdiv', 'pl8app_custom_crypto', 'normal' );
    add_meta_box('pl8app_crypto_payment-data', __('CryptoCurrency Information', 'pl8app'), 'pl8app_crypto_payment_output_meta_boxes', 'pl8app_custom_crypto', 'normal', 'high');
}


function pl8app_crypto_payment_output_meta_boxes($post){

    wp_nonce_field( 'pl8app_save_meta_data', 'pl8app_crypto_meta_box_nonce');

    $contract_address = esc_attr(get_post_meta( $post->ID, 'contract_address', true ));
    $token_tolerance = esc_attr(get_post_meta( $post->ID, 'token_tolerance', true ));
    ?>

    <div class="form-horizontal">
        <div class="col-12">
            <p class="form-group pl8app_crypto_currencies_row">
                <label class="control-label" for=""><strong><?php echo __('Contract Address','pl8app');?></strong></label>
                <input type="text" class="form-control input-field" name="contract_address" id="contract_address"
                       value="<?php echo esc_attr($contract_address); ?>">
                <p class="help-block"></p>
            </p>
            <p class="form-group pl8app_crypto_currencies_row">
                <label class="control-label" for=""><strong><?php echo __('Tolerance Rate(%)','pl8app');?></strong></label>
                <input type="number" class="form-control input-field" name="token_tolerance" id="token_tolerance"
                       value="<?php echo !empty($token_tolerance)?esc_attr($token_tolerance): ''; ?>" placeholder="2" step="0.001">
                <span class="description">
                    <?php echo __('Tolerance rate(%) for Auto Payment Confirmation Detection (Take Into Consideration any Fees of your Token)', 'pl8app')?>
                </span>
                <span class="description">
                    <?php echo __('Default tolerance rate is <b>2%</b>.', 'pl8app')?>
                </span>
            </p>
        </div>
    </div>

    <?php
}

add_action('save_post', 'save_crypto_meta_boxes', 1, 2);

function save_crypto_meta_boxes($post_id, $post){


    // $post_id and $post are required
    if (empty($post_id) || empty($post) || $post->post_type != 'pl8app_custom_crypto') {
        return;
    }

    // Dont' save meta boxes for revisions or autosaves.
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || is_int(wp_is_post_revision($post)) || is_int(wp_is_post_autosave($post))) {
        return;
    }

    // Check the nonce.
    if (empty($_POST['pl8app_crypto_meta_box_nonce']) || !wp_verify_nonce(wp_unslash($_POST['pl8app_crypto_meta_box_nonce']), 'pl8app_save_meta_data')) {
        return;
    }

    // Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
    if (empty($_POST['post_ID']) || absint($_POST['post_ID']) !== $post_id) {
        return;
    }

    // Check user has permission to edit.
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Check contract address validation
    if (empty($_POST['contract_address']) || !pl8app_Cryptocurrencies::is_valid_token_contract_address($_POST['contract_address'])){
        pl8app_add_flash_notice('Invalid token contract address, Please try again!');
        return;
    }

    update_post_meta( $post->ID, 'contract_address', sanitize_text_field($_POST['contract_address']));
    update_post_meta( $post->ID, 'token_tolerance', sanitize_text_field($_POST['token_tolerance']));

}


add_action('wp_trash_post', 'remove_old_setting');

function remove_old_setting($post_id) {

    if( get_post_type($post_id) === 'pl8app_custom_crypto' ) {


        $crypto_id = get_post_field('post_name', $post_id);

        $old_options = get_option(pl8app_REDUX_ID, array());

        unset($old_options[$crypto_id.'_addresses']);
        unset($old_options[$crypto_id.'_mode']);
        unset($old_options[$crypto_id.'_markup']);

        $pos = array_search($crypto_id, $old_options['crypto_select']);
        unset($old_options['crypto_select'][$pos]);

        update_option(pl8app_REDUX_ID, $old_options);
    }
}


