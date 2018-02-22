<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\InntopiaController.
 */
 
namespace Drupal\inntopia\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\inntopia\InntopiaStorage;
use Drupal\inntopia\InntopiaLodging;
use Origindesign\Inntopia\Session;



class InntopiaController extends ControllerBase {


	private $session;

	private $sales_id;

	private $api_url;



	/**
	 * InntopiaController constructor.
	 *
	 * @param \Drupal\inntopia\InntopiaStorage $inntopiaStorage
	 */
	public function __construct(InntopiaStorage $inntopiaStorage) {

		// Store settings in private variable
		$inntopiaStorage = $inntopiaStorage->getSettings();
		$this->sales_id = $inntopiaStorage['sales_id'];
		$this->api_url = $inntopiaStorage['server'];

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
			$container->get('inntopia.inntopia_storage')
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





	public function display() {

		$inntopiaLodging = new InntopiaLodging($this->sales_id, $this->api_url);
		$lodgingList = $inntopiaLodging->getLodgingList();

		$build[] =  [
			'#theme' => 'lodging_listing',
			'#data' => $lodgingList,
			'#cache' => array(
				'max-age' => 0,
			)
		];


        return $build;


    }


  
  
}
