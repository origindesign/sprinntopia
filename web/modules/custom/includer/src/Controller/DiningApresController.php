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
class DiningApresController extends ContentController {



    public function getIncludes () {

        $this->result["listing_isotope"] = $this->getDiningApres();
        $this->result["filter_isotope"] = $this->getCategories();

        return $this->result;

    }





    protected function getDiningApres (){

        $conditions = array(
            "status" => 1,
        );

        $sort = array(
            "field" => 'title',
            "direction" => "ASC"
        );

        $range = array(
            "start" => 0,
            "length" => 150
        );


        $diningApres = $this->efqService->getEntities('dining_apres', 'teaser', $conditions, $range, $sort);

        if ($diningApres){
            return $diningApres;
        }

        return false;

    }




    protected function getCategories (){

        $form = \Drupal::formBuilder()->getForm('Drupal\efq\Form\FilterForm');

        $form["container"] =  array(
            '#prefix' => '<div class="row filter-count-1">',
            '#suffix' => '</div>',
            'categories' => array(
                '#attributes' => [
                    'id' => 'select',
                    'class' => ['dropdown'],
                    'data-filter-group' => "categories"
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
        $terms = $this->taxoHelper->getTaxoTerms('dining_apres_type');

        // Convert terms to filter array
        $filters = $this->getTaxoTermsAsFilters($terms,'Select a Category');

        // Push filters to form
        $form["container"]['categories']['#options'] = $filters;

        return $form;

    }






}
