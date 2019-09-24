<?php

namespace Drupal\fakefields\Plugin\search_api\processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
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
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */
class IndexFakeFields extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    // $node_storage = \Drupal::entityTypeManager()->getStorage('node');

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Fake fields'),
        'description' => $this->t('Fake fields'),
        'type' => 'entity:node',
        'processor_id' => $this->getPluginId(),
      ];

      // @todo: get this list from the value of the storage field (e.g. 'field_mark_s_fake_field'). But how do we get that field's value?
      $this->fake_fields = array('fakefields_fake_field_1', 'fakefields_fake_field_2');
      foreach ($this->fake_fields as $fake_field) {
        $properties[$fake_field] = new ProcessorProperty($definition);
      }
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    dd($this->properties);
    $node = $item->getOriginalObject()->getValue();
    if (!($node instanceof NodeInterface)) {
      return;
    }

    if ($node->hasField('field_mark_s_fake_field')) {
      $parser = new Parser();
      $fake_field = $node->get('field_mark_s_fake_field')->getValue();
      if (isset($fake_field[0]['value'])) {
        dd($fake_field[0]['value']);
      }
    }

    // $fake_fields = array('fakefields_fake_field_1', 'fakefields_fake_field_2');
    $fields = $item->getFields(FALSE);
    foreach ($this->fake_fields as $fake_field) {
      $field = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, $fake_field);
      $field[$fake_field]->addValue("Field 1 and feeling fine.");
    }
  }
}