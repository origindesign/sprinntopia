<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\CartController.
 *
 * This class doesn't add items to the cart since it's
 * done by Ajax through the AjaxController class. see
 * \Drupal\inntopia\Controller\AjaxController.
 *
 */
 
namespace Drupal\inntopia\Controller;

use Origindesign\Inntopia\Cart;


class CartController extends InntopiaBaseController {


	protected $nb_cart_items;



	public function getCartData(){

		$cart = new Cart( $this->sales_id, $this->api_url );

		$cart_obj = $cart->getCart($_SESSION["inntopia"]);

		$current_cart = $cart_obj->getValue();

		$this->nb_cart_items = count($current_cart);

		$data =  array(
			'nb_cart_items' => $this->nb_cart_items,
			'cart' => $current_cart
		);

		return $data;

	}



	public function displayCart($source = 'quickcart') {

		$data = $this->getCartData();
		$data['source'] = $source;

		// Format Listing
		$build[] =  [
			'#theme' => 'cart',
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

  
}
