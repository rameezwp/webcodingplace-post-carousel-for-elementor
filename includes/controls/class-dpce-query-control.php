<?php
/**
 * Select2 control that searches posts or terms over AJAX.
 *
 * Saved values are the same array of IDs the old SELECT2 control stored,
 * so existing widgets keep their selection.
 *
 * @package DPCE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DPCE_Query_Control
 */
class DPCE_Query_Control extends \Elementor\Control_Select2 {

	/**
	 * Control type.
	 */
	const TYPE = 'dpce-query';

	/**
	 * Control type.
	 *
	 * @return string
	 */
	public function get_type() {
		return self::TYPE;
	}

	/**
	 * Default settings.
	 *
	 * `query` tells the editor script what to search:
	 * array( 'kind' => 'post'|'term', 'source' => post type or taxonomy ).
	 *
	 * @return array
	 */
	protected function get_default_settings() {
		return array_merge(
			parent::get_default_settings(),
			array(
				'query'    => array(),
				'multiple' => true,
			)
		);
	}
}
