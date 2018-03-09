<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\InntopiaBaseController.
 */

namespace Drupal\inntopia\Controller;



use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Drupal\inntopia\InntopiaStorage;
use Origindesign\Inntopia\Session;



abstract class InntopiaBaseController extends ControllerBase {


	protected $session;


	protected $sales_id;


	protected $api_url;


	protected $requestStack;


	protected $inntopiaStorage;



	/**
	 * InntopiaBaseController constructor.
	 *
	 * @param \Drupal\inntopia\InntopiaStorage $inntopiaStorage
	 */
	public function __construct(InntopiaStorage $inntopiaStorage, RequestStack $requestStack) {

		// Store settings in private variable
		$this->inntopiaStorage = $inntopiaStorage;
		$this->requestStack = $requestStack;

		$settings = $this->inntopiaStorage->getSettings();
		$this->sales_id = $settings['sales_id'];
		$this->api_url = $settings['server'];

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
			$container->get('inntopia.storage'),
			$container->get('request_stack')
		);
	}







}