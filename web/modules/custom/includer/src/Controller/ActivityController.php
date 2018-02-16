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
class ActivityController extends ContentController {



    public function getIncludes () {

        $this->result["listing_isotope"] = $this->getActivities();
        $this->result["filter_isotope"] = $this->getCategories($this->arguments["tid"]);

        return $this->result;

    }





    protected function getActivities (){

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


        $activities = $this->efqService->getEntities('activity', 'grid', $conditions, $range, $sort);

        if ($activities){
            return $activities;
        }

        return false;

    }




    protected function getCategories ($tid){

        switch($tid){
            case 8: $default = '.cat-winter'; break;
            case 7: $default = '.cat-summer'; break;
            default: $default = ''; break;
        }

        $form = \Drupal::formBuilder()->getForm('Drupal\efq\Form\FilterForm');

        $form["container"] =  array(
            '#prefix' => '<div class="row filter-count-2">',
            '#suffix' => '</div>',
            'season' => array(
                '#attributes' => [
                    'id' => 'select',
                    'class' => ['dropdown'],
                    'data-filter-group' => "season"
                ],
                '#type' => 'select',
                '#value' => $default,
                '#options' => array(
                    '' => t('Select a Season'),
                    '.cat-winter' => t('Winter'),
                    '.cat-summer' => t('Summer'),
                ),
            ),
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
        $terms = $this->taxoHelper->getTaxoTerms('activity_type');

        // Convert terms to filter array
        $filters = $this->getTaxoTermsAsFilters($terms,'Select a Category');

        // Push filters to form
        $form["container"]['categories']['#options'] = $filters;

        return $form;

    }






}
