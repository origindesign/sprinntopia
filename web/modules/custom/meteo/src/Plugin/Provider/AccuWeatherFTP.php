<?php
/**
 * @file
 * Contains \Drupal\meteo\Plugin\Provider\AccuWeatherFTP.
 */

namespace Drupal\meteo\Plugin\Provider;

use Drupal\meteo\ProviderBase;
use Drupal\Core\Datetime\DrupalDateTime;


/**
 * An AccuWeatherFTP provider plugin.
 *
 * @MeteoProvider(
 *   id = "accuweatherftp",
 *   name = @Translation("AccuWeatherFTP")
 * )
 */
class AccuWeatherFTP extends ProviderBase {


    protected $ftp_hostname;
    protected $ftp_username;
    protected $ftp_password;


    public function storeWeatherData( $settings ) {

        // Store FTP connection credential
        $this->ftp_hostname = $settings['hostname'];
        $this->ftp_username = $settings['username'];
        $this->ftp_password = $settings['password'];


        // Get local / Destination path
        $destination_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");

        // Loop over all different files to store them locally
        foreach ( $settings['files'] as $file ){

            $destination_file = $destination_path.$file['destination_file'];
            $source_file = $file['source_file'];

            // Connect to FTP and Get File
            $connection =  $this->getFile( $source_file, $destination_file );

            if ( $connection['success'] ){

                $this->loadXML( $destination_file, $source_file, $file['location_id'] );

            }else{
                return "Error trying to connect to AccuWeather FTP service. Error No ".$connection['error_no'].": ".$connection['message'];
            }

        }

        return "Connection successful";


    }



    protected function getFile( $source_file, $destination_file ){

        $source_url = 'ftp://'.$this->ftp_hostname.$source_file;
        $source_credentials =  $this->ftp_username.":".$this->ftp_password;

        $curl = curl_init();
        $file = fopen($destination_file, 'w');
        curl_setopt($curl, CURLOPT_URL, $source_url);
        curl_setopt($curl, CURLOPT_VERBOSE, true);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FILE, $file);
        curl_setopt($curl, CURLOPT_USERPWD, $source_credentials);

        $result = curl_exec ($curl);
        $error_no = curl_errno($curl);

        curl_close ($curl);
        fclose($file);

        return array(
            "success" => $result,
            "error_no" => $error_no,
            "message" => curl_strerror($error_no)
        );

    }




    protected function loadXML( $local_file, $name_file, $location_id ){

        if (file_exists($local_file)) {

            $xml = simplexml_load_file($local_file);

            switch ($name_file){

                case "/current.xml": $this->parseXMLCurrent( $xml, $location_id ); break;
                case "/forecast.xml": $this->parseXMLForecast( $xml, $location_id ); break;

            }

        } else {

            exit('Failed to open XLM');

        }

    }




    protected function parseXMLCurrent( $xml, $location_id ){

        // Update meteo table only if "current" node exists in XML
        if ( isset($xml->current) ){

            // Get Updated Time and format it in Drupal Time
            $up_date_zone = (string) $xml->created->attributes()->time_zone;
            $up_date_time = (string) $xml->created;
            $up_date = $this->getTime( $up_date_time, $up_date_zone );

            // Parse and Store Data
            $data = array(
                'mwid' => 1,
                'machine_name' => 'day_1',
                'day_name' => 'today',
                'updated_time' => $up_date->format("U"),
                'message' => ucfirst ( strtolower ( (string) $xml->current->phrase ) ),
                'icon_ref' => (string) $xml->current->icon,
                'high_temp' => (string) $xml->current->temp,
                'low_temp' => (string) $xml->current->temp,
                'wind_speed' => (string) $xml->current->windspeed,
                'wind_dir' => (string) $xml->current->wind_dir,
                'location_mlid' => $location_id
            );

            // Define key value for merge request
            $key = array( 'mwid' => 1 );

            // Update Table meteo_weather_data
            $result = $this->meteoQueries->updateMeteoData('meteo_weather_data', $key, $data);

            if ( $result ){

                echo("Meteo Data for Current weather (mwid: ".$result.") has been updated.<br>");

            }else{

                exit('Failed to add Current Weather Data in database.');

            }

        }else{

            exit("Node 'current' doesn't exist in given XML ");

        }


    }



    protected function parseXMLForecast( $xml, $location_id  ){

        // Update meteo table only if "current" node exists in XML
        if ( isset($xml->forecast) ){

            // Get Updated Time and format it in Drupal Time
            $up_date_zone = (string) $xml->timestamp->attributes()->zone;
            $up_date_time = (string) $xml->timestamp;
            $up_date = $this->getTime( $up_date_time, $up_date_zone );
            $i = 2;

            // Loop through all forecast days
            foreach($xml->forecast->day as $day){

                // Parse and Store Data
                $data = array(
                    'mwid' => $i,
                    'machine_name' => 'day_'.$i,
                    'day_name' => strtolower( (string) $day->name ),
                    'updated_time' => $up_date->format("U"),
                    'message' => (string) $day->phrase,
                    'icon_ref' => (string) $day->icon,
                    'high_temp' => (string) $day->high,
                    'low_temp' => (string) $day->low,
                    'location_mlid' => $location_id
                );

                // Define key value for merge request
                $key = array( 'mwid' => $i );

                // Update Table meteo_weather_data
                $result = $this->meteoQueries->updateMeteoData('meteo_weather_data', $key, $data);

                if ( $result ){

                    echo("Meteo Data for Current weather (mwid: ".$result.") has been updated.<br>");

                }else{

                    exit('Failed to add Current Weather Data in database.');

                }

                $i++;

            }

        }else{

            exit("Node 'current' doesn't exist in given XML ");

        }

    }




    protected function getTime ( $date, $timezone ){

        $array_zone = array(
            'eastern' => 'America/New_York',
            'central' => 'America/Chicago',
            'mountain' => 'America/Denver',
            'pacific' => 'America/Los_Angeles',
            'alaska' => 'America/Anchorage',
            'hawaii' => 'America/Adak'
        );

        if (array_key_exists($timezone, $array_zone)) {
            $zone =  $array_zone[$timezone];
        }else{
            $zone =  'UTC';
        }

        $datetime = new DrupalDateTime($date, $zone);

        return $datetime;

    }





}