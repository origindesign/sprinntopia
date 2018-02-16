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
class BlogController extends ContentController {



    public function getIncludes () {

        $this->result["listing_isotope"] = $this->getBlogs();
        $this->result["filter_isotope"] = $this->getCategories();

        return $this->result;

    }



    protected function getBlogs (){

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


        $blogs = $this->efqService->getEntities('blog', 'teaser', $conditions, $range, $sort);

        if ($blogs){
            return $blogs;
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
        $terms = $this->taxoHelper->getTaxoTerms('season');

        // Convert terms to filter array
        $filters = $this->getTaxoTermsAsFilters($terms,'Select a Category');

        // Push filters to form
        $form["container"]['category']['#options'] = $filters;


        return $form;

    }


}
