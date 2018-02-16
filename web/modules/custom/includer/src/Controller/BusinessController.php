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
class BusinessController extends ContentController {



    public function getIncludes () {

        if(isset($this->arguments["type"])) {
            // If type specific
            $this->result["listing_simple"] = $this->getBusiness($this->arguments["type"]);
        }else {
            // Else full business listing
            $this->result["listing_isotope"] = $this->getBusiness();
            $this->result["filter_isotope"] = $this->getCategories();
        }

        return $this->result;

    }



    protected function getBusiness ($type = false){

        $conditions = array(
            "status" => 1,
            "field_ignore" => array( 1, '!=')
        );

        $sort = array(
            "field" => 'title',
            "direction" => "ASC"
        );

        $range = array(
            "start" => 0,
            "length" => 150
        );

        if($type){
            $business = $this->efqService->getEntities($type, 'teaser', $conditions, $range, $sort);
        }else{
            $business = $this->efqService->getEntities(['business','accomodation','dining_apres','shopping','spa'], 'teaser', $conditions, $range, $sort);
        }

        if ($business){
            return $business;
        }

        return false;

    }



    protected function getCategories (){

        $form = \Drupal::formBuilder()->getForm('Drupal\efq\Form\FilterForm');

        $form["container"] =  array(
            '#prefix' => '<div class="row filter-count-1">',
            '#suffix' => '</div>',
            'category' => array(
                '#attributes' => [
                    'id' => 'select',
                    'class' => ['dropdown'],
                    'data-filter-group' => "category"
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
        $terms = $this->taxoHelper->getTaxoTerms('business_type');

        // Convert terms to filter array
        $filters = $this->getTaxoTermsAsFilters($terms,'Select a Category');

        // Push filters to form
        $form["container"]['category']['#options'] = $filters;


        return $form;

    }



}
