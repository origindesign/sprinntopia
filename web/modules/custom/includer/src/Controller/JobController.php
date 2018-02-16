<?php

/**
 * @file
 * Contains \Drupal\includer\Controller\HomepageController
 */

namespace Drupal\includer\Controller;


/**
 * The specific class to gather includes and pass to template
 *
 */
class JobController extends ContentController {



    public function getIncludes () {

        $this->result["listing_simple"] = $this->getJobs();

        return $this->result;

    }



    protected function getJobs (){

        $conditions = array(
            "status" => 1
        );

        $range = array(
            "start" => 0,
            "length" => 100
        );

        $sort = array(
            "field" => 'created',
            "direction" => 'DESC'
        );


        $jobs = $this->efqService->getEntities('job', 'teaser', $conditions, $range, $sort);

        if ($jobs){
            return $jobs;
        }

        return false;


    }


}
