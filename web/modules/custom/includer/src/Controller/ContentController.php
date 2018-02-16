<?php

/**
 * @file 
 * Contains \Drupal\includer\Controller\ContentController
 */
 
namespace Drupal\includer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\efq\EfqQueryEntities;
use Drupal\helper\RenderHelper;
use Drupal\helper\TaxoHelper;
use Drupal\meteo\MeteoHelper;
use Drupal\helper\GlobalHelper;
use Drupal\helper\NodeHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

 
/**
 * The main content controller to gather includes and pass to template
 *
 */
class ContentController extends ControllerBase {


    protected $efqService;
    protected $renderHelper;
    protected $taxoHelper;
    protected $globalHelper;
    protected $nodeHelper;
    protected $result;
    protected $arguments;
    
    
    /**
     * Class constructor.
     */
    public function __construct(EfqQueryEntities $efqService, RenderHelper $renderHelper, TaxoHelper $taxoHelper, GlobalHelper $globalHelper, NodeHelper $nodeHelper) {

        $this->efqService = $efqService;
        $this->renderHelper = $renderHelper;
        $this->taxoHelper = $taxoHelper;
        $this->globalHelper = $globalHelper;
        $this->nodeHelper = $nodeHelper;
    }
    
    
    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('efq.query_entities'),
            $container->get('helper.render_helper'),
            $container->get('helper.taxo_helper'),
            $container->get('helper.global_helper'),
            $container->get('helper.node_helper')
        );
    }


    public function setArguments ( $arguments ){

        $this->arguments = $arguments;

    }
    
    
    public function getIncludes () {

        return $this->result; 
        
    }


    public function getTaxoTermsAsFilters ( $terms, $selectText ){

        $filters = array(
            '' => t($selectText),
        );

        foreach($terms as $term){
            $alias = $this->taxoHelper->getTaxoPathAlias($term->tid);
            $alias = str_replace('/taxonomy-term/','',$alias);
            $alias = str_replace('-0','',$alias);
            $filters['.cat-'.$alias] = t($term->name);
        }

        return $filters;

    }



  public function buildHeader ($title) {

    return '<h2 class="line"><span>'.$title.'</span></h2>';

  }



    
    
   

}
