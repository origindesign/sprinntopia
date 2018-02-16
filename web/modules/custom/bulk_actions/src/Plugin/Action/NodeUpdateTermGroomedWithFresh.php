<?php

namespace Drupal\bulk_actions\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\taxonomy\Entity\Term;


/**
 * Change Status to Groomed with fresh
 *
 * @Action(
 *   id = "node_update_term_groomed_with_fresh",
 *   label = @Translation("Change Status to Groomed with fresh"),
 *   type = "node"
 * )
 */
class NodeUpdateTermGroomedWithFresh extends ActionBase {

    /**
     * {@inheritdoc}
     */
    public function execute($entity = NULL) {
        /** @var \Drupal\node\NodeInterface $entity */
        if ($entity->hasField('field_status')) {
            $term = Term::load(129);
            $entity->field_status->setValue([$term]);
            $entity->save();
        }


    }


    /**
     * {@inheritdoc}
     */
    public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
        /** @var \Drupal\node\NodeInterface $object */
        $result = $object->access('update', $account, TRUE)
            ->andIf($object->field_status->access('edit', $account, TRUE));

        return $return_as_object ? $result : $result->isAllowed();
    }

}