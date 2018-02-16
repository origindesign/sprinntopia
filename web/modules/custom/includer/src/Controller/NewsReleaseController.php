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
class NewsReleaseController extends ContentController {



    public function getIncludes () {

      $this->result["listing_simple"] = $this->getNews();

      return $this->result;

    }



    protected function getNews (){

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


        $news = $this->efqService->getEntities('new_release', 'teaser', $conditions, $range, $sort);

        if ($news){
            return $news;
        }

        return false;


    }


}
