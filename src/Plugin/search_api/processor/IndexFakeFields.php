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

    if (!$datasource) {
      $definition = [
        'label' => $this->t('Fake fields'),
        'description' => $this->t('Fake fields'),
        'type' => 'entity:node',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['fakefields_fake_field_1'] = new ProcessorProperty($definition);
      $properties['fakefields_fake_field_2'] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {

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


    $fields = $item->getFields(FALSE);
    $field_1 = $this->getFieldsHelper()
      ->filterForPropertyPath($fields, NULL, 'fakefields_fake_field_1');
    $field_2 = $this->getFieldsHelper()
      ->filterForPropertyPath($fields, NULL, 'fakefields_fake_field_2');
    $field_1['fakefields_fake_field_1']->addValue("Field 1 and feeling fine.");
    $field_2['fakefields_fake_field_2']->addValue("What are you looking at?");
  }

}
