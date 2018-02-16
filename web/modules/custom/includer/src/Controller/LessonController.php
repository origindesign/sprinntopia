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
class LessonController extends ContentController {



    public function getIncludes () {

        $sport = $this->arguments["sport"];

        switch($sport){
            case "winter": $type = 'lesson'; break;
            case "summer": $type = 'summer_lesson'; break;
        }

      $this->result["listing_isotope"] = $this->getLessons($type);
      $this->result["filter_isotope"] = $this->getCategories($sport);

        return $this->result;

    }





    protected function getLessons ($type){

        $conditions = array(
            "status" => 1
        );

        $sort = array(
            "field" => 'title',
            "direction" => "ASC"
        );

        $range = array(
            "start" => 0,
            "length" => 150
        );


        $lessons = $this->efqService->getEntities($type, 'teaser', $conditions, $range, $sort);

        if ($lessons){
            return $lessons;
        }

        return false;

    }




    protected function getCategories ($sport){

        $form = \Drupal::formBuilder()->getForm('Drupal\efq\Form\FilterForm');


        $form["container"] =  array(
            '#prefix' => '<div class="row filter-count-4">',
            '#suffix' => '</div>',
            'type' => array(
              '#attributes' => [
                'id' => 'select',
                'class' => ['dropdown'],
                'data-filter-group' => "type"
              ],
              '#type' => 'select',
              '#default_value' => ''
            ),
            'sport' => array(
                '#attributes' => [
                    'id' => 'select',
                    'class' => ['dropdown'],
                    'data-filter-group' => "sport"
                ],
                '#type' => 'select',
                '#default_value' => ''
            ),
            'age' => array(
                '#attributes' => [
                    'id' => 'select',
                    'class' => ['dropdown'],
                    'data-filter-group' => "age"
                ],
                '#type' => 'select',
                '#default_value' => ''
            ),
            'level' => array(
                '#attributes' => [
                    'id' => 'select',
                    'class' => ['dropdown'],
                    'data-filter-group' => "level"
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
        $type = $this->taxoHelper->getTaxoTerms('lesson_type');
        $level = $this->taxoHelper->getTaxoTerms('lesson_level');
        switch($sport){
          case 'winter':
            $age = $this->taxoHelper->getTaxoTerms('age');
            $sportTax = $this->taxoHelper->getTaxoTerms('lesson_sport',[105,106]);
            break;
          case 'summer':
              unset($form["container"]["age"]);
              $sportTax = $this->taxoHelper->getTaxoTerms('lesson_sport',[103,104]);
            break;
        }

        // Convert terms to filter array
        $typeFilters = $this->getTaxoTermsAsFilters($type,'Select a Category');
        $sportFilters = $this->getTaxoTermsAsFilters($sportTax,'Select a Sport');
        $levelFilters = $this->getTaxoTermsAsFilters($level,'Select an Ability Level');
        if($sport == 'winter'){
            $ageFilters = $this->getTaxoTermsAsFilters($age,'Select an Age Group');
        }


        // Push filters to form
        $form["container"]['type']['#options'] = $typeFilters;
        $form["container"]['sport']['#options'] = $sportFilters;
        if($sport == 'winter'){
            $form["container"]['age']['#options'] = $ageFilters;
        }
        $form["container"]['level']['#options'] = $levelFilters;


        return $form;

    }






}
