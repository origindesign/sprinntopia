<?php

/**
 * @file 
 * Contains \Drupal\includer\Controller\WeatherController
 */
 
namespace Drupal\includer\Controller;


/**
 * The specific class to gather includes and pass to template
 *
 */
class WeatherController extends ContentController {


    
    public function getIncludes () {

        // Define Helpers (not using dependency so Includer doesn't necessarily require Meteo module)
        $meteoHelper = \Drupal::service('meteo.meteo_helper');
        $lift_trails = \Drupal::service('lifts_trails.get_lifts_trails');
        $globalSettings = $this->globalHelper->getGlobalSettings();

        // Lifts/Trails open
        $season = 'winter';

        if($globalSettings["field_override_lifts_trails"] == "0"){
            $this->result["trails_open"] = $lift_trails->getOpenTrailsFromFeed();
            $this->result["lifts_open"] = $lift_trails->getOpenLiftsFromFeed();
        }else{
            $this->result["trails_open"] = $lift_trails->getLiftsTrailsFromCMS('trail', 1, $season);
            $this->result["lifts_open"] = $lift_trails->getLiftsTrailsFromCMS('lift', 1, $season);
        }


        if ( $meteoHelper->setCurrentRaw() ){

            $this->result["current_conditions"] = $meteoHelper->getCurrentConditions( $globalSettings );
            $this->result["snow_conditions"] = $meteoHelper->getSnowConditions( $globalSettings );
            $this->result["wind_conditions"] = $meteoHelper->getWindConditions();

        }


        $url = $this->nodeHelper->getFileUrl( 733, 'field_file') ;
        $this->result["avalanche_report"] = $url;

        return $this->result;

        
    }




    

}
