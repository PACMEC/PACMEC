=== pl8app Cryptocurrency BEP20 Payment Gateway For WooCommerce ===
Contributors: pl8apptoken, pl8app,
Donate link: https://token.pl8app.co.uk
Tags: cryptocurrency, woocommerce, crypto, payments, token, e-commerce, ecommerce, pay with crypto, crypto woo, accept, gateway, payment gateway, woocommerce gateway, wordpress, address, free crypto plugin, plugin, plug-in, binance coin, bnb, bep20 token, custom token, add token, pl8app token, pl8app,
Requires at least: 4.7
Tested up to: 5.8.1
Stable tag: 2.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Take BEP20 Cryptocurrency on your Woocommerce based site, with the pl8app Token in built as standard, you can add any token of your chosing within the BEP20/Binance Smart Chain. As simple as adding the contract address and logo (no fees for adding your own tokens). Payment is not automated, it will produce a QR code for your shoppers to perform a wallet payment from and copyable values. There is automatic payment confirmations (assuming the correct tolerances have been set within the admin area).

This has been updated to allow pl8app Token plus any BEP20 Token added through the back end to act as a currency, so essentially everything will be priced as per that Token by making it the currency within WooCommerce Settings if required. (Optional)

== Description ==

This is a simple WP plugin developed by ourselves using API from Coingecko and PancakeSwap. Any pricing differences will be based on the exchanges used as we take our price only from PancakeSwap, however this should provide a relatively accurate price for coins which are not listed on the regular exchanges. We do however make the assumption you will need to have set up a liquidity pair within PancakeSwap (this allows any Swap/Token Trade to happen)

This will allow your customers to pay with any BEP20 token of your chosing.

We provide this plugin for free under the following licenses

“GPLv2 or later.”

The front end will provide the BEP20 Token Cryptocurrency as a payment option within the checkout area of Woocommerce. This will allow the user to select the token(s) you have added to the backend with pl8app token being as standard default.

Upon Checkout, when set up, the plugin will add BEP20 Cryptocurrency Token as a Payment Gateway Option, this will form a drop down box with all tokens added for your customer to select from.

Once the customer has selected their chosen token to pay with, they will then press place order.The next screen will show the relative number of token/coins to the currency amount of the product. Copy buttons included at Wallet and Number of Tokens for copying to customers wallet for transaction.

There is a Pay With MetaMask Button for your customers ease of use to connect with their Google browser based extension wallet. 

The backend within WP admin has 3 sections;

1. All Crypto Currencies (this section displays any of the custom BEP20 tokens you have added)
2. Add New ( this section allows the admin user to add any BEP20 token, based on contract address, you will also need to add the tokens logo as a graphic file at this location).
2.1 There is also a Tolerance (default at 2%) which is used as part of the automatic payment confirmation feature, where you will use this in relation to the total % fees your custom BEP20 token may have. For example if your custom BEP20 token has fees of 10%, you would change this tolerance to reflect this.
3. Crypto Payments Setting - This section has three main areas to cover; 
3.1. The first is a % markup/markdown, this is purely for use of your chosing, it is not required.
3.2. The second section is mandatory for the plugin to operate correctly, this is the wallet addresses (Binance Smart Chain) for payments to be made to, multiple addresses can be used, the plugin should rotate between any of the addresses selected). You can easily check the payment has been made by checking your wallets either with Binance Smart Chain Wallet/MetaMask Wallet/Trust Wallet. Only dispatch the sold item once you have checked payment has been sent.
3.3. The third is a quick checkbox for any cryptocurrencies you have added within 2. to be added as a payment selection. pl8app token is added by default for this plugin to work.



= WooCommerce compatibility =

This plugin is fully compatible with WooCommerce.

== Privacy Policy ==

https://token.pl8app.co.uk/privacy-policy/

= Contact us =

Got questions or feedback? Let us know! Head to our website https://token.pl8app.co.uk and we have many methods of contact and further information on our project.

= About pl8app =

We at pl8app have been developing various solutions since 2013, first being a simple Android based food review 
application. Essentially an app to take photos (and review) your plate, hence the name pl8app.

We have then developed ordering and time slot booking systems and solutions for the UK based Hospitality and Takwaway markets , more information can be seen at https://pl8app.co.uk (please note, we only sell to the UK due to our business policies, we actively Geofence this website from any other country to avoid any sign ups from Geographical areas we do not cater for.

Looking into the future, we developed our own BEP20 based token ( Contract Address 0xb77178a0fdead814296eae631be8e8171c02592b ) , this developed from the idea that many popular payment solutions these days can prove expensive, so we made a viable token with only 2% fees (1% of which is reflected back to our holders) to give the businesses we support by our solutions a fair alternative, with low fees and payments straight to their wallet (some providers and solutions also can hold funds for a number of days).

Whereas our main goal was to integrate the cryptocurrency payments into our own solutions as standard we needed to first build up a range of tools to allow us to do so, the first was simple to get the price of any BEP20 token which has not been listed on any exchanges. There are many of these, so we saw it as a common issue.

The second goal, relied on the first to get the price, which could then work along side regular FIAT currencies to make the gateway work, with a payment confirmation built in ofcourse.

After creating these tools for ourselves, we decided to release edited versions as Wordpress Plugins for our greater community.

We will always be developing new solutions based on our needs, or the needs of our clients.


== Frequently Asked Questions ==

= Can I deselect pl8app Token? =

No, we decided to make it a default token, as the developers we are supporting the use of our own token.

= Can the Automatic Payment Confirmation Feature detect two transactions happening for exactly the same amount, at exactly the same time with the same token? =

In this rare case, we will advise users to firstly add more than one wallet within the settings, then the first and second address should have been used in rotation. However if these two transactions do happen to go to the same address, this may depend on the frequency of the users business and how they have set the plugin up. Then a note will be added to ask the users to perform a manual check within their cryptocurrency wallet themselves.

= My BEP20 token contract is verified, but still not working =

To use your token to trade, it also needs liquidity (Usually against BNB) within the PancakeSwap liquidity pairs section, once created this should work.

== Changelog ==

= 2.0 =
* Added QR code options to suit users needs

= 1.8 =
* Added fixed price per token as a currency within WooCommerce if required (Optional)

= 1.6 =
* Pay With MetaMask Button Added

= 1.4 =
* Automatic Payment Confirmation Feature Added

= 1.2 =
* Fix FIAT Conversion Issue

= 1.1 =
* Fix Wallet Save Issue

= 1.0 =
* Initial Release