<?php

namespace handlers;

require_once '../vendor/autoload.php';

use controllers;
use Exception;
use model\MysqlDB;

$db = new MysqlDB();
$db->dbConnect();
$h = new Router();

header("Access-Control-Allow-Origin: *");


// open endpoints
$h->post('/api/auth', function () {
    $t = new controllers\TokenController();
    echo $t->getJWTToken($_POST["code"]);
});

// TODO remove the following
$h->get('/api/scheduler/schedule/$schedule_id', function ($schedule_id) {
    $s = new controllers\ScheduleController();

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

$h->post('/api/events/$event_id/settings', function ($event_id) {
    $s = new controllers\SettingController($event_id);
    try {
        $s->saveSettings();
    } catch (Exception $e) {
        http_response_code($e->getCode());
        exit($e->getMessage());
    }
});

$h->any('/404', '../404.php');