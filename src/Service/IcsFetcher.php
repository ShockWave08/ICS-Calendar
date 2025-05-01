<?php

namespace Drupal\ics_calendar\Service;

use Drupal\Core\Http\ClientFactory;
use ICal\ICal;

class IcsFetcher {

  protected $httpClient;

  public function __construct(ClientFactory $clientFactory) {
    $this->httpClient = $clientFactory->fromOptions([
      'timeout' => 10,
      'verify' => FALSE,
    ]);
  }

  /**
   * Fetch and parse ICS from URL, return dates array.
   */
  public function fetchDates(string $url): array {
    try {
      $response = $this->httpClient->get($url);
      $ics_content = (string) $response->getBody();

      $ical = new ICal(false, ['defaultTimeZone' => 'America/Barbados']);
      $ical->initString($ics_content);
      $events = $ical->events();

      $dates = [];
      foreach ($events as $event) {
        $dates[] = [
          'title' => $event->summary,
          'start' => date('Y-m-d', strtotime($event->dtstart)),
          'end' => date('Y-m-d', strtotime($event->dtend)),
        ];
      }

      return $dates;

    } catch (\Exception $e) {
      \Drupal::logger('ics_calendar')->error($e->getMessage());
      return [];
    }
  }
}

