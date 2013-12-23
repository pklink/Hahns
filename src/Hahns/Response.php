<?php


namespace Hahns;


interface Response
{

    const CODE_OK = 200;
    const MSG_200 = 'OK';

    const CODE_CREATED = 201;
    const MSG_201 = 'Created';

    const CODE_ACCEPTED = 202;
    const MSG_202 = 'Accepted';

    const CODE_NON_AUTHORITATIVE_INFORMATION = 203;
    const MSG_203 = 'Non-Authoritative Information';

    const CODE_NO_CONTENT = 204;
    const MSG_204 = 'No Content';

    const CODE_RESET_CONTENT = 205;
    const MSG_205 = 'Reset Content';

    const CODE_PARTIAL_CONTENT = 206;
    const MSG_206 = 'Partial Content';

    const CODE_MULTI_STATUS_CODE = 207;
    const MSG_207 = 'Multi-Status';

    const CODE_ALREADY_REPORTED = 208;
    const MSG_208 = 'Already Reported';

    const CODE_IM_USED = 226;
    const MSG_226 = 'IM Used';

    const CODE_MULTIPLE_CHOICES = 300;
    const MSG_300 = 'Multiple Choices';

    const CODE_MOVED_PERMANENTLY = 301;
    const MSG_301 = 'Moved Permanently';

    const CODE_FOUND = 302;
    const MSG_302 = 'Found';

    const CODE_SEE_OTHER = 303;
    const MSG_303 = 'See Other';

    const CODE_NOT_MODIFIED = 304;
    const MSG_304 = 'Not Modified';

    const CODE_USE_PROXY = 305;
    const MSG_305 = 'Use Proxy';

    const CODE_SWITCH_PROXY = 306;
    const MSG_306 = 'Switch Proxy';

    const CODE_TEMPORARY_REDIRECT = 307;
    const MSG_307 = 'Temporary Redirect';

    const CODE_PERMANENT_REDIRECT = 308;
    const MSG_308 = 'Permanent Redirect';

    const CODE_BAD_REQUEST = 400;
    const MSG_400 = 'Bad Request';

    const CODE_UNAUTHORIZED = 401;
    const MSG_401 = 'Unauthorized';

    const CODE_PAYMENT_REQUIRED = 402;
    const MSG_402 = 'Payment Required';

    const CODE_FORBIDDEN = 403;
    const MSG_403 = 'Forbidden';

    const CODE_NOT_FOUND = 404;
    const MSG_404 = 'Not Found';

    const CODE_METHOD_NOT_ALLOWED = 405;
    const MSG_405 = 'Method Not Allowed';

    const CODE_NOT_ACCEPTABLE = 406;
    const MSG_406 = 'Not Acceptable';

    const CODE_PROXY_AUTHENTICATION_REQUIRED = 407;
    const MSG_407 = 'Proxy Authentication Required';

    const CODE_REQUEST_TIME_OUT = 408;
    const MSG_408 = 'Request Time-out';

    const CODE_CONFLICT = 409;
    const MSG_409 = 'Conflict';

    const CODE_GONE = 410;
    const MSG_410 = 'Gone';

    const CODE_LENGTH_REQUIRED = 411;
    const MSG_411 = 'Length Required';

    const CODE_PRECONDITION_FAILED = 412;
    const MSG_412 = 'Precondition Failed';

    const CODE_REQUEST_ENTITY_TOO_LARGE = 413;
    const MSG_413 = 'Request Entity Too Large';

    const CODE_REQUEST_URL_TOO_LONG = 414;
    const MSG_414 = 'Request-URL Too Long';

    const CODE_UNSUPPORTED_MEDIA_TYPE = 415;
    const MSG_415 = 'Unsupported Media Type';

    const CODE_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const MSG_416 = 'Requested range not satisfiable';

    const CODE_EXPECTATION_FAILED = 417;
    const MSG_417 = 'Expectation Failed';

    const CODE_IM_A_TEAPOT = 418;
    const MSG_418 = 'I\'m a teapot';

    const CODE_POLICY_NOT_FULFILLED = 420;
    const MSG_420 = 'Policy Not Fulfilled';

    const CODE_THERE_ARE_TOO_MANY_CONNECTIONS_FROM_YOUR_INTERNET_ADDRESS = 421;
    const MSG_421 = 'There are too many connections from your internet address';

    const CODE_UNPROCESSABLE_ENTITY = 422;
    const MSG_422 = 'Unprocessable Entity';

    const CODE_LOCKED = 423;
    const MSG_423 = 'Locked';

    const CODE_FAILED_DEPENDENCY = 424;
    const MSG_424 = 'Failed Dependency';

    const CODE_UNORDERED_COLLECTION = 425;
    const MSG_425 = 'Unordered Collection';

    const CODE_UPGRADE_REQUIRED = 426;
    const MSG_426 = 'Upgrade Required';

    const CODE_PRECONDITION_REQUIRED = 428;
    const MSG_428 = 'Precondition Required';

    const CODE_TOO_MANY_REQUESTS = 429;
    const MSG_429 = 'Too Many Requests';

    const CODE_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    const MSG_431 = 'Request Header Fields Too Large';

    const CODE_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    const MSG_451 = 'Unavailable For Legal Reasons';

    const CODE_INTERNAL_SERVER_ERROR = 500;
    const MSG_500 = 'Internal Server Error';

    const CODE_NOT_IMPLEMENTED = 501;
    const MSG_501 = 'Not Implemented';

    const CODE_BAD_GATEWAY = 502;
    const MSG_502 = 'Bad Gateway';

    const CODE_SERVICE_UNAVAILABLE = 503;
    const MSG_503 = 'Service Unavailable';

    const CODE_GATEWAY_TIME_OUT = 504;
    const MSG_504 = 'Gateway Time-out';

    const CODE_HTTP_VERSION_NOT_SUPPORTED = 505;
    const MSG_505 = 'HTTP Version not supported';

    const CODE_VARIANT_ALSO_NEGOTIATES = 506;
    const MSG_506 = 'Variant Also Negotiates';

    const CODE_INSUFFICIENT_STORAGE = 507;
    const MSG_507 = 'Insufficient Storage';

    const CODE_LOOP_DETECTED = 508;
    const MSG_508 = 'Loop Detected';

    const CODE_BANDWIDTH_LIMIT_EXCEEDED = 509;
    const MSG_509 = 'Bandwidth Limit Exceeded';

    const CODE_NOT_EXTENDED = 510;
    const MSG_510 = 'Not Extended';


    /**
     * @param string $name
     * @param string $value
     */
    public function header($name, $value);

    /**
     * @param mixed $data
     * @param array $headers
     * @return string
     */
    public function send($data, $headers = []);

    /**
     * @param int $code
     * @param string|null $message
     * @param string $httpVersion
     * @return void
     */
    public function status($code, $message = null, $httpVersion = '1.1');
}
