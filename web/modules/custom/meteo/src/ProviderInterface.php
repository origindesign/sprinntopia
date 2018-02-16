<?php
/**
 * @file
 * Provides Drupal\meteo\ProviderInterface
 */

namespace Drupal\meteo;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;


/**
 * An interface for meteo providers.
 */
interface ProviderInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {

    /**
     * Return the name of the ice cream flavor.
     *
     * @return string
     */
    public function getName();


    /**
     * Return the weather data.
     *
     * @param array $settings
     * @return bool
     */
    public function storeWeatherData( $settings );



}
