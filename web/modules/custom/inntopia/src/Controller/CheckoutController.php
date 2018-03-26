<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\CheckoutController.
 *
 */

namespace Drupal\inntopia\Controller;


class CheckoutController extends InntopiaBaseController {


	public function displayCheckout($step = 'register'){


		$data = array();
		$data['step'] = $step;

		// Format Listing
		$build[] =  [
			'#theme' => 'checkout_container',
			'#data' => $data,
			'#attached' => array(
				'library' => array(
					'inntopia/inntopia',
				),
			),
			'#cache' => array(
				'max-age' => 0,
			)
		];

		// Return listing ready for display
		return $build;

	}


	public function displaySummary(){

		$cart = new CartController($this->inntopiaStorage, $this->requestStack);
		$build = $cart->displayCart('summary');

		// Return listing ready for display
		return $build;

	}



}
