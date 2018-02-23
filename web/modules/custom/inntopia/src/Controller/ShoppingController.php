<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\ShoppingController.
 */
 
namespace Drupal\inntopia\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Drupal\inntopia\InntopiaStorage;
use Drupal\inntopia\InntopiaLodging;
use Origindesign\Inntopia\Session;



class ShoppingController extends ControllerBase {


	private $session;

	private $sales_id;

	private $api_url;

	private $requestStack;

	private $inntopiaLodging;


	/**
	 * InntopiaController constructor.
	 *
	 * @param \Drupal\inntopia\InntopiaStorage $inntopiaStorage
	 */
	public function __construct(InntopiaStorage $inntopiaStorage, RequestStack $requestStack) {

		// Store settings in private variable
		$inntopiaStorage = $inntopiaStorage->getSettings();
		$this->sales_id = $inntopiaStorage['sales_id'];
		$this->api_url = $inntopiaStorage['server'];
		$this->requestStack = $requestStack;

		// If there is no session set, set one using Inntopia Session
		if ( !isset($_SESSION["inntopia"]) ){

			$inntopiaSession = new Session( $this->sales_id, $this->api_url);

			$_SESSION["inntopia"] = $inntopiaSession->getSessionId();

		}

		$this->session = $_SESSION["inntopia"];

	}



	/**
	 * @param ContainerInterface $container
	 *
	 * @return static
	 */
	public static function create(ContainerInterface $container) {
		return new static(
			$container->get('inntopia.inntopia_storage'),
			$container->get('request_stack')
		);
	}




	public function info() {

		$build[] = array(
			'#type' => 'markup',
			'#markup' => t('<p>Sales ID: '.$this->sales_id.'</p><p>Server: '.$this->api_url.'</p><p>Session is: '.$this->session.'</p>'),
		);

		/*
		 * Invalidate cache so the cron will always be run
		 */
		$build['#cache']['max-age'] = 0;

		return $build;


	}



	public function displayLodging() {

		// Get parameters
		$params = $this->requestStack->getCurrentRequest()->query->all();

		// Get Listing
		$inntopiaLodging = new InntopiaLodging($this->sales_id, $this->api_url);
		$lodgingList = $inntopiaLodging->getLodgingList($params);
		$filters = $inntopiaLodging->getParams();

		$data = array(
			'filters' => $filters,
			'listing' => $lodgingList
		);

		// Format Listing
		$build[] =  [
			'#theme' => 'lodging_listing',
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
		$inntopiaLodging = new InntopiaLodging($this->sales_id, $this->api_url);
		$lodging = $inntopiaLodging->getLodgingDetail($params);

		$data = $lodging;

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
