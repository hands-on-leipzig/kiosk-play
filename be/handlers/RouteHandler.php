<?php

namespace handlers;

require_once '../vendor/autoload.php';

use controllers\ScoreController;
use Exception;
use model\MysqlDB;
use model\Router;
use model\Screen;

$db = new MysqlDB();
$db->dbConnect();
$h = new Router();

try {
    $h->auth();
} catch (Exception $e) {
    http_response_code(403);
    exit("Unauthorized");
}

$h->post('/api/events/$event_id/screens/register', function ($event_id) {
    global $db;
    $screen = new Screen($db);
    echo $screen->register($event_id);
});

$h->get('/api/events/$event_id/screens', function ($event_id) {
    global $db;
    $screen = new Screen($db);
    echo $screen->show_screens($event_id);
});

$h->get('/api/events/$event_id/data/rg-scores', function ($event_id) {
    $s = new ScoreController();
    echo json_encode($s->getScore($event_id));
});
/*
post('/api/events/$event_id/screens/$screen_id/slides', function($event_id, $screen_id) {
    push_slides($event_id, $screen_id);
});

post('/api/events/$event_id/slides', function($event_id) {
    save_slides($event_id);
});
*/
$h->any('/404', '../404.php');