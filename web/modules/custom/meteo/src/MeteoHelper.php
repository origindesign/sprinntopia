<?php

/**
 * @file
 * Contains \Drupal\meteo\MeteoHelper.
 */

namespace Drupal\meteo;

use Drupal\Core\Render\Renderer;
use Drupal\Core\Datetime\DrupalDateTime;




/**
 * Provides the MeteoHelper function within service of meteos.
 */
class MeteoHelper {


    protected $rendererService;
    protected $meteoQueries;
    protected $currentConditions;


    /**
     * Class constructor.
     * @param Renderer $rendererService
     * @param MeteoQueries $meteoQueries
     */
    public function __construct( Renderer $rendererService, MeteoQueries $meteoQueries) {
        $this->rendererService = $rendererService;
        $this->meteoQueries = $meteoQueries;


    }



    /**
     * Initiate Weather with Raw Table
     * @return bool
     */
    public function setCurrentRaw (){

        $this->currentConditions = $this->getAllRawData();

        if($this->currentConditions){

            return true;

        }

        return false;

    }


    /**
     * Return given field or override if given
     * @return mixed
     */
    public function getAllRawData () {

        // Get all data from db
        $allRawData = $this->meteoQueries->getMultipleRows( 'meteo_raw_feed', 1 );

        // Transform result into array
        $result = array();
        foreach ($allRawData as $dataRaw){
            $result[(string) $dataRaw->raw_name] = (string) $dataRaw->raw_data;
        }
        return $result;
    }





    /**
     * Return given field or override if given
     * @param $field
     * @param $override
     * @return bool
     */
    public function getSingleData ($field, $override = null ){

        if ( is_null($override) ){

            $current = $this->currentConditions;

            if( $current ){
                if ( array_key_exists($field, $current)){
                    return $current[$field];
                }
                return false;
            }
            return false;

        }else{
            return $override;
        }

    }




    /**
     * Return Current Conditions based on raw table unless there is an override
     * @return mixed
     */
    public function getCurrentConditions ( $globalSettings ) {

        $result = array();

        // Current Conditions
        $result['todayConditions'] = $globalSettings['field_today_conditions'];
        $result['todayDescription'] = $globalSettings['field_today_description'];
        $result['weatherdate'] = $this->getSingleData( 'weatherdate' );
        $result['midMtnTemp'] = $this->getSingleData( 'midMtnTemp', $globalSettings['field_midmtntemp']  );
        $result['TOTWTemp'] = $this->getSingleData( 'TOTWTemp', $globalSettings['field_totwtemp']  );
        $result['moExTemp'] = $this->getSingleData( 'moExTemp', $globalSettings['field_moextemp']  );
        $result['valleyTemp'] = $this->getSingleData( 'valleyTemp', $globalSettings['field_valleytemp']  );

        return $result;

    }



    /**
     * Return Snow Conditions based on raw table unless there is an override
     * @return mixed
     */
    public function getSnowConditions ( $globalSettings ) {

        $result = array();

        // Current Conditions
        $result['snow_description'] = $globalSettings['field_snow_description'];
        $result['mid_1600_1600'] = $this->getSingleData( 'Mid_Laser_1600_1600', $globalSettings['field_mid_1600_1600']  );
        $result['midMtnDepth'] = $this->getSingleData( 'midMtnDepth', $globalSettings['field_midmtndepth']  );
        $result['midMtnStorm1hr'] = $this->getSingleData( 'midMtnStorm1hr', $globalSettings['field_midmtnstorm1hr']  );
        $result['midMtnStorm12hr'] = $this->getSingleData( 'midMtnStorm12hr', $globalSettings['field_midmtnstorm12hr']  );
        $result['midMtnStorm24hr'] = $this->getSingleData( 'midMtnStorm24hr', $globalSettings['field_midmtnstorm24hr']  );
        $result['midMtnStorm48hr'] = $this->getSingleData( 'midMtnStorm48hr', $globalSettings['field_midmtnstorm48hr']  );
        $result['midMtnStorm7Day'] = $this->getSingleData( 'midMtnStorm7Day', $globalSettings['field_midmtnstorm7day']  );
        $result['alpineSnowDepth'] = $this->getSingleData( 'alpineSnowDepth', $globalSettings['field_alpinesnowdepth']  );
        $result['alpineStorm1hr'] = $this->getSingleData( 'alpineStorm1hr', $globalSettings['field_alpinestorm1hr']  );
        $result['alpineStorm12hr'] = $this->getSingleData( 'alpineStorm12hr', $globalSettings['field_alpinestorm12hr']  );
        $result['alpineStorm24hr'] = $this->getSingleData( 'alpineStorm24hr', $globalSettings['field_alpinestorm24hr']  );
        $result['alpineStorm48hr'] = $this->getSingleData( 'alpineStorm48hr', $globalSettings['field_alpinestorm48hr']  );
        $result['alpineStorm7Day'] = $this->getSingleData( 'alpineStorm7Day', $globalSettings['field_alpinestorm7day']  );


        return $result;

    }





    /**
     * Return Wind Speeds from DB using raw data (no override)
     * @return mixed
     */
    public function getWindConditions () {

        $result = array();

        // Wind Speed
        $result['crystalWindSpeed'] = $this->getSingleData( 'crystalWindSpeed' );
        $result['crystalAVGWindDir'] = $this->getSingleData( 'crystalAVGWindDir' );
        $result['TOTWWindSpeed'] = $this->getSingleData( 'TOTWWindSpeed' );
        $result['TOTWAVGWindDir'] = $this->getSingleData( 'TOTWAVGWindDir' );
        $result['sunburstWindSpeed'] = $this->getSingleData( 'sunburstWindSpeed' );
        $result['sunburstAVGWindDir'] = $this->getSingleData( 'sunburstAVGWindDir' );
        $result['sundanceWindSpeed'] = $this->getSingleData( 'sundanceWindSpeed' );
        $result['sundanceAVGWindDir'] = $this->getSingleData( 'sundanceAVGWindDir' );
        $result['moExWindSpeed'] = $this->getSingleData( 'moExWindSpeed' );
        $result['moExAVGWindDir'] = $this->getSingleData( 'moExAVGWindDir' );


        return $result;

    }






    /**
     * Get updated time
     * @param $machine_name
     * @param $location_id
     * @return string
     */
    public function getUpdateTime ( $machine_name, $location_id ){

        $datetime = (int) $this->meteoQueries->getSingleData( 'meteo_weather_data', 'updated_time', $machine_name, $location_id );

        $result = DrupalDateTime::createFromTimestamp($datetime);

        return $result->format('M jS g:ia');

    }




}
