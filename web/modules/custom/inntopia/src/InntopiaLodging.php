<?php

namespace Drupal\inntopia;

use Origindesign\Inntopia\Lodging\LodgingHelper;


class InntopiaLodging {


	private $salesId;

	private $api_url;

	private $supplierId;

	private $arrivalDate;

	private $departureDate;

	private $adultCount;

	private $childrenCount;

	private $lodging_helper;


	/**
	 * Construct
	 *
	 * @param $salesId
	 * @param $api_url
	 * @param $params
	 */
    public function __construct($salesId, $api_url, $params) {

		// Define settings
    	$this->salesId = $salesId;
		$this->api_url = $api_url;

		// Define parameters
		$this->supplierId = ( isset($params['supplierId']) ) ? $params['supplierId'] : null;
		$this->arrivalDate = ( isset($params['arrivalDate']) ) ? $params['arrivalDate'] : date("Y-m-d", strtotime('+3 days'));
		$this->departureDate = ( isset($params['departureDate']) ) ? $params['departureDate'] : date("Y-m-d", strtotime('+6 days'));
		$this->adultCount =  ( isset($params['adultCount']) ) ? $params['adultCount'] : 2;
		$this->childrenCount =  ( isset($params['childrenCount']) ) ? $params['childrenCount'] : 0;

		// Instantiate Lodging Helper from Library
		$this->lodging_helper = new LodgingHelper( $this->salesId, $this->api_url );

    }



	/**
	 * Get Filters from Parameters set for the request
	 * @return array
	 */
	public function getSidebarData() {
		return array(
			"arrivalDate" => $this->arrivalDate,
			"departureDate" => $this->departureDate,
			"adultCount" => $this->adultCount,
			"childrenCount" => $this->childrenCount
		);
	}





	/**
     * Get Lodging List
     * @return array
     */
    public function getListing(){

		$listing = $this->lodging_helper->getLodgingList( $this->arrivalDate, $this->departureDate, $this->adultCount, $this->childrenCount);

		return $listing;

    }


	/**
	 * Get Lodging List
	 * @return array
	 */
	public function getDetail(){

		$detail = $this->lodging_helper->getLodgingDetail($this->supplierId, $this->arrivalDate, $this->departureDate, $this->adultCount, $this->childrenCount);

		return $detail;

	}







}