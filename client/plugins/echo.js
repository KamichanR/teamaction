import Echo from 'laravel-echo';

export default function ({ $config }) {
  window.Pusher = require('pusher-js');

  window.Echo = new Echo({
      broadcaster: 'pusher',
      key: $config.mixPusherAppKey,
      cluster: $config.mixPusherAppCluster,
      forceTLS: true,
      encrypted: true,
  });
}

