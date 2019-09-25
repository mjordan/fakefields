<?php

namespace Drupal\fakefields\Plugin\search_api\processor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Plugin\PluginFormTrait;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\node\NodeInterface;
use Symfony\Component\Yaml\Parser;

/**
 * Adds "fake fields" to the index.
 *
 * @SearchApiProcessor(
 *   id = "fakefields_index_fake_fields",
 *   label = @Translation("Index fake fields"),
 *   description = @Translation("Index fields managed by the Fake Fields module."),
 *   stages = {
 *     "alter_items" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class IndexFakeFields extends ProcessorPluginBase {

  public static function supportsIndex(IndexInterface $index) {
    $interface = NodeInterface::class;
    foreach ($index->getDatasources() as $datasource) {
      $entity_type_id = $datasource->getEntityTypeId();
      if (!$entity_type_id) {
        continue;
      }
      if ($entity_type_id === 'node') {
        return TRUE;
      }
      $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);
      if ($entity_type && $entity_type->entityClassImplements($interface)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function alterIndexedItems(array &$items) {
    foreach ($items as $item_id => $item) {
      $object = $item->getOriginalObject()->getValue();
      if (!$object instanceof NodeInterface) {
        return;
      }
      // unset($items[$item_id]);
      $items['fakefield_1'][0]['value'] = 'Fake field 1 value';
      $items['fakefield_2'][0]['value'] = 'Fake field 2 value';
    }
  }

}
