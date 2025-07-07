<?php
function appendParams($param = []){
    $urlQuery = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    parse_str($urlQuery, $paramsArr);

    return http_build_query(array_merge($paramsArr, $param));
}

function getBindType($value) {
    return is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
}