<?php

/**
 * @file
 * Contains \Drupal\inntopia\Twig\StringFormat.
 */

namespace Drupal\inntopia\TwigExtension;



/**
 * Provides format function for strings
 */
class StringFormat extends \Twig_Extension {


	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'string_format';
	}


	/**
	 * {@inheritdoc}
	 */
	public function getFilters() {
		return  array(
			new \Twig_SimpleFilter('machineFilter', [$this, 'machineName']),
		);
	}


	/**
	 * @param $string
	 *
	 * @return mixed
	 */
	public function machineName($string) {
		$value = str_replace('&amp; ', '', $string);
		$value = str_replace(' ', '-', strtolower($value));
		$value = preg_replace('@[^a-z0-9-]+@', '', $value);
		return $value;
	}


}