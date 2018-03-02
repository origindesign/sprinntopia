<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\ShoppingController.
 */
 
namespace Drupal\inntopia\Controller;


use Drupal\inntopia\InntopiaLodging;



class ShoppingController extends InntopiaBaseController {


	public function displayContainer() {

		// Get parameters
		$params = $this->requestStack->getCurrentRequest()->query->all();

		// Get Listing
		$inntopiaLodging = new InntopiaLodging($this->sales_id, $this->api_url, $params);
		$filters = $inntopiaLodging->getFilters();

		$data = array(
			'filters' => $filters,
		);

		// Format Listing
		$build[] =  [
			'#theme' => 'lodging_container',
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




	public function displayProduct() {

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
