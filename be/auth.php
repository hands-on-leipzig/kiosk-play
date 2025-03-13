<?php


require_once 'db.php';
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$code = $data['code'];

$fields = [
    "grant_type" => "authorization_code",
    "client_id" => $_ENV["CLIENT_ID"],
    "client_secret" => $_ENV["CLIENT_SECRET"],
    "code" => $code,
    "redirect_uri" => "http://localhost:5173/callback"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $_ENV["KEYCLOAK_URL"]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$keycloak_response = json_decode(curl_exec($ch), true);
curl_close($ch);

if (!isset($keycloak_response["access_token"])) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid authentication"]);
    exit;
}

$jwt_payload = [
    "sub" => $keycloak_response["sub"],
    "username" => $keycloak_response["preferred_username"],
    "exp" => time() + 3600 // Token expires in 1 hour
];

$jwt_secret = "your-very-secure-secret";
$jwt_token = base64_encode(json_encode($jwt_payload)) . "." . base64_encode(hash_hmac("sha256", json_encode($jwt_payload), $jwt_secret, true));

echo json_encode(["jwt_token" => $jwt_token]);

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