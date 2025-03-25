<?php

require_once 'db.php';
require_once 'vendor/autoload.php';


/*
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Jumbojett\OpenIDConnectClient;

$secretKey = $_ENV["JWT_SECRET"];
$issuedAt = time();
$expiration = $issuedAt + 3600; // Token valid for 1 hour

// Mock user authentication (replace with database verification)
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === "testuser" && $password === "password123") {
    $payload = [
        "iat" => $issuedAt,
        "exp" => $expiration,
        "sub" => $username,
    ];

    $jwt = JWT::encode($payload, $secretKey, 'HS256');

    echo json_encode(["token" => $jwt]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
}


/*
$dev = false;
$user_db = new stdClass();


$oidc = new OpenIDConnectClient('https://sso.hands-on-technology.org/realms/master',
    'planning',
    'Vl5f0vQKBzpq76dbDoNmUPk7lMNQIvG5');

$backto = "";
$redirect_uri = "https://" . $_SERVER["HTTP_HOST"] . ":443/auth.php" . ($backto ? ("?backto=" . $backto) : "");
$oidc->addScope(["profile"]);
$oidc->setRedirectURL($redirect_uri);
try {
    $oidc->authenticate();
} catch (\Jumbojett\OpenIDConnectClientException $e) {
    header('Location: timed_out');
}

$user_info = $oidc->requestUserInfo();

$sub = $user_info->sub;

if (isset($user_info->dolibarrId)) {
    $_SESSION['dolibarr_id'] = $user_info->dolibarrId;
}

$token = $oidc->getIdToken();
$introspect = $oidc->introspectToken($token);

if (hasAccess($introspect)) {
    require_once 'include/db.inc.php';
    $db = new MysqlDB();
    $db->dbConnect();

    $res = $db->selectAsObj('user', 'subject', '=', $introspect->sub, "char");
    if (!$res) {
        // Register new user
        $values = [
            $sub,
            $user_info->dolibarrId ?? 0,
            "de",
            $db->now(),
            isAdmin($introspect) ? "1" : "0",
        ];
        $user_db_id = $db->insertInto("user", ["subject", "dolibarr_id", "lang", "last_login", "is_admin"], $values);
    } else {
        $db->execute("UPDATE user SET last_login='" . $db->now() . "', is_admin=" . (isAdmin($introspect) ? "1" : "0") . ", dolibarr_id=" . $user_info->dolibarrId . " WHERE subject='" . $sub . "'");
        $user_db_id = $res[0]->id;
    }

    if (!isAdmin($introspect)) {

        $res = $db->selectAsObj("user_regional_partner", "user", "=", $user_db_id);

        if (!$res) {
            $rp_ids = callAPI("/handson/contact/" . $user_info->dolibarrId . "/regionalpartner");

            if (!$rp_ids) {
                header('Location: login/wrong_role');
                exit(0);
            }

            foreach ($rp_ids as $doli_rp_id) {
                $rp = $db->selectAsObj("regional_partner", "dolibarr_id", "=", $doli_rp_id);
                if (!$rp) {
                    $doli_rp = callAPI("/handson/rp/" . $doli_rp_id . "/planner");
                    $rp_id = $db->insertInto("regional_partner", ["region", "dolibarr_id"], [$doli_rp->name, (int)$doli_rp_id]);
                } else {
                    $rp_id = $rp[0]->id;
                }
                $db->insertInto("user_regional_partner", ["user", "regional_partner"], [$user_db_id, (int)$rp_id]);
            }
        }
    }

    $_SESSION['token'] = $token;
    $_SESSION['user_id'] = $user_db_id;

    $user = $db->selectAsObj("user", "id", "=", $user_db_id);
    if ($user && $user[0]->selection_event) {
        header('Location: event/' . $user[0]->selection_event);
    } else {
        header('Location: home');
    }
} else {
    exit(0);
    header('Location: login/wrong_role');
}