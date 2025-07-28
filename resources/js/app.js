import './bootstrap';
import './components/LiveUserCounter';
import './components/ActivityFeed';
import './components/NotificationBell';

// Import Master Layout JavaScript
import './admin/master-layout';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
