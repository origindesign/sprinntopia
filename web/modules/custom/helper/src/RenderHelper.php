<?php
/**
 * @file Contains \Drupal\helper\RenderHelper
 */
 
namespace Drupal\helper;


use Drupal\efq\EfqQueryEntities;

 
/**
 * A Class for helping in rendering elements in templates
 *
 */
class RenderHelper {


	/**
	 * @var \Drupal\efq\EfqQueryEntities
	 */
	protected $efqQueryEntities;




	// Dependency injection pattern.
	public function __construct(EfqQueryEntities $EfqQueryEntities) {
		$this->efqQueryEntities = $EfqQueryEntities;
	}





	/**
	 * Return a block
	 */
	public function renderBlock () {

		return [
			'#type' => 'markup',
			'#markup' => '<p>This will render a block.</p>',
		];


	}




    /**
     * Return a block that uncache the page it's included on
     */
    public function renderUncacheBlock () {
      \Drupal::service('page_cache_kill_switch')->trigger();
      return [
        '#type' => 'markup',
        '#markup' => '<span class="no-cache" style="display:none"></span>',
        '#cache' => array(
          'max-age' => 0,
        ),

      ];


    }


}
