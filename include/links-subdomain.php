<?php
/**
 * @package Polylang
 */

/**
 * Links model for use when the language code is added in url as a subdomain
 * for example en.mysite.com/something
 * implements the "links_model interface"
 *
 * @since 1.2
 */
class PLL_Links_Subdomain extends PLL_Links_Abstract_Domain {
	/**
	 * Stores whether the home url includes www. or not.
	 * Either '://' or '://www.'.
	 *
	 * @var string
	 */
	protected $www;

	/**
	 * Constructor.
	 *
	 * @since 1.7.4
	 *
	 * @param PLL_Model $model Instance of PLL_Model.
	 */
	public function __construct( &$model ) {
		parent::__construct( $model );
		$this->www = ( false === strpos( $this->home, '://www.' ) ) ? '://' : '://www.';
	}

	/**
	 * Adds the language code in a url.
	 * links_model interface.
	 *
	 * @since 1.2
	 *
	 * @param string             $url  The url to modify.
	 * @param PLL_Language|false $lang The language object.
	 * @return string                  Modified url.
	 */
	public function add_language_to_link( $url, $lang ) {
		if ( ! empty( $lang ) && false === strpos( $url, '://' . $lang->slug . '.' ) ) {
			$url = $this->options['default_lang'] == $lang->slug && $this->options['hide_default'] ? $url : str_replace( $this->www, '://' . $lang->slug . '.', $url );
		}
		return $url;
	}

	/**
	 * Returns the url without language code
	 * links_model interface
	 *
	 * @since 1.2
	 *
	 * @param string $url url to modify
	 * @return string modified url
	 */
	public function remove_language_from_link( $url ) {
		$languages = array();

		foreach ( $this->model->get_languages_list() as $language ) {
			if ( ! $this->options['hide_default'] || $this->options['default_lang'] != $language->slug ) {
				$languages[] = $language->slug;
			}
		}

		if ( ! empty( $languages ) ) {
			$url = preg_replace( '#://(' . implode( '|', $languages ) . ')\.#', $this->www, $url );
		}

		return $url;
	}

	/**
	 * Get hosts managed on the website.
	 *
	 * @since 1.5
	 *
	 * @return string[] The list of hosts.
	 */
	public function get_hosts() {
		$hosts = array();
		foreach ( $this->model->get_languages_list() as $lang ) {
			$hosts[ $lang->slug ] = wp_parse_url( $this->home_url( $lang ), PHP_URL_HOST );
		}
		return $hosts;
	}
}
