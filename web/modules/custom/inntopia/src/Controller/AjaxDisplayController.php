<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\AjaxDisplayController.
 */
 
namespace Drupal\inntopia\Controller;


use Drupal\inntopia\InntopiaLodging;
use Drupal\inntopia\InntopiaActivity;


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
				case "listing-activity": $result = $this->activityListing( $this->data ); break;
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



	private function activityListing( $params ) {

		// Set default data if it's not set
		if ( !isset($params) ){
			$params = array(
				"startDate" => date("Y-m-d", strtotime('+3 days')),
				"productSuperCategoryId" => 2,
			);
		}

		$inntopiaActivity = new InntopiaActivity($this->sales_id, $this->api_url, $params);
		$activityList = $inntopiaActivity->getListing();
		$filters = $inntopiaActivity->getFilters();

		$data = array(
			'settings' => array(
				'sales_id' => $this->sales_id,
				'api_url' => $this->api_url
			),
			'filters' => $filters,
			'listing' => $activityList
		);

		$result =  array(
			'#theme' => 'activities_listing',
			'#data' => $data,
			'#cache' => array(
				'max-age' => 0,
			)
		);

		return $result;

	}



	private function lodgingListing( $params ){

		// Set default data if it's not set
		if ( !isset($params) ){
			$params = array(
				"arrivalDate" => date("Y-m-d", strtotime('+3 days')),
				"departureDate" => date("Y-m-d", strtotime('+6 days')),
				"adultCount" => 2,
				"childrenCount" => 0
			);
		}

		$inntopiaLodging = new InntopiaLodging($this->sales_id, $this->api_url, $params);
		$lodgingList = $inntopiaLodging->getListing();
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
