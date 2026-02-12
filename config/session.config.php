<?php

ini_set("session.use_only_cookies", 1);
ini_set("session.use_strict_mode", 1);

session_set_cookie_params([
    'lifetime' => 1800,
    'domain' => 'www.sissisemijoiaseacessorios.com.br',
    'path' => '/',
    'secure' => true,
    'httponly' => true
]);

session_start();

// regenerate session ID every 30 minutes


/* 
    get the current time if the session doesn't exists and store it in the session.
    but if the session exists, then it will check if the current time minus
    the session creation time is greater than or equal to the interval

    if it is, regenerate the session
*/
if(isset($_SESSION['user_id'])) {
    if(!isset($_SESSION['last_regeneration'])) {
        regenerate_session_id_loggedin();
    } else {
        $interval = 60 * 30;

        if (time() - $_SESSION['last_regeneration'] >= $interval) { //check if the interval has expired
            regenerate_session_id_loggedin();
        };
    };
} else {
    if(!isset($_SESSION['last_regeneration'])) {
        regenerate_session_id();
    } else {
        $interval = 60 * 30;

        if (time() - $_SESSION['last_regeneration'] >= $interval) { //check if the interval has expired
            regenerate_session_id();
        };
    };
}

function regenerate_session_id() {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time(); //store current timestamp
}

function regenerate_session_id_loggedin() {
    session_regenerate_id(true);

    $userId = $_SESSION['user_id'];
    $newSessionId = session_create_id();
    $sessionId = $newSessionId . "_" . $userId;
    session_id($sessionId);

    $_SESSION["last_regeneration"] = time();
} 