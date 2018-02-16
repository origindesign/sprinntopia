<?php
/**
 * @file Contains \Drupal\meteo\MeteoQueries
 */

namespace Drupal\meteo;

use Drupal\Core\Database\Connection;


/**
 * A Service for querying in meteo tables.
 *
 */
class MeteoQueries {


    protected $connection;


    /**
     * MeteoQueries constructor.
     */
    public function __construct( Connection $connection ) {
        $this->connection  = $connection;
    }


    /**
     * Insert Location
     * @param int $mlid
     * @param $name
     * @param $description
     * @param $service
     * @return int
     * @internal param array $data
     */
    public function createLocation ( $mlid, $name, $description, $service ) {

        $query = $this->connection->merge('meteo_location');
        $query->key(array(
            'mlid' => $mlid
        ));
        $query->fields(array(
            'mlid' => $mlid,
            'name' => $name,
            'description' => $description,
            'data_provider' => $service
        ));

        if ( $query->execute() ){
            return $mlid;
        }

        return false;

    }



    /*
     * Update a Row of a given table, based on the custom key provided
     */
    public function updateMeteoData( $table, $key, $data ){

        // Merge data to database in proper table
        $query = $this->connection->merge($table);
        $query->key($key);
        $query->fields($data);

        if ( $query->execute() ){
            $first_key = key($key);
            return $key[$first_key];
        }

        return false;

    }





    /*
     * Get all data of a single row base on machine_name (ie: day_0)
     */
    public function getAllDataOfRow ( $table, $machine_name, $mlid  ){

        $query = $this->connection->select($table, 'mwd');
        $query->fields('mwd');
        $query->condition( 'mwd.location_mlid', $mlid);
        $query->condition( 'mwd.machine_name', $machine_name);
        $query->range(0, 1);
        $result = $query->execute();

        if ( $result ){
            return $result->fetchAssoc();
        }

        return false;

    }



    /*
     * Get all data of multiple rows based on location
     */
    public function getMultipleRows ( $table, $mlid  ){

        $query = $this->connection->select($table, 'mwd');
        $query->fields('mwd');
        $query->condition( 'mwd.location_mlid', $mlid);
        $result = $query->execute();

        if ( $result ){
            return $result->fetchAll();
        }

        return false;

    }



    /*
     * Get a single field from a single row given a  machine_name
     */
    public function getSingleData( $table, $field, $machine_name, $mlid ){

        // Single Value of a field
        $query = $this->connection->select($table, 'mwd');
        $query->addField( 'mwd', $field );
        $query->condition( 'mwd.location_mlid', $mlid);
        $query->condition( 'mwd.machine_name', $machine_name);
        $query->range(0, 1);
        $result = $query->execute();

        if ( $result ){
            return $result->fetchField();
        }

        return false;

    }














}
