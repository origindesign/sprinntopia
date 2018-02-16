<?php

/**
 * @file 
 * Contains \Drupal\includer\Controller\ActivityController
 */
 
namespace Drupal\includer\Controller;


/**
 * The specific class to gather includes and pass to template
 *
 */
class AccommodationController extends ContentController {



    public function getIncludes () {

        $this->result["listing_simple"] = $this->getAccommodations($this->arguments["tid"]);

        return $this->result;

    }





    protected function getAccommodations ($tid){

        $conditions = array(
            "status" => 1,
            "field_category.entity.tid" => $tid
        );

        $sort = array(
            "field" => 'title',
            "direction" => "ASC"
        );

        $range = array(
            "start" => 0,
            "length" => 100
        );


        $accommodations = $this->efqService->getEntities('accomodation', 'teaser', $conditions, $range, $sort);

        if ($accommodations){
            return $accommodations;
        }

        return false;

    }




}
