<?php

namespace Drupal\inntopia;

use Origindesign\Inntopia\Lodging\LodgingHelper;


class InntopiaLodging {


	private $lodginHelper;

	private $salesId;

	private $api_url;

	private $arrivalDate;

	private $departureDate;

	private $adultCount;

	private $childrenCount;


    /**
     * Construct
     */
    public function __construct($salesId, $api_url) {

    	$this->salesId = $salesId;
		$this->api_url = $api_url;

		$this->lodging_helper = new LodgingHelper( $this->salesId, $this->api_url );

    }




	/**
     * Get Lodging List
     * @return array
     */
    public function getLodgingList( $params ){

		$this->arrivalDate = ( isset($params['arrivalDate']) ) ? $params['arrivalDate'] : date("Y-m-d", strtotime('+3 days'));
		$this->departureDate = ( isset($params['departureDate']) ) ? $params['departureDate'] : date("Y-m-d", strtotime('+6 days'));
		$this->adultCount =  ( isset($params['adultCount']) ) ? $params['adultCount'] : 2;
		$this->childrenCount =  ( isset($params['childrenCount']) ) ? $params['childrenCount'] : 0;

		$lodging_list = $this->lodging_helper->getLodgingList( $this->arrivalDate, $this->departureDate, $this->adultCount, $this->childrenCount);

		return $lodging_list;

    }




	/**
	 * Get Parameters set for the request
	 * @return array
	 */
	public function getParams() {
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
	public function getLodgingDetail( $params ){

		$supplierId = ( isset($params['supplierId']) ) ? $params['supplierId'] : null;
		$this->arrivalDate = ( isset($params['arrivalDate']) ) ? $params['arrivalDate'] : date("Y-m-d", strtotime('+3 days'));
		$this->departureDate = ( isset($params['departureDate']) ) ? $params['departureDate'] : date("Y-m-d", strtotime('+6 days'));
		$this->adultCount =  ( isset($params['adultCount']) ) ? $params['adultCount'] : 2;
		$this->childrenCount =  ( isset($params['childrenCount']) ) ? $params['childrenCount'] : 0;


		$lodging = $this->lodging_helper->getLodgingDetail($supplierId, $this->arrivalDate, $this->departureDate, $this->adultCount, $this->childrenCount);

		return $lodging;

	}







}