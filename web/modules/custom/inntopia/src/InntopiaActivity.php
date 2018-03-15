<?php

namespace Drupal\inntopia;

use Origindesign\Inntopia\Activity\ActivityHelper;


class InntopiaActivity {


	private $salesId;

	private $api_url;

	private $supplierId;

	private $productId;

	private $startDate;

	private $productSuperCategoryId;

	private $productCategoryId;

	private $groupId;

	private $quantity;

	private $activity_helper;


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
		$this->productId = ( isset($params['productId']) ) ? $params['productId'] : null;
		$this->startDate = ( isset($params['startDate']) ) ? $params['startDate'] : date("Y-m-d", strtotime('+3 days'));
		$this->productSuperCategoryId = ( isset($params['productSuperCategoryId']) ) ? $params['productSuperCategoryId'] : 2;
		$this->productCategoryId =  ( isset($params['productCategoryId']) ) ? $params['productCategoryId'] : null;
		$this->groupId =  ( isset($params['groupId']) ) ? $params['groupId'] : null;
		$this->quantity =  ( isset($params['quantity']) ) ? $params['quantity'] : 1;

		// Instantiate Lodging Helper from Library
		$this->activity_helper = new ActivityHelper( $this->salesId, $this->api_url );

    }



	/**
	 * Get Filters from Parameters set for the request
	 * @return array
	 */
	public function getFilters() {
		return array(
			"startDate" => $this->startDate,
			"productSuperCategoryId" => $this->productSuperCategoryId,
			"productCategoryId" => $this->productCategoryId,
			"quantity" => $this->quantity
		);
	}





	/**
     * Get Lodging List
     * @return array
     */
    public function getListing(){

		$listing = $this->activity_helper->getActivityList( $this->startDate, $this->productSuperCategoryId, $this->productCategoryId, $this->quantity);

		return $listing;

    }


	/**
	 * Get Lodging List
	 * @return array
	 */
	public function getDetail(){

		$detail = $this->activity_helper->getActivityDetail($this->supplierId, $this->productId, $this->startDate, $this->groupId, $this->productSuperCategoryId);

		return $detail;

	}







}