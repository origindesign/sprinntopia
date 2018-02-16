<?php
 
/**
 * @file
 * Contains \Drupal\efq\Controller\EfqController.
 */
 
namespace Drupal\efq\Controller;
 
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\efq\EfqQueryEntities;
 


class EfqController extends ControllerBase {
 
    /**
     * @var \Drupal\efq\EfqQueryEntities
     */
    protected $efqQueryEntities;
    
    
 
    /**
     * @param \Drupal\efq\EfqQueryEntities $EfqQueryEntities
     */
    public function __construct(EfqQueryEntities $EfqQueryEntities) {
        $this->efqQueryEntities = $EfqQueryEntities;
    }
 

    /**
     * When this controller is created, it will get the efq.queryEntities service and store it.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @return static
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('efq.query_entities')
        );
    }




    /**
     * Get Nodes based on content type and params
     *
     * @param string $content_type
     * @param string $view_mode
     * @param null $param
     * @param null $date
     * @return array
     */
    public function getNodes($content_type = 'article', $view_mode = 'teaser', $param = null, $date = null ) {


        // Only get Published Nodes
        $conditions = array(
            "status" => 1
        );

        // Default Sort
        $sort = NULL;

		// Default Range
		$range = array(
		  "start" => 0,
		  "length" => 1000
		);


        // If there is a date in argument, then add conditions to get if a date is included in a given month (used for calendar)
        if ( $date ){

            $dateArray = explode('--',$date);

            $startDate = $this->formatDateFilter($dateArray[0], 'start');
            $endDate = $this->formatDateFilter($dateArray[1], 'end');

            $conditions['group'] =array(
                "andor" => "OR",
                'grouping' => array(
                    array(
                        "andor" => "AND",
                        "grouping" => array(
                            "field_date.0.value" => array( $startDate, '<='),
                            "field_date.1.value" => array( $startDate, '>')
                        )
                    ),
                    array(
                        "andor" => "AND",
                        "grouping" => array(
                            "field_date.0.value" => array( $endDate, '<'),
                            "field_date.1.value" => array( $endDate, '>=')
                        )
                    ),
                    "field_date.0.value" => array( array($startDate, $endDate), 'BETWEEN'),
                    "field_date.1.value" => array( array($startDate, $endDate), 'BETWEEN')
                )

            );


            $sort = array(
                "field" => 'field_date',
                "direction" => 'ASC'
            );

        }


        // Use the injected service to get the node list.
        $nodesList = $this->efqQueryEntities->getEntities( $content_type, $view_mode, $conditions, $range, $sort );


        // Return a render of all nodes
        if ($nodesList){
            return $nodesList;
        }


        // If nothing is returned, then return a no result message
        return [
            '#type' => 'markup',
            '#markup' => '<p class="no-results">Sorry, there are no results for your current selection.</p>',
        ];


    }





    /**
     * Get start or end of day
     *
     * @param $date
     * @param string $delta (either start or end)
     * @return DrupalDateTime
     */
    protected function formatDateFilter ($date, $delta = 'start'){

        // Transform $date string into a date object, first hour first minute of day
        $formatDate = new DrupalDateTime($date);

        if ( $delta == 'end' ){
			// Transform $date string into a date object, last hour last minute of day
			$formatDate->setTime(23,59);
        }

        // Set the time zone as per stored in DB (GMT usually)
		$formatDate->setTimezone(new \DateTimezone(DATETIME_STORAGE_TIMEZONE));

        // Return the DrupalDatetime object
        return $formatDate->format(DATETIME_DATETIME_STORAGE_FORMAT);

    }

  
  
}
