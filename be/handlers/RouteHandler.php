<?php

namespace handlers;

require_once '../vendor/autoload.php';

use controllers;
use Exception;
use model\MysqlDB;
use model\Router;
use model\Screen;

$db = new MysqlDB();
$db->dbConnect();
$h = new Router();

header("Access-Control-Allow-Origin: *");


// open endpoints
$h->post('/api/auth', function () {
    $t = new controllers\TokenController();
    echo $t->getJWTToken($_POST["code"]);
});

$h->get('/api/events/$event_id/data/rg-scores', function ($event_id) {
    $s = new controllers\ScoreController();

    try {
        $scores = json_encode($s->getScores($event_id));
    } catch (Exception $e) {
        http_response_code($e->getCode());
        exit($e->getMessage());
    }
    if (json_validate($scores)) {
        header("Content-Type: application/json");
    }
    echo $scores;
});

// endpoints needing authorization
try {
    $h->auth();
} catch (Exception $e) {
    http_response_code($e->getCode());
    exit($e->getMessage());
}

$h->post('/api/events/$event_id/screens/register', function ($event_id) {
    global $db;
    $screen = new Screen($db);
    echo $screen->register($event_id);
});

$h->post('/api/events/$event_id/scores/show-rounds', function ($event_id) {
    $s = new controllers\ScoreController();
    try {
        $s->setRoundsToShow($event_id);
    } catch (Exception $e) {
        http_response_code($e->getCode());
        exit($e->getMessage());
    }
});

$h->get('/api/events/$event_id/scores/show-rounds', function ($event_id) {
    $s = new controllers\ScoreController();
    try {
        $res = $s->getRoundsToShow($event_id);
    } catch (Exception $e) {
        http_response_code($e->getCode());
        exit($e->getMessage());
    }
    echo $res;
});

$h->get('/api/events/$event_id/screens', function ($event_id) {
    global $db;
    $screen = new Screen($db);
    echo $screen->show_screens($event_id);
});

$h->post('/api/events/$event_id/settings', function ($event_id) {
    $s = new controllers\SettingController($event_id);
    try {
        $s->saveSettings();
    } catch (Exception $e) {
        http_response_code($e->getCode());
        exit($e->getMessage());
    }
});

$h->get('/api/events/$event_id/settings', function ($event_id) {
    $s = new controllers\SettingController($event_id);
    try {
        $res = $s->getSettings();
    } catch (Exception $e) {
        http_response_code($e->getCode());
        exit($e->getMessage());
    }
    echo $res;
});

$h->post('/api/events/$event_id/slides', function ($event_id) {
    $s = new controllers\SlideController($event_id);
    try {
        $s->addSlide();
    } catch (Exception $e) {
        http_response_code($e->getCode());
        exit($e->getMessage());
    }
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