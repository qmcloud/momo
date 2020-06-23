<?php

namespace QCloud\Cos;

class HttpRequest {
    public $timeoutMs;        // int: the maximum number of milliseconds to perform this request.
    public $url;              // string: the url this request will be sent to.
    public $method;           // string: POST or GET.
    public $customHeaders;    // array: custom modified, removed and added headers.
    public $dataToPost;       // array: the data to post.
    public $userData;         // any: user custom data.
}