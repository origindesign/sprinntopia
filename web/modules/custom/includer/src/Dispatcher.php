<?php
/**
 * @file Contains \Drupal\includer\Dispatcher
 */
 
namespace Drupal\includer;


use Drupal\efq\EfqQueryEntities;
use Drupal\helper\RenderHelper;
use Drupal\helper\TaxoHelper;
use Drupal\helper\GlobalHelper;
use Drupal\helper\NodeHelper;


/**
 * The main service to dispatch controller, get includes and pass to template
 *
 */
class Dispatcher {



    /**
     * @var \Drupal\efq\EfqQueryEntities
     */
    protected $efqQueryEntities;
    
    
    /**
     * @var \Drupal\helper\RenderHelper
     */
    protected $renderHelper;


    /**
     * @var \Drupal\helper\TaxoHelper
     */
    protected $taxoHelper;


    /**
     * @var \Drupal\meteo\GlobalHelper
     */
    protected $globalHelper;


    /**
     * @var \Drupal\helper\NodeHelper
     */
    protected $nodeHelper;


    
    // Dependency injection pattern.
    public function __construct(EfqQueryEntities $EfqQueryEntities, RenderHelper $renderHelper, TaxoHelper $taxoHelper, GlobalHelper $globalHelper, NodeHelper $nodeHelper) {
        $this->efqQueryEntities = $EfqQueryEntities;
        $this->renderHelper = $renderHelper;
        $this->taxoHelper = $taxoHelper;
        $this->globalHelper = $globalHelper;
        $this->nodeHelper = $nodeHelper;
    }


    /**
     * Return a list of variable for templates
     * @param $controllerName
     * @param array $arguments
     * @return mixed
     */
    public function dispatch ($controllerName, $arguments = array() ) {
        
        // Locate the controler using double anti-slash because of escaping and for concatenation
        $controller_locate = "\\Drupal\\includer\\Controller\\".$controllerName;
        
        //Instantiate the controler including the services in construct method
        $controller = new $controller_locate($this->efqQueryEntities, $this->renderHelper, $this->taxoHelper, $this->globalHelper, $this->nodeHelper);

        // Set arguments if they're not empty
        if ( $arguments ){
            $controller->setArguments( $arguments );
        }

        // Return include variables to template
        return $controller->getIncludes();
   
    }
    
   

    

}
