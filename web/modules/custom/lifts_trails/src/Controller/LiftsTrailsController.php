<?php
/**
 * @file
 * Contains \Drupal\lifts_trails\Controller\LiftsTrailsController.
 */
 
namespace Drupal\lifts_trails\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\ftp_connect\FtpConnection;
use Drupal\efq\EfqQueryEntities;


class LiftsTrailsController extends ControllerBase {


	protected $connection;
    protected $ftpConnection;
    protected $efqQueryEntities;
    protected $ignoreNames = array('5 Mile Lower','Munro Ridge Lower','Sun Downer Lower','Sundance Lower','Exhibition Lower','Chute Lower');
    protected $ignoreFromFeed = array('Drift Line','Easy Out','Little Headwalls','Rollers','Static Cling Return');
    protected $upperLowerCombinations = array(
        array('5 Mile Upper','5 Mile Lower'),
        array('Munro Ridge Upper','Munro Ridge Lower'),
        array('Sun Downer Upper','Sun Downer Lower'),
        array('Sundance Upper','Sundance Lower'),
        array('Exhibition Upper','Exhibition Lower'),
        array('Chute','Chute Lower')
    );


    /**
     * LiftsTrailsController constructor.
     * @param Connection $connection
     * @param FtpConnection $ftpConnection
     * @param EfqQueryEntities $EfqQueryEntities
     */
    public function __construct(Connection $connection, FtpConnection $ftpConnection, EfqQueryEntities $EfqQueryEntities) {
		$this->connection  = $connection;
		$this->ftpConnection = $ftpConnection;
        $this->efqQueryEntities = $EfqQueryEntities;
    }



    /**
     * When this controller is created, it will get services
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @return static
     */
    public static function create(ContainerInterface $container) {
        return new static(
		  $container->get('database'),
          $container->get('ftp_connect.ftp_connection'),
          $container->get('efq.query_entities')
        );
    }



    /**
     * Insert data to database
     * @param $id
     * @param $machine_name
     * @param $name
     * @param $status
     * @param $difficulty
     * @param $lift
     * @return bool
     */
	private function insertData ( $id, $machine_name, $name, $status, $difficulty, $lift, $type ) {

		$query = $this->connection->merge('lifts_trails');
		$query->key(array(
			'id' => $id
		));
		$query->fields(array(
			'id' => $id,
            'machine_name' => $machine_name,
			'name' => $name,
			'status' => $status,
			'difficulty' => $difficulty,
			'lift' =>  $lift,
            'type' =>  $type
		));

		if ( $query->execute() ){
			return true;
		}

		return false;

	}



    /**
     * Get lift or trail by machine name
     * @param $machine_name
     * @return mixed
     */
	public function getByName($machine_name){

        $query = $this->connection->select('lifts_trails', 'lt');
        $query->addField('lt', 'status');
        $query->condition('lt.machine_name', $machine_name);

        $result = $query->execute()->fetchField();

        if($result == ''){
            return 'N/A';
        }else{
            return $result;
        }

    }



    /**
     * Get open lifts from feed
     * @return mixed
     */
    public function getOpenLiftsFromFeed(){

        $query = $this->connection->select('lifts_trails', 'lt');
        $query->addField('lt', 'status');
        $query->condition('lt.type', 'lift');
        $query->condition('lt.status', 0);

        $result = $query->countQuery()->execute()->fetchField();

        return $result;

    }



    /**
     * Get open trails from feed
     * @return mixed
     */
    public function getOpenTrailsFromFeed(){

        $query = $this->connection->select('lifts_trails', 'lt');
        $query->addField('lt', 'status');
        $query->condition('lt.type', 'trail');
        $query->condition('lt.status', [0,2,3], 'IN');
        $query->condition('lt.name', $this->ignoreFromFeed, 'NOT IN');

        $result = $query->countQuery()->execute()->fetchField();

        $upperLowerOpen = $this->getUpperLowerOpenFromFeed();

        return $result - $upperLowerOpen;

    }



    /**
     * Get number of combined upper/lower open trails from Feed
     * @return int
     */
    private function getUpperLowerOpenFromFeed(){

        $total = 0;

        // Loop and query for both upper/lower trails open
        foreach($this->upperLowerCombinations as $combo){

            $query = $this->connection->select('lifts_trails', 'lt');
            $query->addField('lt', 'status');
            $query->condition('lt.type', 'trail');
            $query->condition('lt.status', [0,2,3], 'IN');
            $query->condition('lt.name', $combo, 'IN');

            $count = $query->countQuery()->execute()->fetchField();

            // If both are open
            if($count > 1){
                $total ++;
            }

        }

        return $total;

    }



    /**
     * Get number of upper trails from CMS
     * @return array|string
     */
    private function getIgnoreTrails(){

        $range = array(
            "start" => 0,
            "length" => 50
        );

        // Get only published node with defined params
        $conditions = array(
            "status" => 1,
            "title" => array( $this->ignoreNames, 'IN'),
            'field_sport.entity.tid' => 15
        );

        // Get number of upper lifts
        $upperTrails = $this->efqQueryEntities->getEntities('trail', "row", $conditions, $range, NULL, true);

        if($upperTrails){
            return $upperTrails;
        }

        return '0';

    }



    /**
     * Get number of combined upper/lower open trails from CMS
     * @return int
     */
    private function getUpperLowerOpenFromCMS(){

        $range = array(
            "start" => 0,
            "length" => 2
        );

        $total = 0;

        // Loop and query for both upper/lower trails open
        foreach($this->upperLowerCombinations as $combo){

            $conditions = array(
                "status" => 1,
                "title" => array( $combo, 'IN'),
                "field_status" => array( array(126,128,129), 'IN'),
                'field_sport.entity.tid' => 15
            );

            $count = $this->efqQueryEntities->getEntities('trail', "row", $conditions, $range, NULL, true);

            // If both are open
            if($count > 1){
                $total ++;
            }

        }

        return $total;


    }



    /**
     * Get total or open trails from CMS
     * @param string $type
     * @param int $open
     * @param string $season
     * @return array|int
     */
    public function getLiftsTrailsFromCMS ($type, $open = 0, $season = 'winter'){

        switch ($open){
            case 0: $status = array( array(126,127,128,129), 'IN'); break;
            case 1: $status = array( array(126,128,129), 'IN'); break;
        }

        // Set season based terms
        switch ($season){
            case "winter":
                $seasonTid = 8; // Winter tid
                $sportTid = 15; // Ski sport tid
                break;
            case "summer":
                $seasonTid = 7; // Summer tid
                $sportTid = 17 ; // Bike sport tid
                break;
        }

        // Get only published node with defined params
        $conditions = array(
            "status" => 1,
            "field_status" => $status
        );

        switch($type){
            case 'lift':
                $conditions['field_category.entity.tid'] = $seasonTid;
                break;
            case 'trail':
                $conditions['field_sport.entity.tid'] = $sportTid;
                break;
        }

        // Get all nodes
        $range = array(
            "start" => 0,
            "length" => 1000
        );

        // We use the injected service to get the message.
        $entitiesList = $this->efqQueryEntities->getEntities($type, "row", $conditions, $range, NULL, true);

        if ($entitiesList){

            // If getting total trails
            if($type == 'trail' && $open == 0){
                $upperTrails = $this->getIgnoreTrails();
                $entitiesList = $entitiesList - $upperTrails;

            }
            // If getting open trails
            if($type == 'trail' && $open == 1){
                $upperLowerOpen = $this->getUpperLowerOpenFromCMS();
                $entitiesList = $entitiesList - $upperLowerOpen;
            }
            return $entitiesList;
        }

        // If nothing, return 0
        return '0';

    }



	/**
	 * Get and store json data
	 */
    public function getJson() {

        // Invalidate cache so the cron will always be run
        \Drupal::service('page_cache_kill_switch')->trigger();

		// Settings for connection and files to grab
		$settings = array(
		  'hostname' => '50.21.233.236',
		  'username' => 'WWW_spr',
		  'password' => 'snow1234!',
		  'port' => 990,
		  'secured' => true
		);

		// Setup connection
		$this->ftpConnection->setSettings($settings);

		// Get local / Destination path
		$destination_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");

		$response = '';

		// Set file paths
        $source_file = '/grooming/run_output.json';
        $destination_file = $destination_path.DIRECTORY_SEPARATOR.'lifts_trails'.DIRECTORY_SEPARATOR.'lifts_trails.json';

        // Connect to FTP and Get File
        $storeFile =  $this->ftpConnection->getFile( $source_file, $destination_file );

        if ( $storeFile['success'] ){

            $response .= "<p>Connection successful (No ".$storeFile['error_no'].": ".$storeFile['message'].")<br />";

            // Get data from file
            $data = file_get_contents($destination_file);
            $json = json_decode($data, true);

            // Loop over data and insert into table
            foreach($json as $key => $array){

                // Setup machine name
                switch($array['lift']){
                    case 'lifts':
                        $machine_name = 'lift_';
                        $type = 'lift';
                        break;
                        
                    case 'nordic':
                    case 'nordic ':
                        $machine_name = 'trail_';
                        $type = 'nordic';
                        break;

                    default:
                        $machine_name = 'trail_';
                        $type = 'trail';
                        break;
                }

                $machine_name .= $this->createMachineName($array['name']);

                // Insert data and print output
                $this->insertData($key+1,$machine_name,$array['name'],$array['status'],$array['difficulty'],$array['lift'],$type);
                $response .= 'Added row key:'.($key+1).', machine_name:'.$machine_name.', name:'.$array['name'].', status:'.$array['status'].', difficulty:'.$array['difficulty'].', lift:'.$array['lift'].', type:'.$type.'</br>';
            }

        }else{

            $response .= "<p>Connection failed (No ".$storeFile['error_no'].": ".$storeFile['message'].")<br />";
            $response .=  "Error trying to connect to SPR Grooming FTP service. Error No ".$storeFile['error_no'].": ".$storeFile['message'];
        }


		$build[] = array(
		  '#type' => 'markup',
		  '#markup' => t($response),
		);
		$build['#cache']['max-age'] = 0;

		return $build;


    }


    /**
     * Create machine name from title
     * @param $name
     * @return string
     */
    public function createMachineName($name){

        // remove (x.xkm) from nordic trails
        $machine_name = preg_replace("/ \([^)]+km\)/", '', $name);
        // remove punctuation
        $search = [' ','\'','.'];
        $replace = ['_','',''];
        $machine_name = strtolower(str_replace($search,$replace,$machine_name));

        return $machine_name;

    }



}
