<?php
/**
 * Facebook locale data.
 *
 * @since      0.3.0
 * @package    Classic_SEO
 * @subpackage Classic_SEO\OpenGraph
 */


namespace Classic_SEO\OpenGraph;

defined( 'ABSPATH' ) || exit;

/**
 * Facebook class.
 */
class Facebook_Locale {

	const FACEBOOK_LOCALES = [
		'af_ZA', // Afrikaans.
		'ak_GH', // Akan.
		'am_ET', // Amharic.
		'ar_AR', // Arabic.
		'as_IN', // Assamese.
		'ay_BO', // Aymara.
		'az_AZ', // Azerbaijani.
		'be_BY', // Belarusian.
		'bg_BG', // Bulgarian.
		'bn_IN', // Bengali.
		'br_FR', // Breton.
		'bs_BA', // Bosnian.
		'ca_ES', // Catalan.
		'cb_IQ', // Sorani Kurdish.
		'ck_US', // Cherokee.
		'co_FR', // Corsican.
		'cs_CZ', // Czech.
		'cx_PH', // Cebuano.
		'cy_GB', // Welsh.
		'da_DK', // Danish.
		'de_DE', // German.
		'el_GR', // Greek.
		'en_GB', // English (UK).
		'en_IN', // English (India).
		'en_PI', // English (Pirate).
		'en_UD', // English (Upside Down).
		'en_US', // English (US).
		'eo_EO', // Esperanto.
		'es_CL', // Spanish (Chile).
		'es_CO', // Spanish (Colombia).
		'es_ES', // Spanish (Spain).
		'es_LA', // Spanish.
		'es_MX', // Spanish (Mexico).
		'es_VE', // Spanish (Venezuela).
		'et_EE', // Estonian.
		'eu_ES', // Basque.
		'fa_IR', // Persian.
		'fb_LT', // Leet Speak.
		'ff_NG', // Fulah.
		'fi_FI', // Finnish.
		'fo_FO', // Faroese.
		'fr_CA', // French (Canada).
		'fr_FR', // French (France).
		'fy_NL', // Frisian.
		'ga_IE', // Irish.
		'gl_ES', // Galician.
		'gn_PY', // Guarani.
		'gu_IN', // Gujarati.
		'gx_GR', // Classical Greek.
		'ha_NG', // Hausa.
		'he_IL', // Hebrew.
		'hi_IN', // Hindi.
		'hr_HR', // Croatian.
		'hu_HU', // Hungarian.
		'hy_AM', // Armenian.
		'id_ID', // Indonesian.
		'ig_NG', // Igbo.
		'is_IS', // Icelandic.
		'it_IT', // Italian.
		'ja_JP', // Japanese.
		'ja_KS', // Japanese (Kansai).
		'jv_ID', // Javanese.
		'ka_GE', // Georgian.
		'kk_KZ', // Kazakh.
		'km_KH', // Khmer.
		'kn_IN', // Kannada.
		'ko_KR', // Korean.
		'ku_TR', // Kurdish (Kurmanji).
		'ky_KG', // Kyrgyz.
		'la_VA', // Latin.
		'lg_UG', // Ganda.
		'li_NL', // Limburgish.
		'ln_CD', // Lingala.
		'lo_LA', // Lao.
		'lt_LT', // Lithuanian.
		'lv_LV', // Latvian.
		'mg_MG', // Malagasy.
		'mi_NZ', // Maori.
		'mk_MK', // Macedonian.
		'ml_IN', // Malayalam.
		'mn_MN', // Mongolian.
		'mr_IN', // Marathi.
		'ms_MY', // Malay.
		'mt_MT', // Maltese.
		'my_MM', // Burmese.
		'nb_NO', // Norwegian (bokmal).
		'nd_ZW', // Ndebele.
		'ne_NP', // Nepali.
		'nl_BE', // Dutch (Belgie).
		'nl_NL', // Dutch.
		'nn_NO', // Norwegian (nynorsk).
		'ny_MW', // Chewa.
		'or_IN', // Oriya.
		'pa_IN', // Punjabi.
		'pl_PL', // Polish.
		'ps_AF', // Pashto.
		'pt_BR', // Portuguese (Brazil).
		'pt_PT', // Portuguese (Portugal).
		'qu_PE', // Quechua.
		'rm_CH', // Romansh.
		'ro_RO', // Romanian.
		'ru_RU', // Russian.
		'rw_RW', // Kinyarwanda.
		'sa_IN', // Sanskrit.
		'sc_IT', // Sardinian.
		'se_NO', // Northern Sami.
		'si_LK', // Sinhala.
		'sk_SK', // Slovak.
		'sl_SI', // Slovenian.
		'sn_ZW', // Shona.
		'so_SO', // Somali.
		'sq_AL', // Albanian.
		'sr_RS', // Serbian.
		'sv_SE', // Swedish.
		'sw_KE', // Swahili.
		'sy_SY', // Syriac.
		'sz_PL', // Silesian.
		'ta_IN', // Tamil.
		'te_IN', // Telugu.
		'tg_TJ', // Tajik.
		'th_TH', // Thai.
		'tk_TM', // Turkmen.
		'tl_PH', // Filipino.
		'tl_ST', // Klingon.
		'tr_TR', // Turkish.
		'tt_RU', // Tatar.
		'tz_MA', // Tamazight.
		'uk_UA', // Ukrainian.
		'ur_PK', // Urdu.
		'uz_UZ', // Uzbek.
		'vi_VN', // Vietnamese.
		'wo_SN', // Wolof.
		'xh_ZA', // Xhosa.
		'yi_DE', // Yiddish.
		'yo_NG', // Yoruba.
		'zh_CN', // Simplified Chinese (China).
		'zh_HK', // Traditional Chinese (Hong Kong).
		'zh_TW', // Traditional Chinese (Taiwan).
		'zu_ZA', // Zulu.
		'zz_TR', // Zazaki.
	];

	/**
	 * Catch some weird locales served out by WP that are not easily doubled up.
	 *
	 * @param string $locale Current site locale.
	 *
	 * @return string
	 */
	public static function sanitize( $locale ) {
		$fix_locales = [
			'ca' => 'ca_ES',
			'en' => 'en_US',
			'el' => 'el_GR',
			'et' => 'et_EE',
			'ja' => 'ja_JP',
			'sq' => 'sq_AL',
			'uk' => 'uk_UA',
			'vi' => 'vi_VN',
			'zh' => 'zh_CN',
		];

		if ( isset( $fix_locales[ $locale ] ) ) {
			$locale = $fix_locales[ $locale ];
		}

		// Convert locales like "es" to "es_ES", in case that works for the given locale (sometimes it does).
		if ( 2 === strlen( $locale ) ) {
			$locale = self::join( $locale );
		}

		return $locale;
	}

	/**
	 * Validate with locales FB supports.
	 *
	 * Check to see if the locale is a valid FB one, if not, use en_US as a fallback.
	 *
	 * @param string $locale Current site locale.
	 *
	 * @return string
	 */
	public static function validate( $locale ) {
		if ( in_array( $locale, self::FACEBOOK_LOCALES, true ) ) {
			return $locale;
		}

		$locale = self::join( substr( $locale, 0, 2 ) );

		return in_array( $locale, self::FACEBOOK_LOCALES, true ) ? $locale : 'en_US';
	}

	/**
	 * Join locale to make full locale.
	 *
	 * @param string $locale Locale to join.
	 *
	 * @return string
	 */
	private static function join( $locale ) {
		return strtolower( $locale ) . '_' . strtoupper( $locale );
	}
}
