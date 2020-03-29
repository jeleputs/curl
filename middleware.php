<?php

function validateLogin()
{
    header('Content-Type: application/json');
    $data = cleanupData($_GET);
    if (isValid(array($data['username'], $data['password'], $data['date']), $data['hash'])) {
        $response = json_decode(login($data), true);
        if (isValid(array($response['id'], $response['date']), $response['hash'])) {
            echo json_encode($response);
        }

    }
}

function cleanupData($data)
{
    $_DIE_NOT_ENOUGH = json_encode(array('error' => '404', 'message' => 'not enough data'));
    $_DIE_UNEXPECTED = json_encode(array('error' => '404', 'message' => 'unexpected data'));
    $_DATA_VAR       = array('username', 'password', 'hash', 'date');

    //if there is any untrusted data, return 404

    $itemCount = 0;
    foreach ($data as $k => $v) {
        if (in_array($k, $_DATA_VAR) === false) {
            http_response_code(404);
            die($_DIE_UNEXPECTED);
        } else {
            $data[$k] = addslashes(trim($v));
            $itemCount++;
        }
    }
    if ($itemCount !== sizeof($_DATA_VAR)) {
        http_response_code(404);
        die($_DIE_NOT_ENOUGH);
    }
    return $data;
}
function isValid($data, $hash)
{

    $_DIE         = json_encode(array('error' => '404', 'message' => 'data has been tempered'));
    $concatenated = "";
    foreach ($data as $v) {
        $concatenated .= "$v";
    }
    if (hash('sha256', $concatenated) === $hash) {
        return true;
    }
    http_response_code(404);
    die($_DIE);
}

function login($data)
{
    $_SERVICE = 'http://www.beliveo.net/beliveo/bbox/wp-content/themes/BLANK-Theme-extranet/private/mockLogin.php';
    define("DOC_ROOT", "/path/to/html");

    $path = DOC_ROOT . "/ctemp";

    $postinfo = "";
    foreach ($data as $k => $v) {
        $postinfo .= "$k=$v&";
    }

    $cookie_file_path = $path . "/cookie.txt";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_URL, $_SERVICE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);

    curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");
    curl_setopt($ch, CURLOPT_USERAGENT,
        "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
    $data = curl_exec($ch);
    curl_close($ch);
    return ($data);
}

validateLogin() || die;
