<?php

function is_valid_business_id($string) {
    return preg_match('/^[0-9]{7}\-[0-9]{1}$/', $string) === 1;
}

function handle_exception(Exception $exception) {
    header('HTTP/1.1 500 Internal Server Error');
    echo $exception->getMessage();
}

set_exception_handler('handle_exception');

$baseUrl = 'http://avoindata.prh.fi:80/';
$db = new SQLite3('db.sqlite3');

$businessId = isset($_GET['businessId']) ? $_GET['businessId'] : null;
$update = isset($_GET['update']);

header('Content-type: application/json');

if ($businessId === null) throw new Exception("Sovellusvirhe");
if (!is_valid_business_id($businessId)) throw new Exception("Virheellinen Y-tunnus");

$cached = $db->querySingle("SELECT * FROM results WHERE business_id = '$businessId'", true);

if ($update || $cached === false || empty($cached)) {
    $db->exec("DELETE FROM results WHERE business_id = '$businessId'");
    $resource = curl_init($baseUrl . 'tr/v1/' . $businessId);
    curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($resource);

    if ($response !== false) {
        $model = json_decode($response);
        $result = empty($model->results) ? null : $model->results[0];
        $timestamp = time();
        $data = json_encode($result);
        $db->exec("INSERT INTO results (business_id, fetched, data) VALUES ('$businessId', $timestamp, '$data')");
        echo json_encode(array(
            'business_id' => $businessId,
            'fetched' => $timestamp,
            'data' => $result
        ));
    }
    else {
        throw new Exception('Palvelu ei vastaa, yritä myöhemmin uudelleen');
    }
}
else {
    echo json_encode(array(
        'business_id' => $cached['business_id'],
        'fetched' => $cached['fetched'],
        'data' => json_decode($cached['data'])
    ));
}

