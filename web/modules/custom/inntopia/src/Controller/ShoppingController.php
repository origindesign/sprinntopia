<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\ShoppingController.
 */
 
namespace Drupal\inntopia\Controller;


use Drupal\inntopia\InntopiaActivity;
use Drupal\inntopia\InntopiaLodging;



class ShoppingController extends InntopiaBaseController {


	public function dispatch($method = 'lodging', $type = null) {

		switch ($method){
			case "activities": $result = $this->displayContainer('activities_container'); break;
			case "lodging": $result = $this->displayContainer('lodging_container'); break;
			case "product": $result = $this->displayProduct($type); break;
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
	private function displayProduct($type) {

		// Get parameters
		$params = $this->requestStack->getCurrentRequest()->query->all();

		// Get Listing
		switch ($type) {
			case "lodging":
				$instance = new InntopiaLodging($this->sales_id, $this->api_url, $params);
				$theme = 'lodging_detail';
				break;
			case "activity":
				$instance = new InntopiaActivity($this->sales_id, $this->api_url, $params);
				$theme = 'activity_detail';
				break;
			default:
				$instance = new InntopiaLodging($this->sales_id, $this->api_url, $params);
				$theme = 'lodging_detail';

		}

		$detail = $instance->getDetail();
		$filters = $instance->getSidebarData();

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
			'#theme' => $theme,
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
	 *
	 * @TODO refactor the dispatch of the sidebar so product is handled in a better way
	 */
	public function displaySidebar( $method ) {

		// Get parameters
		$params = $this->requestStack->getCurrentRequest()->query->all();

		// Get Arguments
		$path_args = $this->requestStack->getCurrentRequest()->getPathInfo();
		$args = explode('/', substr($path_args, 1));


		switch ($method){
			case "activities":
				$instance = new InntopiaActivity($this->sales_id, $this->api_url, $params);
				$theme = 'activities_filters';
				break;
			case "lodging":
				$instance = new InntopiaLodging($this->sales_id, $this->api_url, $params);
				$theme = 'lodging_filters';
				break;
			case "product":
				if($args[2] == 'activity'){
					$instance = new InntopiaActivity($this->sales_id, $this->api_url, $params);
					$theme = 'activities_filters';
				}else{
					$instance = new InntopiaLodging($this->sales_id, $this->api_url, $params);
					$theme = 'lodging_filters';
				}
				break;
			default:
				$instance = new InntopiaLodging($this->sales_id, $this->api_url, $params);
				$theme = 'lodging_filters';
		}

		$filters = $instance->getSidebarData();

		$data = array(
			'filters' => $filters,
			'args' => $args
		);

		// Format Listing
		$build[] =  [
			'#theme' => $theme,
			'#data' => $data,
			'#cache' => array(
				'max-age' => 0,
			)
		];

		// Return listing ready for display
		return $build;


	}

  
}
