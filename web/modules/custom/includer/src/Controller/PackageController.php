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
class PackageController extends ContentController {



    public function getIncludes () {

        $this->result["listing_simple"] = $this->getPackages($this->arguments["tid"]);

        return $this->result;

    }





    protected function getPackages ($tid){

        $conditions = array(
            "status" => 1,
            "field_season.entity.tid" => $tid
        );

        $sort = array(
            "field" => 'field_weight',
            "direction" => 'DESC'
        );

        $range = array(
            "start" => 0,
            "length" => 100
        );


        $packages = $this->efqService->getEntities('package', 'teaser', $conditions, $range, $sort);

        if ($packages){
            return $packages;
        }

        return false;

    }




}
