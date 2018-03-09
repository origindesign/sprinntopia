<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\AjaxDisplayController.
 */
 
namespace Drupal\inntopia\Controller;


use Drupal\inntopia\InntopiaLodging;


class AjaxDisplayController extends InntopiaBaseController {


	private $data;


	/**
	 * Dispatch ajax function based on param in route url
	 * @param string $method
	 *
	 * @return array
	 */
	public function dispatch($method = 'none') {

		// Get parameters
		$getParams = $this->requestStack->getCurrentRequest()->query->all();
		$this->data = ( isset($getParams["data"]) ) ? $getParams["data"] : FALSE;

		if ($this->session ){

			switch ($method){

				case "listing-lodging": $result = $this->lodgingListing( $this->data );	break;
				case "listing-activities": $result = $this->activitiesListing( $this->data ); break;
				case "display-quickcart": $result = $this->displayQuickCart(); break;
				default:
					$result = array(
						'#type' => 'markup',
						'#markup' => t('<p>No render could be found. '),
					);
			}

		}else{

			$result = array(
				'#type' => 'markup',
				'#markup' => t('<p>No render could be found. '),
			);

		}

		return $result;

	}



	private function activitiesListing( $data ) {

		// @TODO implement activities listing

		$data = NULL;

		$result =  array(
			'#theme' => 'activities_listing',
			'#data' => $data,
			'#cache' => array(
				'max-age' => 0,
			)
		);

		return $result;

	}



	private function lodgingListing( $data ){

		// Set default data if it's not set
		if ( !isset($data) ){
			$data = array(
				"arrivalDate" => date("Y-m-d", strtotime('+3 days')),
				"departureDate" => date("Y-m-d", strtotime('+6 days')),
				"adultCount" => 2,
				"childrenCount" => 0
			);
		}

		$inntopiaLodging = new InntopiaLodging($this->sales_id, $this->api_url, $data);
		$lodgingList = $inntopiaLodging->getLodgingList();
		$filters = $inntopiaLodging->getFilters();

		$data = array(
			'settings' => array(
				'sales_id' => $this->sales_id,
				'api_url' => $this->api_url
			),
			'filters' => $filters,
			'listing' => $lodgingList
		);

		$result =  array(
			'#theme' => 'lodging_listing',
			'#data' => $data,
			'#cache' => array(
				'max-age' => 0,
			)
		);

		return $result;

	}



	private function displayQuickCart (){

		$cart = new CartController($this->inntopiaStorage, $this->requestStack);
		$result = $cart->displayCart();
		return $result;

	}




  
}
