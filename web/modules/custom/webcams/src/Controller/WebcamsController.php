<?php
/**
 * @file
 * Contains \Drupal\webcams\Controller\WebcamsController.
 */
 
namespace Drupal\webcams\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\ftp_connect\FtpConnection;


class WebcamsController extends ControllerBase {


	protected $connection;

    protected $ftpConnection;



    /**
     * @param Connection $connection
	 * @param FtpConnection $ftpConnection
	 * @internal param \Drupal\Core\Database\Connection $connection
     * @internal param \Drupal\ftp_connect\FtpConnection $ftpConnection
     */
    public function __construct(Connection $connection, FtpConnection $ftpConnection) {
		$this->connection  = $connection;
		$this->ftpConnection = $ftpConnection;
    }


    /**
     * When this controller is created, it will get the ftp_connect.connection service and store it.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @return static
     */
    public static function create(ContainerInterface $container) {
        return new static(
		  $container->get('database'),
          $container->get('ftp_connect.ftp_connection')
        );
    }



	/**
	 * Insert Webcam to database
	 * @param int $wid
	 * @param $name
	 * @param $timestamp
	 * @return int
	 * @internal param array $data
	 */
	private function createWebcam ( $wid, $name, $machine_name, $location, $elevation, $filename, $timestamp ) {

		$query = $this->connection->merge('webcams_data');
		$query->key(array(
			'wid' => $wid
		));
		$query->fields(array(
			'wid' => $wid,
			'name' => $name,
			'machine_name' => $machine_name,
			'location' => $location,
			'elevation' => $elevation,
			'filename' =>  $filename,
			'timestamp' => $timestamp
		));

		if ( $query->execute() ){
			return true;
		}

		return false;

	}



	/*
     * Get all webcams data
     */
	public function getWebcams(){

		$query = $this->connection->select('webcams_data','web');
		$query->fields('web');
		$result = $query->execute()->fetchAll();

		$data = [];

		foreach($result as $record) {
			$data[] = array(
			  'name' => $record->name,
			  'machine_name' => $record->machine_name,
			  'location' => $record->location,
			  'elevation' => $record->elevation,
			  'filename' => $record->filename,
			  'timestamp' => $record->timestamp,
			);
		}

		return $data;

	}



	/**
	 * Get and store webcam images from ftp to local file system
	 */
    public function getWebcamImages() {

        /*
         * Invalidate cache so the cron will always be run
         */
        \Drupal::service('page_cache_kill_switch')->trigger();


		/*
         * Settings for connection and files to grab
         */
		$settings = array(
		  'hostname' => '50.21.233.236',
		  'username' => 'WWW_spr',
		  'password' => 'snow1234!',
		  'port' => 990,
		  'secured' => true,
		  'files' => array(
			array(
			  'source_file' => '/webcams/Valley.jpg',
			  'destination_file' => DIRECTORY_SEPARATOR.'webcams'.DIRECTORY_SEPARATOR.'Valley.jpg',
			  'name' => 'Valley',
			  'machine_name' => 'valley',
			  'location' => 'Village Square',
			  'elevation' => 'Village Base, 1,255m (4,116\')',
			  'filename' => 'Valley.jpg',
			),
			array(
			  'source_file' => '/webcams/Village Day Lodge Slopeside.jpg',
			  'destination_file' => DIRECTORY_SEPARATOR.'webcams'.DIRECTORY_SEPARATOR.'Village Day Lodge Slopeside.jpg',
			  'name' => 'Village Day Lodge Slopeside',
			  'machine_name' => 'village_day_lodge',
			  'location' => 'Village Day Lodge',
			  'elevation' => 'Village Base, 1,255m (4,116\')',
			  'filename' => 'Village Day Lodge Slopeside.jpg'
			),
			array(
			  'source_file' => '/webcams/Sunburst Lift Base.jpg',
			  'destination_file' => DIRECTORY_SEPARATOR.'webcams'.DIRECTORY_SEPARATOR.'Sunburst Lift Base.jpg',
			  'name' => 'Sunburst Lift Base',
			  'machine_name' => 'sunburst_lift_base',
			  'location' => 'The Annex',
			  'elevation' => 'Village Base, 1,255m (4,116\')',
			  'filename' => 'Sunburst Lift Base.jpg'
			),
			array(
			  'source_file' => '/webcams/Elevation Chair.jpg',
			  'destination_file' => DIRECTORY_SEPARATOR.'webcams'.DIRECTORY_SEPARATOR.'Elevation Chair.jpg',
			  'name' => 'Elevation Chair',
			  'machine_name' => 'elevation_chair',
			  'location' => 'Base of the Elevation Chairlift',
			  'elevation' => '1,600m (5,249\')',
			  'filename' => 'Elevation Chair.jpg'
			),
			array(
			  'source_file' => '/webcams/Sundance Lift Base.jpg',
			  'destination_file' => DIRECTORY_SEPARATOR.'webcams'.DIRECTORY_SEPARATOR.'Sundance Lift Base.jpg',
			  'name' => 'Sundance Lift Base',
			  'machine_name' => 'sundance_lift_base',
			  'location' => 'Village Square',
			  'elevation' => 'Village Base, 1,255m (4,116\')',
			  'filename' => 'Sundance Lift Base.jpg'
			),
			array(
			  'source_file' => '/webcams/sundance.jpg',
			  'destination_file' => DIRECTORY_SEPARATOR.'webcams'.DIRECTORY_SEPARATOR.'sundance.jpg',
			  'name' => 'Sundance',
			  'machine_name' => 'sundance',
			  'location' => 'Top of Sundance Express Chairlift',
			  'elevation' => '1,731m (5,677\')',
			  'filename' => 'sundance.jpg'
			),
			array(
			  'source_file' => '/webcams/view of mt todd.jpg',
			  'destination_file' => DIRECTORY_SEPARATOR.'webcams'.DIRECTORY_SEPARATOR.'view of mt todd.jpg',
			  'name' => 'Mt. Tod, View from Mt. Morrisey',
			  'machine_name' => 'mt_tod',
			  'location' => 'Top of the Morrisey Express',
			  'elevation' => '1,672m (5,495\')',
			  'filename' => 'view of mt todd.jpg'
			),
			array(
			  'source_file' => '/webcams/Village Clock Tower.jpg',
			  'destination_file' => DIRECTORY_SEPARATOR.'webcams'.DIRECTORY_SEPARATOR.'Village Clock Tower.jpg',
			  'name' => 'Village Clock Tower',
			  'machine_name' => 'village_clock_tower',
			  'location' => 'Village Square',
			  'elevation' => 'Village Base, 1,255m (4,116\')',
			  'filename' => 'Village Clock Tower.jpg'
			),
			array(
			  'source_file' => '/webcams/Golf Course.jpg',
			  'destination_file' => DIRECTORY_SEPARATOR.'webcams'.DIRECTORY_SEPARATOR.'Golf Course.jpg',
			  'name' => 'Golf Course',
			  'machine_name' => 'golf_course',
			  'location' => 'Overlooking hole 17 on the left and hole 10 on the right',
			  'elevation' => 'Village Base, 1,255m (4,116\')',
			  'filename' => 'Golf Course.jpg'
			),
		  )
		);



		// Setup connection
		$this->ftpConnection->setSettings($settings);


		// Get local / Destination path
		$destination_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");

		$response = '';

		// Loop over all different files to store them locally
		foreach ( $settings['files'] as $key => $file ){

			$destination_file = $destination_path.$file['destination_file'];
			$source_file = $file['source_file'];

			// Connect to FTP and Get File
			$storeFile =  $this->ftpConnection->getFile( $source_file, $destination_file );

			if ( $storeFile['success'] ){

                // Get time of source file
                $timestamp = $this->ftpConnection->getFileTime($source_file);

                // Insert into database table
                $this->createWebcam($key+1, $file['name'], $file['machine_name'], $file['location'], $file['elevation'], $file['filename'], $timestamp);

				$response .= "<p>Connection successful (No ".$storeFile['error_no'].": ".$storeFile['message']."): ".$file['filename']."<br />";

			}else{
                $response .= "<p>Error trying to connect to SPR Webcam FTP service (No ".$storeFile['error_no'].": ".$storeFile['message']."): ".$file['filename']."<br />";
			}

		}


		$build[] = array(
		  '#type' => 'markup',
		  '#markup' => t($response),
		);
		$build['#cache']['max-age'] = 0;

		return $build;


    }




}
