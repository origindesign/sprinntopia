<?php
/**
 * @file Contains \Drupal\efq\EfqQueryEntities
 */
 
namespace Drupal\efq;

use Drupal\efq\QueryBuilder\QueryBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;

 
/**
 * A Service for querying entities.
 * 
 */
class EfqQueryEntities {

    
    protected $entityTypeManager;
    protected $queryBuilder;


    /**
     * EfqQueryEntities constructor.
     * @param EntityTypeManagerInterface $entityTypeManager
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(EntityTypeManagerInterface $entityTypeManager, QueryBuilder $queryBuilder) {
        $this->entityTypeManager  = $entityTypeManager;
        $this->queryBuilder = $queryBuilder;

    }


    /**
     * Get the entities requested by EntityQuery
     * @param string $bundle
     * @param string $view_mode
     * @param array $conditions
     * @param array $range
     * @param array $sortBy
     * @param bool $count
     * @param string $entity_type
     * @return array
     */
    public function getEntities ( $bundle = 'article', $view_mode = 'teaser', $conditions = NULL, $range = NULL, $sortBy = NULL, $count = false, $entity_type = 'node' ) {

        // Build the query
        $this->queryBuilder->build( $bundle, $conditions, $range, $sortBy, $entity_type );

        if ($count){
            $render = $this->queryBuilder->count();
        }else{
            // Run the query and grab entities IDs out of it.
            $nids = $this->queryBuilder->apply();
            // Get proper rendered Entities based on IDs and view mode
            $render = $this->renderNodes( $nids, $view_mode );
        }


        return $render;

    }




    /**
     * Return a list of nodes that are published.
     * @param $nids
     * @param $view_mode
     * @return string
     */
    protected function renderNodes ($nids, $view_mode) {

        if( !empty($nids) ){    
            
             // Get the node storage object.
            $node_storage = $this->entityTypeManager->getStorage('node');
            
            // Get the view builder object
            $view_builder = $this->entityTypeManager->getViewBuilder('node');

            // Use the loadMultiple function to load an array of node objects keyed by node ID.
            $nodes = $node_storage->loadMultiple($nids);

            // Prepare output for rendering
            $output = $view_builder->viewMultiple($nodes, $view_mode);
            
            return $output;
            
        }

        return false;
        
    }
    
    
}
