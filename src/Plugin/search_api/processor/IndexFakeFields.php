<?php

namespace Drupal\fakefields\Plugin\search_api\processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorProperty;
use Drupal\node\NodeInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\TypedData\EntityDataDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\TypedData\ComplexDataDefinitionInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Item\FieldInterface;
use Drupal\search_api\Plugin\PluginFormTrait;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Utility\Utility;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
 *   }
 * )
 */
class IndexFakeFields extends ProcessorPluginBase implements PluginFormInterface {

  /**
   * The list of fake fields in the processor config form.
   *
   * @var array
   */
  protected $fake_fields_list;

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

      $this->fake_fields_list = preg_split("/\\r\\n|\\r|\\n/", $this->configuration['fake_fields']);
      foreach ($this->fake_fields_list as $fake_field_name) {
        $fake_field_name = trim($fake_field_name);
        dd($fake_field_name);
        $properties[$fake_field_name] = new ProcessorProperty($definition);
      }
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

    $parser = new Parser();
    $fields = $item->getFields(FALSE);
    $fake_fields_source = $this->configuration['fake_fields_source'];
    if ($node->hasField($fake_fields_source)) {
      $fake_fields = array();
      $fake_fields_source_value = $node->get($fake_field_source)->getValue();
      if (isset($fake_fields_source[0]['value'])) {
        $fake_fields = preg_split("/\\r\\n|\\r|\\n/", $fake_fields_source[0]['value']);
        foreach ($fake_fields as $fake_field) {
	  list($fake_field_name, $fake_field_value) = explode(':', $fake_field, 2); 
          $fake_field_name = trim($fake_field_name);
	  $field = $this->getFieldsHelper()->filterForPropertyPath($fields, NULL, $fake_field_name);
          $field[$fake_field_name]->addValue(trim($fake_field_value));
        }
      }
    }

  }

 /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();

    $configuration += [
      'fake_fields_source' => '',
      'fake_fields' => '',
    ];

    return $configuration;
  }

 /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['fake_fields_source'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Machine namd of field that holds your "fake fields"'),
      '#default_value' => $this->configuration['fake_fields_source'],
    ];

    $form['fake_fields'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Fake fields'),
      '#description' => $this->t('Fake field names. One per line.'),
      '#default_value' => $this->configuration['fake_fields'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $formState) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->setConfiguration($form_state->getValues());
  }
  
}
