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
class EditorialController extends ContentController {



    public function getIncludes () {

      $this->result["listing_simple"] = $this->getEditorials();

      return $this->result;

    }



    protected function getEditorials(){

        $conditions = array(
            "status" => 1
        );

        $range = array(
            "start" => 0,
            "length" => 1000
        );

        $sort = array(
            "field" => 'created',
            "direction" => 'DESC'
        );


        $editorials = $this->efqService->getEntities('editorial', 'teaser', $conditions, $range, $sort);

        if ($editorials){
            return $editorials;
        }

        return false;


    }


}
