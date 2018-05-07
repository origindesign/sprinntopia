<?php

namespace Drupal\inntopia\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
 * Provides a Inntopia Check Availability Block
 *
 * @Block(
 *   id = "inntopia_check_block",
 *   admin_label = @Translation("Inntopia Check Availability Block"),
 * )
 */

class InntopiaCheckBlock extends BlockBase implements ContainerFactoryPluginInterface {


    /**
     * InntopiaCheckBlock constructor.
     *
     * @param array $configuration
     * @param string $plugin_id
     * @param mixed $plugin_definition
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition) {

      parent::__construct($configuration, $plugin_id, $plugin_definition);

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
        $plugin_definition
      );

    }



    /**
     * Implements \Drupal\block\BlockBase::blockBuild().
     */
    public function build() {

        $data = array( 'section' => 'lodging');

        return array(
            '#theme' => 'widget_check_availability',
            '#data' => $data
        );
    }



  
}
