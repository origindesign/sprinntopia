<?php
/**
 * @file
 * Contains \Drupal\inntopia\Controller\InntopiaController.
 */
 
namespace Drupal\inntopia\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;


class InntopiaController extends ControllerBase implements ContainerInjectionInterface {


    public function display() {

        $build[] = array(
            '#type' => 'markup',
            '#markup' => t('<p>This is a test for Inntopia</p>'),
        );

        /*
         * Invalidate cache so the cron will always be run
         */
        $build['#cache']['max-age'] = 0;

        return $build;


    }


  
  
}
