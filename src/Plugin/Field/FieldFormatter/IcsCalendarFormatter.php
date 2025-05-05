<?php

namespace Drupal\ics_calendar\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ics_calendar\Service\IcsFetcher;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @FieldFormatter(
 *   id = "ics_calendar_formatter",
 *   label = @Translation("ICS Calendar Formatter"),
 *   field_types = {"string", "string_long"}
 * )
 */
class IcsCalendarFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  protected $icsFetcher;

  public function __construct($plugin_id, $plugin_definition, $field_definition, array $settings, $label, $view_mode, array $third_party_settings, IcsFetcher $icsFetcher) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->icsFetcher = $icsFetcher;
  }

  public static function create(ContainerInterface $container, array $config, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $config['field_definition'],
      $config['settings'],
      $config['label'],
      $config['view_mode'],
      $config['third_party_settings'],
      $container->get('ics_calendar.fetcher')
    );
  }

  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $item) {
      $dates = $this->icsFetcher->fetchDates($item->value);
      $elements[] = [
        '#theme' => 'ics_calendar_display',
        '#dates' => $dates,
        '#attached' => [
          'library' => ['ics_calendar/fullcalendar'],
        ],
        '#cache' => [
          'tags' => $items->getEntity()->getCacheTags(),
          'max-age' => 3600, // Cache for 1 hour, adjust as needed.
        ],
      ];
    }

    return $elements;
  }
}
