<?php
/**
 * @file
 * Provides Drupal\meteo\ProviderBase.
 */

namespace Drupal\meteo;

use Drupal\Core\Plugin\PluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * A base for the provider plugins.
 */
class ProviderBase extends PluginBase implements ProviderInterface {


    protected $meteoQueries;


    /**
     * ProviderBase constructor.
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
     * @param \Drupal\meteo\MeteoQueries $meteoQueries
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, MeteoQueries $meteoQueries) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->meteoQueries = $meteoQueries;
    }



    /**
     * @param ContainerInterface $container
     * @param array $configuration
     * @param $plugin_id
     * @param $plugin_definition
     * @return static
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('meteo.query_meteo')
        );

    }


    /**
     * {@inheritdoc}
     */
    public function getName() {
        return $this->pluginDefinition['name'];
    }


    /**
     * {@inheritdoc}
     */
    public function storeWeatherData( $settings ) {
        return false;
    }


}
