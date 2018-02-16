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
class VideoController extends ContentController {



    public function getIncludes () {

      $this->result["listing_isotope"] = $this->getVideos();
      $this->result["filter_isotope"] = $this->getCategories();
      $this->result["featured_video"] = $this->getFeatured();

        return $this->result;

    }


    protected function getVideos (){

        $conditions = array(
            "status" => 1,
        );

      $sort = array(
        [
          "field" => 'field_weight',
          "direction" => 'DESC',
        ],
        [
          "field" => 'created',
          "direction" => 'DESC',
        ]
      );

        $range = array(
            "start" => 0,
            "length" => 200
        );


        $videos = $this->efqService->getEntities('video', 'grid', $conditions, $range, $sort);

        if ($videos){
            return $videos;
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
        $terms = $this->taxoHelper->getTaxoTerms('video_gallery');

        // Convert terms to filter array
        $filters = $this->getTaxoTermsAsFilters($terms,'Select a Category');

        // Push filters to form
        $form["container"]['category']['#options'] = $filters;

        return $form;

    }


    protected function getFeatured(){

        $conditions = array(
            "status" => 1,
            "field_featured" => 1
        );

        $sort = array(
            [
                "field" => 'created',
                "direction" => 'DESC',
            ]
        );

        $range = array(
            "start" => 0,
            "length" => 1
        );


        $videos = $this->efqService->getEntities('video', 'full', $conditions, $range, $sort);

        if ($videos){
            return $videos;
        }

        return false;

    }




}
