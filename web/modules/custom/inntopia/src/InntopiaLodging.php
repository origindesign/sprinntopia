<?php

namespace Drupal\inntopia;

use Origindesign\Inntopia\Lodging\LodgingHelper;


class InntopiaLodging {


	private $salesId;

	private $api_url;

	private $arrivalDate;

	private $departureDate;

	private $adultCount;


    /**
     * Construct
     */
    public function __construct($salesId, $api_url) {

    	$this->salesId = $salesId;
		$this->api_url = $api_url;

		$this->arrivalDate = date("Y-m-d", strtotime('+3 days'));
		$this->departureDate = date("Y-m-d", strtotime('+6 days'));
		$this->adultCount = 2;

    }



    /**
     * Get Lodging List
     * @return array
     */
    public function getLodgingList(){

		$lodging_helper = new LodgingHelper( $this->salesId, $this->api_url );
		$lodging_list = $lodging_helper->getLodgingList( $this->arrivalDate, $this->departureDate, $this->adultCount);

		return $lodging_list;

    }



}