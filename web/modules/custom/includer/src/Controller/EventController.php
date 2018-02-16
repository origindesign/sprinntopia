<?php

/**
 * @file 
 * Contains \Drupal\includer\Controller\EventController
 */
 
namespace Drupal\includer\Controller;

use Drupal\Core\Datetime\DrupalDateTime;


/**
 * The specific class to gather includes and pass to template
 *
 */
class EventController extends ContentController {



	public function getIncludes () {

		$this->result["listing_ajax"] = $this->getEvents();
		$this->result["filter_ajax"] = $this->datePicker();

		return $this->result;

	}





	protected function getEvents (){

		// Get current date plus one month
		$now = new DrupalDateTime('now');
		$plusMonth = new DrupalDateTime('now +1 month');

		$defaultDate = $now->format("d/m/Y").'--'.$plusMonth->format("d/m/Y");


		return [
			'#type' => 'markup',
			'#markup' => '<div class="ajax-list-container ajax-calendar loading" data-ajax-path="/efq/event" data-default-hash="'.$defaultDate.'"></div>'
		];



	}




	protected function datePicker(){

		// Get current date plus one month
		$now = new DrupalDateTime('now');
		$plusMonth = new DrupalDateTime('now +2 month');
		$from = $now->format("d/m/Y");
		$to = $plusMonth->format("d/m/Y");

		$form = \Drupal::formBuilder()->getForm('Drupal\efq\Form\FilterForm');

		$form["container"] =  array(
			'#prefix' => '<div class="row filter-count-2 filter-events">',
			'#suffix' => '</div>',
			'from' => array(
				'#attributes' => [
					'id' => 'from',
					'class' => ['from-date'],
					'data-filter-group' => "from",
					'readonly' => 'true'
				],
				'#title' => 'From',
				'#type' => 'textfield',
				'#value' => $from
			),
		  'to' => array(
			'#attributes' => [
			  'id' => 'to',
			  'class' => ['to-date'],
			  'data-filter-group' => "to",
			  'readonly' => 'true'
			],
			'#title' => 'To',
			'#type' => 'textfield',
			'#value' => $to
		  )
		);

		return $form;

	}






}
