(function ($, Drupal, drupalSettings) {
  "use strict";
  /**
   * Attaches the JS countdown behavior
   */
  Drupal.behaviors.jsCountdownTimer = {
    attach: function (context) {
      var note = $('#jquery-countdown-timer-note'),
        ts = new Date(drupalSettings.countdown.unixtimestamp * 1000);

      $(context).find('#jquery-countdown-timer').once('jquery-countdown-timer').countdown({
        timestamp: ts,
        font_size: drupalSettings.countdown.fontsize,
        callback: function (days, hours, minutes) {
          var dateStrings = new Array();
          dateStrings['@days'] = Drupal.formatPlural(days, 'Day', 'Days');
          dateStrings['@hours'] = Drupal.formatPlural(hours, 'Hour', 'Hours');
          dateStrings['@minutes'] = Drupal.formatPlural(minutes, 'Minute', 'Minutes');
          var message = Drupal.t('@days @hours @minutes', dateStrings);
          note.html(message);


        }
      });
      
      var note_voting = $('#jquery-countdown-timer-note-voting'),
      ts = new Date(drupalSettings.countdownvoting.unixtimestampvoting * 1000);
      $(context).find('#jquery-countdown-timer-voting').once('jquery-countdown-timer-voting').countdown({
        timestamp: ts,
        font_size: drupalSettings.countdownvoting.fontsize,
        callback: function (days, hours, minutes) {
          var dateStrings = new Array();
          dateStrings['@days'] = Drupal.formatPlural(days, 'Day', 'Days');
          dateStrings['@hours'] = Drupal.formatPlural(hours, 'Hour', 'Hours');
          dateStrings['@minutes'] = Drupal.formatPlural(minutes, 'Minute', 'Minutes');
          var message = Drupal.t('@days @hours @minutes', dateStrings);
          note_voting.html(message);


        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
