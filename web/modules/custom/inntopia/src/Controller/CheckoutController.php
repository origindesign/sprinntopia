<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\CheckoutController.
 *
 */

namespace Drupal\inntopia\Controller;

use Drupal\inntopia\InntopiaStorage;
use Symfony\Component\HttpFoundation\RequestStack;


class CheckoutController extends InntopiaBaseController {


	protected $cart;



	/**
	 * CheckoutController constructor.
	 *
	 * @param \Drupal\inntopia\InntopiaStorage $inntopiaStorage
	 * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
	 */
	public function __construct(InntopiaStorage $inntopiaStorage, RequestStack $requestStack) {

		parent::__construct($inntopiaStorage, $requestStack);

		$this->cart = new CartController($this->inntopiaStorage, $this->requestStack);

	}



	/**
	 * @param string $step
	 *
	 * @return array
	 */
	public function displayCheckout($step = 'register'){


		$data = ($step === 'review') ? $this->cart->getCartData() : array();
		$data['step'] = $step;
		$data['source'] = 'recap';

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




	/**
	 * @return array
	 */
	public function displaySummary(){

		$build = $this->cart->displayCart('summary');

		// Return listing ready for display
		return $build;

	}



}
