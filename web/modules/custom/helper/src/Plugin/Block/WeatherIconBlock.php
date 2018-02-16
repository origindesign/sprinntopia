<?php

namespace Drupal\helper\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\helper\NodeHelper;
use Drupal\helper\GlobalHelper;
use Drupal\meteo\MeteoHelper;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
 * Provides a weather icon block for the header.
 *
 * @Block(
 *   id = "weather_icon_block",
 *   admin_label = @Translation("Weather Icon Block"),
 * )
 */

class WeatherIconBlock extends BlockBase implements ContainerFactoryPluginInterface {


    /**
     * @var \Drupal\helper\RenderHelper
     */
    protected $nodeHelper;


    /**
     * @var \Drupal\helper\GlobalHelper
     */
    protected $globalHelper;


    /**
     * @var \Drupal\meteo\MeteoHelper
     */
    protected $meteoHelper;


    /**
     * @var global settings data
     */
    protected $data;



    // Dependency injection pattern.
    public function __construct(array $configuration, $plugin_id, $plugin_definition, NodeHelper $nodeHelper, GlobalHelper $globalHelper, MeteoHelper $meteoHelper) {

      parent::__construct($configuration, $plugin_id, $plugin_definition);

      $this->nodeHelper = $nodeHelper;
      $this->globalHelper = $globalHelper;
      $this->meteoHelper = $meteoHelper;

    }


    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
     * @return static
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

      return new static(
        $configuration,
        $plugin_id,
        $plugin_definition,
        $container->get('helper.node_helper'),
        $container->get('helper.global_helper'),
        $container->get('meteo.meteo_helper')
      );

    }



    // Get Data
    private function setData(){

      // Load node fields
      $fieldSettings = $this->nodeHelper->getNodeFields(471, 'resort_settings');

      // Store Settings in Global Variable using Global Helper
      $this->globalHelper->setGlobalSettings($fieldSettings);
      $globalSettings = $this->globalHelper->getGlobalSettings();

      $this->data = $this->meteoHelper->getCurrentConditions( $globalSettings );

    }



    /**
     * Implements \Drupal\block\BlockBase::blockBuild().
     */
    public function build() {

      // Get data
      $this->setData();

      return array(
        '#type' => 'markup',
        '#markup' => '<a href="#" class="toggle weather" data-type="weather">
                      <span class="icon icon-'.$this->data['todayConditions'].'"></span>
                      <span class="temp">'.$this->data["midMtnTemp"].'</span>
                      <span class="degree">&deg;C</span>
                    </a>',
        '#cache' => array(
          'max-age' => 0,
        ),
      );
    }



  
}
