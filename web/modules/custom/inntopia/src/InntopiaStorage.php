<?php

namespace Drupal\inntopia;

use Drupal\Core\Config\ConfigFactoryInterface;


class InntopiaStorage {


    /**
     * Config factory.
     * @var \Drupal\Core\Config\ConfigFactoryInterface
     */
    protected $configFactory;



    /**
     * Construct
     * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
     */
    public function __construct(ConfigFactoryInterface $config_factory) {
        $this->configFactory = $config_factory;
    }



    /**
     * Get Inntopia Setttings from config
     * @return array
     */
    public function getSettings(){

        $config = $this->configFactory->get('inntopia.settings');

        // Define API url based on server
        switch ( $config->get('inntopia_server') ){
			case "live": $subdomain = "www"; break;
			case "stage": $subdomain = "stage"; break;
			case "dev": $subdomain = "dev"; break;
			default: $subdomain = "stage";
		}

		// Put result in array
        $data = array(
            'sales_id' => $config->get('inntopia_sales_id'),
			'server' => "https://".$subdomain.".inntopia.travel/ecomm/"
        );

        return $data;

    }



}