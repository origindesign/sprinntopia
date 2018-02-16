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
class UpdateController extends ContentController {



    public function getIncludes () {

      $this->result["listing_simple"] = $this->getUpdates($this->arguments["tid"]);

      switch ($this->arguments["tid"]) {
        case '5':
          $title = 'Golf';
          break;
        case '6':
          $title = 'Bike Park';
          break;
      }

      if (isset($title)) {
        $this->result["listing_simple"]["#prefix"] = $this->buildHeader($title . " Updates");
      }

        return $this->result;

    }



    protected function getUpdates ($tid){

        $conditions = array(
            "status" => 1
        );

        if($tid) {
            $conditions['field_category.entity.tid'] = $tid;
        }

        $range = array(
            "start" => 0,
            "length" => 100
        );

        $sort = array(
            "field" => 'created',
            "direction" => 'DESC'
        );


        $updates = $this->efqService->getEntities('update', 'teaser', $conditions, $range, $sort);

        if ($updates){
            return $updates;
        }

        return false;


    }


}
