(function (Drupal, once) {
  Drupal.behaviors.icsCalendar = {
    attach: function (context) {
      once('ics-calendar-init', '.ics-calendar', context).forEach(function (el) {
        const dates = JSON.parse(el.getAttribute('data-dates'));
        const calendar = new FullCalendar.Calendar(el, {
          initialView: 'dayGridMonth',
          events: dates,
        });
        calendar.render();
      });
    }
  };
})(Drupal, once);

