<?php
require_once __DIR__.'/handler.php';
require_once __DIR__.'/router.php';

get('/api/events/$event_id/screens/register', function($event_id) {
    register_screen($event_id);
});

post('/api/events/$event_id/screens/$screen_id/slides', function($event_id, $screen_id) {
    push_slides($event_id, $screen_id);
});

post('/api/events/$event_id/slides', function($event_id) {
    save_slides($event_id);
});

any('/404','404.php');