import moment from 'moment';
import countdown from 'countdown';
import momentCountdown from 'moment-countdown';
(function() {
  const $el = document.querySelector('.countdown');
  const arrival = $el.dataset.arrival;
  const departure = $el.dataset.departure;
  const present = !!$el.dataset.present;
  let countdown;
  if (present) {
    setInterval(function () {
      countdown = moment(departure).countdown().toString();
      $el.textContent = `will depart in ${countdown}`;
    }, 1000);
  } else {
    setInterval(function () {
      countdown = moment(arrival).countdown().toString();
      $el.textContent = `will arrive in ${countdown}`;
    }, 1000);
  }
})()
