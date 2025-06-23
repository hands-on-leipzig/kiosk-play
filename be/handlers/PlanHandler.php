<?php

namespace handlers;

require_once '../vendor/autoload.php';

use controllers;
use Exception;
use model\MysqlDB;
use model\Router;

$db = new MysqlDB();
$db->dbConnect();
$h = new Router();

header("Access-Control-Allow-Origin: *");


// open endpoints
$h->post('/api/auth', function () {
    $t = new controllers\TokenController();
    echo $t->getJWTToken($_POST["code"]);
});

// endpoints needing authorization
try {
    $auth = $h->auth();
    var_dump($auth);
} catch (Exception $e) {
    http_response_code($e->getCode());
    exit($e->getMessage());
}

$h->get('/api/scheduler/schedule/$schedule_id', function ($plan_id) {
    $s = new controllers\PlanController();

    try {
        $result = json_encode($s->getPlan($plan_id));
    } catch (Exception $e) {
        http_response_code($e->getCode());
        exit($e->getMessage());
    }
    if (json_validate($result)) {
        header("Content-Type: application/json");
    }
    echo $result;
});

$h->get('/api/scheduler/schedule/$schedule_id/params', function ($plan_id) {
    $s = new controllers\PlanController();

    try {
        $result = json_encode($s->getParams($plan_id));
    } catch (Exception $e) {
        http_response_code($e->getCode());
        exit($e->getMessage());
    }
    if (json_validate($result)) {
        header("Content-Type: application/json");
    }
    echo $result;
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

$h->any('/404', '../404.php');