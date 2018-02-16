<?php
/**
 * @file
 * Contains \Drupal\meteo\Annotation\MeteoProvider.
 */

namespace Drupal\meteo\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a MeteoProvider item annotation object.
 *
 * @Annotation
 */
class MeteoProvider extends Plugin {


    /**
     * The plugin ID.
     *
     * @var string
     */
    public $id;


    /**
     * The name of the provider.
     *
     * @var \Drupal\Core\Annotation\Translation
     *
     * @ingroup plugin_translatable
     */
    public $name;




}
