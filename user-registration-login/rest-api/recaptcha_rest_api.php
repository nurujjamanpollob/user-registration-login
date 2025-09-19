<?php



/**
 * Callback function for hello world rest api
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function hello_world_callback(WP_REST_Request $request): WP_REST_Response
{
    return new WP_REST_Response(array('message' => 'Hello World'), 200);
}

/**
 * Callback function for recaptcha verify rest api
 *
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function recaptcha_verify_callback(WP_REST_Request $request): WP_REST_Response
{
    $response = $request->get_param('response');
    $remoteip = $_SERVER["REMOTE_ADDR"];
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => get_option(RECAPTCHA_SECRET_KEY_OPTION_NAME),
        'response' => $response,
        'remoteip' => $remoteip
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        return new WP_REST_Response(array('success' => false), 500);
    }

    $result = json_decode($result);

    return new WP_REST_Response(array('success' => $result->success), 200);
}


