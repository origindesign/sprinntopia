<?php
/**
 * @file
 * Contains \Drupal\meteo\Controller\MeteoController.
 */
 
namespace Drupal\meteo\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Controller\ControllerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\meteo\MeteoQueries;


class MeteoController extends ControllerBase implements ContainerInjectionInterface {


    protected $meteoQueries;

    /**
     * @param MeteoQueries $meteoQueries
     * @internal param \Drupal\meteo\MeteoQueries $meteoQueries
     */
    public function __construct(MeteoQueries $meteoQueries) {
        $this->meteoQueries = $meteoQueries;
    }


    /**
     * When this controller is created, it will get the meteo.query_meteo service and store it.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @return static
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container
                ->get('meteo.query_meteo')
        );
    }


    public function getMeteoData() {

        /*
         * Invalidate cache so the cron will always be run
         */
        \Drupal::service('page_cache_kill_switch')->trigger();


        /*
         * These are data to change according to the provider we want to use
         * Idea for improvement : Store this in DB when setting up a new meteo location. (table: meteo_location)
         *
         */
        $settings = array(
            'service' => 'customweatherftp',
            'hostname' => '50.21.233.236',
            'username' => 'WWW_spr',
            'password' => 'snow1234!',
            'port' => 990,
            'secured' => true,
            'files' => array(
                array(
                    'location_id' => 1,
                    'source_file' => '/weather/weather_output.json',
                    'destination_file' => DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR.'weather_output.json',
                )
            )
        );


        /*
         * Create Location if doesn't exists
         * Idea for improvement : Create Location in Drupal Back end along with all credentials above
         */
        $service = $settings['service'];
        $this->meteoQueries->createLocation(1,'sunpeak','SprWeather FTP for Mountain Creek', $service);



        /*
         * Instantiate CustomWeatherFTP provider
         * Idea for improvement : Get provider and credentials out of DB based on Meteo location ID (table: meteo_location)
         *
         */
        $manager = \Drupal::service('plugin.manager.meteo');
        $plugins = $manager->getDefinitions();
        $instance = $manager->createInstance($plugins[$service]['id']);



        $build[] = array(
            '#type' => 'markup',
            '#markup' => t('<p>Provider @name.</p>', array( '@name' => $instance->getName() ) ),
        );

        $storeItRaw = true; // Store the value in the Raw Table (not organized, straight as per file provided)
        $instance->storeWeatherData( $settings, $storeItRaw );


        /*
         * Invalidate cache so the cron will always be run
         */
        $build['#cache']['max-age'] = 0;

        return $build;


    }


  
  
}
