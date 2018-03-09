<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\ShoppingController.
 */
 
namespace Drupal\inntopia\Controller;


use Drupal\inntopia\InntopiaLodging;



class ShoppingController extends InntopiaBaseController {


	public function dispatch($method = 'lodging') {

		switch ($method){
			case "activities": $result = $this->displayContainer('activities_container'); break;
			case "lodging": $result = $this->displayContainer('lodging_container'); break;
			case "product": $result = $this->displayProduct(); break;
			default: $result = $this->displayContainer();
		}

		return $result;

    }


	/**
	 * Display main container (loading ajax inside template)
	 * @return array
	 */
    private function displayContainer($theme){
		$build[] =  ['#theme' => $theme];
		return $build;
	}


	/**
	 * Display Lodging Product
	 * @return array
	 */
	private function displayProduct() {

		// Get parameters
		$params = $this->requestStack->getCurrentRequest()->query->all();

		// Get Listing
		$inntopiaLodging = new InntopiaLodging($this->sales_id, $this->api_url, $params);
		$detail = $inntopiaLodging->getLodgingDetail();
		$filters = $inntopiaLodging->getFilters();

		$data = array(
			'settings' => array(
				'sales_id' => $this->sales_id,
				'api_url' => $this->api_url
			),
			'filters' => $filters,
			'detail' => $detail
		);

		// Format Listing
		$build[] =  [
			'#theme' => 'lodging_detail',
			'#data' => $data,
			'#cache' => array(
				'max-age' => 0,
			)
		];

		// Return listing ready for display
		return $build;
	}




	/**
	 * Display Lodging Sidebar
	 * @return array
	 */
	public function displaySidebar( $method ) {

		// Get parameters
		$params = $this->requestStack->getCurrentRequest()->query->all();

		switch ($method){
			case "lodging":
				$instance = new InntopiaLodging($this->sales_id, $this->api_url, $params);
				$theme = 'lodging_filters';
				break;
			default:
				$instance = new InntopiaLodging($this->sales_id, $this->api_url, $params);
				$theme = 'lodging_filters';
		}

		$filters = $instance->getFilters();

		// Format Listing
		$build[] =  [
			'#theme' => $theme,
			'#data' => $filters
		];

		// Return listing ready for display
		return $build;


	}

  
}
