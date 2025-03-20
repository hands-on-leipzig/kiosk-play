<?php

use handlers\ApiHandler;
use model\Screen;
use model\MysqlDB;
$db = new MysqlDB();
$h = new ApiHandler($db);

$h->post('/api/events/$event_id/screens/register', function($event_id) {
    global $h, $db;
    $screen = new Screen($db);
    $screen->register($event_id);
});

$h->get('/api/events/$event_id/screens', function($event_id) {
    global $h, $db;
    $screen = new Screen($db);
    $screen->show_screens($event_id);
});
/*
post('/api/events/$event_id/screens/$screen_id/slides', function($event_id, $screen_id) {
    push_slides($event_id, $screen_id);
});

post('/api/events/$event_id/slides', function($event_id) {
    save_slides($event_id);
});
*/
$h->any('/404','404.php');