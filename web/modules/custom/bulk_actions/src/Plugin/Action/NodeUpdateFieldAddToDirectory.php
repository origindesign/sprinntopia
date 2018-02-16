<?php

namespace Drupal\bulk_actions\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;


/**
 * Add To Directory
 *
 * @Action(
 *   id = "node_update_field_add_to_directory",
 *   label = @Translation("Add To Business Directory"),
 *   type = "node"
 * )
 */
class NodeUpdateFieldAddToDirectory extends ActionBase {

    /**
     * {@inheritdoc}
     */
    public function execute($entity = NULL) {
        /** @var \Drupal\node\NodeInterface $entity */
        if ($entity->hasField('field_ignore')) {
            $entity->field_ignore->setValue(0);
            $entity->save();
        }


    }


    /**
     * {@inheritdoc}
     */
    public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
        /** @var \Drupal\node\NodeInterface $object */
        $result = $object->access('update', $account, TRUE)
            ->andIf($object->field_ignore->access('edit', $account, TRUE));

        return $return_as_object ? $result : $result->isAllowed();
    }

}