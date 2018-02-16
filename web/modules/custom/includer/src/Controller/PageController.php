<?php

/**
 * @file 
 * Contains \Drupal\includer\Controller\PageController
 */
 
namespace Drupal\includer\Controller;


/**
 * The specific class to gather includes and pass to template
 *
 */
class PageController extends ContentController {

   
    
    public function getIncludes () {

        if(isset($this->arguments["viewmode"]) ? $viewmode = $this->arguments["viewmode"] : $viewmode = 'teaser');
        if(isset($this->arguments["tid"]) ? $tid = $this->arguments["tid"] : $tid = null);

        $this->result["listing_simple"] = $this->getNodes($this->arguments["type"], $tid, $viewmode);

        return $this->result; 
        
    }





    protected function getNodes ($type, $tid, $viewmode){

        $conditions = array(
            "status" => 1,
        );

        if($tid) {
            $conditions['field_category.entity.tid'] = $tid;
        }

        $sort = array(
            "field" => 'title',
            "direction" => "ASC"
        );

        $range = array(
            "start" => 0,
            "length" => 100
        );


        $nodes = $this->efqService->getEntities($type, $viewmode, $conditions, $range, $sort);

        if ($nodes){
            return $nodes;
        }

        return false;

    }
    
    
    

}
