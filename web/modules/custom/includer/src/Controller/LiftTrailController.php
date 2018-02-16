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
class LiftTrailController extends ContentController {


    public function getIncludes () {


        // If season set, show lifts
        if(isset($this->arguments["season"])) {

            $this->result["listing_lifts"] = $this->getLifts($this->arguments["season"]);
            $this->result["listing_lifts"]["#prefix"] = $this->buildHeader('Lift Status') . $this->buildTableHeader("Lift Name");

        }

        if($this->arguments["sport"] == 15) { // Ski Trails

            $this->result["trails_key"] = true;
            $this->result["listing_isotope"] = $this->getTrails($this->arguments["sport"]);
            $this->result["listing_isotope"]["#prefix"] = $this->buildTableHeader("Trail Name");
            $this->result["filter_isotope"] = $this->getCategories();
            $this->result["filter_isotope"]["#prefix"] = $this->buildHeader('Alpine Trail Status');

        }else{

            // If array of sports
            if(is_array($this->arguments["sport"])){

                $this->result["trails_key"] = true;

                foreach($this->arguments["sport"] as $key => $sport){

                    $this->result["listing_trails"][$key] =  $this->getTrails($sport);

                    switch($sport){

                      case 31:
                        $this->result["listing_trails"][$key]["#prefix"] = $this->buildHeader('Groomed &amp; Trackset Nordic Trails').$this->buildTableHeader("Trail Name");
                        break;

                      case 81:
                        $this->result["listing_trails"][$key]["#prefix"] = $this->buildHeader('Backcountry Nordic Trails').$this->buildTableHeader("Trail Name");
                        break;

                    }

                }

            }else{

                $this->result["listing_trails"] = $this->getTrails($this->arguments["sport"]);
                $this->result["listing_trails"]["#prefix"] = $this->buildTableHeader("Trail Name");

                switch($this->arguments["sport"]){
                  // Hiking
                  case 107:
                    $this->result["listing_trails"]["#prefix"] = $this->buildHeader('Hiking Trails').$this->buildTableHeader("Trail Name");
                    break;
                  // Bike
                  case 17:
                    $this->result["extra_classes"] = 'bike-trails';
                    break;
                }

            }

        }

        return $this->result;

    }



    protected function buildTableHeader ( $title ){

        $header = '
            <div class="node-view-row lift-trail-header">
                <div class="row-cell name">'.$title.'</div>
                <div class="row-cell status">Status</div>
            </div>
        ';

        return $header;

    }



    protected function getLifts ($season){

        $conditions = array(
            "status" => 1,
            "field_category.entity.tid" => $season
        );

        $sort = array(
            "field" => 'title',
            "direction" => "ASC"
        );

        $range = array(
            "start" => 0,
            "length" => 100
        );


        $lifts = $this->efqService->getEntities('lift', 'row', $conditions, $range, $sort);

        if ($lifts){
            return $lifts;
        }

        return false;

    }



    protected function getTrails ($sport){

        $conditions = array(
            "status" => 1,
            "field_sport.entity.tid" => $sport
        );

        // Bike
        if($sport == 17){

            $sort = array(
                [
                    "field" => 'field_ability.entity.name',
                    "direction" => 'ASC',
                ],
                [
                    "field" => 'field_weight',
                    "direction" => 'DESC',
                ]
            );

        // Hiking
        }else if($sport == 107){

            $sort = array(
                [
                    "field" => 'field_ability.entity.name',
                    "direction" => 'ASC',
                ],
                [
                    "field" => 'field_weight',
                    "direction" => 'DESC',
                ]
            );

        } else{

            $sort = array(
                [
                    "field" => 'field_ability.entity.name',
                    "direction" => 'ASC',
                ],
                [
                    "field" => 'title',
                    "direction" => 'ASC',
                ]
            );

        }

        $range = array(
            "start" => 0,
            "length" => 1000
        );


        $trails = $this->efqService->getEntities('trail', 'row', $conditions, $range, $sort);

        if ($trails){
            return $trails;
        }

        return false;

    }



    protected function getCategories (){

        $form = \Drupal::formBuilder()->getForm('Drupal\efq\Form\FilterForm');

        $form["container"] =  array(
            '#prefix' => '<div class="row filter-count-1">',
            '#suffix' => '</div>',
            'zone' => array(
                '#attributes' => [
                    'id' => 'select',
                    'class' => ['dropdown'],
                    'data-filter-group' => "zone"
                ],
                '#type' => 'select',
                '#default_value' => ''
            ),
            'reset' => array(
                '#prefix' => '<div class="clear-filters">',
                '#suffix' => '</div>',
                '#markup' => '<a href="#">Clear filters</a>'
            )

        );

        // Get terms from vocab
        $terms = $this->taxoHelper->getTaxoTerms('mountain_zones');

        // Convert terms to filter array
        $filters = $this->getTaxoTermsAsFilters($terms,'Select a Zone');

        // Push filters to form
        $form["container"]['zone']['#options'] = $filters;

        return $form;

    }




}
