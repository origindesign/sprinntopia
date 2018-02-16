<?php
/**
 * @file
 * Contains \Drupal\meteo\Plugin\Provider\CustomWeatherFTP.
 */

namespace Drupal\meteo\Plugin\Provider;

use Drupal\meteo\ProviderBase;
use Drupal\Core\Datetime\DrupalDateTime;


/**
 * An CustomWeatherFTP provider plugin.
 *
 * @MeteoProvider(
 *   id = "customweatherftp",
 *   name = @Translation("CustomWeatherFTP")
 * )
 */
class CustomWeatherFTP extends ProviderBase {


    protected $ftp_hostname;
    protected $ftp_username;
    protected $ftp_password;
    protected $ftp_secured;
    protected $ftp_port;
    protected $store_it_raw;


    public function storeWeatherData( $settings, $storeItRaw = false ) {

        // Store FTP connection credential
        $this->ftp_hostname = $settings['hostname'];
        $this->ftp_username = $settings['username'];
        $this->ftp_password = $settings['password'];
        $this->ftp_secured = $settings['secured'];
        $this->ftp_port = $settings['port'];
        $this->store_it_raw = $storeItRaw;


        // Get local / Destination path
        $destination_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");

        // Loop over all different files to store them locally
        foreach ( $settings['files'] as $file ){

            $destination_file = $destination_path.$file['destination_file'];
            $source_file = $file['source_file'];

            // Connect to FTP and Get File
            $connection =  $this->getFile( $source_file, $destination_file );

            var_dump($connection);

            if ( $connection['success'] ){

                echo "Connection successful (No ".$connection['error_no'].": ".$connection['message'].")<br />";

                $this->loadJson( $destination_file, $source_file, $file['location_id'] );

            }else{
                return "Error trying to connect to SprWeather FTP service. Error No ".$connection['error_no'].": ".$connection['message'];
            }

        }

        return "Connection successful";


    }



    protected function getFile( $source_file, $destination_file ){

        $protocol = ( $this->ftp_secured ) ? 'ftps' : 'ftp';
        $source_url = $protocol.'://'.$this->ftp_hostname.":".$this->ftp_port.$source_file;
        $source_credentials =  $this->ftp_username.":".$this->ftp_password;

        var_dump($source_url);

        $curl = curl_init();
        $file = fopen($destination_file, 'w');
        curl_setopt($curl, CURLOPT_URL, $source_url);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FILE, $file);
        curl_setopt($curl, CURLOPT_USERPWD, $source_credentials);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        if($this->ftp_secured)curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); /* Disabled if not sftp */
        if($this->ftp_secured)curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); /* Disabled if not sftp */
        if($this->ftp_secured)curl_setopt($curl, CURLOPT_FTP_SSL, CURLOPT_FTPSSLAUTH);  /* Disabled if not sftp */
        if($this->ftp_secured)curl_setopt($curl, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS);  /* Disabled if not sftp */


        $result = curl_exec ($curl);
        $error_no = curl_errno($curl);
        $success = ($error_no == 0 || $error_no == 18) ? true : false; // if error is 0 or 18 (partial) then say success

        curl_close ($curl);
        fclose($file);

        return array(
            "success" => $success,
            "result" => $result,
            "error_no" => $error_no,
            "message" => curl_strerror($error_no)
        );

    }




    protected function loadJson( $local_file, $name_file, $location_id ){

        if (file_exists($local_file)) {

            $jsonData = file_get_contents($local_file);
            $jsonObj = json_decode($jsonData, true);
            $this->parseJsonCurrent( $jsonObj, $location_id );

        } else {

            exit('Failed to open JSON');

        }

    }




    protected function parseJsonCurrent( $json, $location_id ){

        /*
         * Idea for improvement : Check if this needs to be stored in raw table or in classic location system based on variable "store_it_raw"
         */

        $up_date_zone = "pacific";
        $up_date_time = $json["weatherdate"];
        //$up_date_time = "2016-11-01 08:00";
        $up_date = $this->getTime( $up_date_time, $up_date_zone );

        $date_data = array(
            'mrid' => 1,
            'raw_name' => 'weatherdate',
            'raw_data' => (string) $up_date->format("U"),
            'location_mlid' => $location_id
        );

        // Define key value for merge request
        $date_key = array( 'mrid' => 1 );
        // Update Date Data
        $this->meteoQueries->updateMeteoData('meteo_raw_feed', $date_key, $date_data);
        // Define default increment for mrid;
        $mrid = 1;


        foreach ($json['weather'] as $raw_name => $raw_data){
            $mrid++;
            $data = array(
                'mrid' => $mrid,
                'raw_name' => $raw_name,
                'raw_data' => $raw_data,
                'location_mlid' => $location_id
            );
            // Define key value for merge request
            $key = array( 'mrid' => $mrid );
            // Update Data
            $result = $this->meteoQueries->updateMeteoData('meteo_raw_feed', $key, $data);
            if ( $result ){
                echo("Meteo Data for Current weather Raw (".$raw_name.": ".$raw_data.") has been updated.<br>");
            }else{
                exit('Failed to add Current Weather Data in database. Issues on: '.$raw_name);
            }
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