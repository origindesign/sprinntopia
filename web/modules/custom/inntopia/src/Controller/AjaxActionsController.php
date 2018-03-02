<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\AjaxActionsController.
 */
 
namespace Drupal\inntopia\Controller;


use Origindesign\Inntopia\Cart;


class AjaxActionsController extends InntopiaBaseController {


	private $data;


	/**
	 * Dispatch ajax function based on param in route url
	 * @param string $method
	 *
	 * @return array
	 */
	public function dispatch($method = 'none') {

		$this->data = ( isset($_POST["data"]) ) ? $_POST["data"] : false;

		if ($this->data && $this->session ){

			switch ($method){
				case "add-to-cart": $result = $this->addToCart(); break;
				case "remove-from-cart": $result = $this->removeFromCart(); break;
				case "attach-customer-to-cart": $result = $this->attachCustomerToCart(); break;
				case "update-customer-billing": $result = $this->updateCustomerBilling(); break;
				case "complete-order": $result = $this->completeOrder(); break;
				default: $result = "Nothing to do.";
			}

		}else{

			$result = "Error with Session on add to cart ajax call.";

		}

		$build[] = array(
			'#type' => 'markup',
			'#markup' => t('<p>Method: '.$method.'</p><p>Result: '.$result.'</p>'),
		);


		/*
		 * Invalidate cache so the cron will always be run
		 */
		$build['#cache']['max-age'] = 0;

		return $build;


	}


	/**
	 * Adding an item to the Cart
	 *
	 * @return string
	 */
	private function addToCart (){

		$itineraryArr  = array($this->data);
		$cart = new Cart( $this->sales_id, $this->api_url );

		if( isset($this->data["PackageId"]) ){
			// If it's a package, add to cart using the package method
			$cartRequest = $cart->addPackageItemsToCart($this->session, $itineraryArr, $this->data["PackageId"]);
		}else{
			// Otherwise add the item with the standard method
			$cartRequest = $cart->addItemsToCart($this->session, $itineraryArr);
		}

		if ($cartRequest->getSuccess()){
			$result = "Success adding item to the cart.";
		}else{
			$result = "Error trying to add item to the cart.";
			$result .= $cartRequest->getErrorDetail();
		}

		return $result;

	}



	/**
	 * Deleting item from cart.
	 *
	 * @return string
	 */
	private function removeFromCart (){

		$itineraryItemId  = $this->data["ItineraryItemId"];
		$cart = new Cart( $this->sales_id, $this->api_url );
		$cartRequest = $cart->deleteFromCart($this->session, $itineraryItemId);

		if ($cartRequest->getSuccess()){
			$result = "Success removing item from the cart";
		}else{
			$result = "Error trying to remove item from the cart";
		}

		return $result;
	}


	/**
	 * Attaching customer to cart.
	 *
	 * @return string
	 */
	private function attachCustomerToCart (){
		// @TODO implementing Customer class from library (addAnonymousCustomer)
		return "Attaching customer to cart.";
	}


	/**
	 * Updating customer billing information.
	 *
	 * @return string
	 */
	private function updateCustomerBilling (){
		// @TODO implementing Customer class from library (updateCustomer)
		return "Updating customer billing information.";
	}


	/**
	 * Completing Order
	 *
	 * @return string
	 */
	private function completeOrder (){
		// @TODO implementing Checkout class from library (completeOrder)
		return "Completing Order.";
	}


  
}
