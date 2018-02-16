<?php
/**
 * @file Contains \Drupal\helper\DateFieldHelper
 */

namespace Drupal\helper;

use Drupal\Core\Render\Renderer;
use Drupal\Core\Datetime\DrupalDateTime;


/**
 * A Class for helping rendering fields in templates
 *
 */
class DateFieldHelper {


    protected $rendererService;


    /**
     * Class constructor.
     * @param Renderer $rendererService
     */
    public function __construct( Renderer $rendererService) {
        $this->rendererService = $rendererService;

    }


    public function getRawDates ($node, $fieldName){

        if ( isset($node->{$fieldName}) ){

            $result = array();

            $date_field = ($node->{$fieldName}->getValue());

            $fromDate = new DrupalDateTime($date_field[0]['value'], 'UTC');

            $result['from'] = $fromDate->format("U");
            $result['to'] = false;

            if ( isset($date_field[1]) ){
                $toDate = new DrupalDateTime($date_field[1]['value'], 'UTC');
                $result['to'] =  $toDate->format("U");
            }

            return $result;

        }

        return false;

    }





}
