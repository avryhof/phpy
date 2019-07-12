<?php
require_once("stdlib.php");

class python_HttpResponse {
    var $status_code;
    var $text;
    var $headers;
    var $raw;
    var $url;
    var $encoding;
    var $is_redirect;
    var $detail;

    function __construct($curl_handle, $curl_response)
    {
        $info = curl_getinfo($curl_handle);

        $header_size = $info['header_size'];
        $header = substr($curl_response, 0, $header_size);

//        echo $header;

        $this->raw = $curl_response;
        $this->status_code = $info['http_code'];
//        $this->headers = $info
        $this->text = substr($curl_response, $header_size);
        $this->url = $info['url'];
        $this->content_type = $info['content_type'];
        $this->is_redirect = !empty($info['redirect_url']);

        /* Return everything if it's not defined above */
        foreach($info as $key => $value){
            if (!$this->$key) {
                $this->{$key} = $value;
            }
        }
    }
}


class python_requests
{
    var $curlerror = None;
    var $url = None;
    var $curl_options = array();

    var $follow_location = False;
    var $max_redir = False;

    var $ssl_verify = True;
    var $ssl_verify_status = True;

    function __construct($kwargs = [])
    {
        $this->ssl_verify = $kwargs->get('ssl_verify');
        $this->ssl_verify_status = $kwargs->get('ssl_verify_status');

        $this->curl_options = array(
            CURLOPT_URL => $this->url,
            CURLOPT_HEADER => True,
            CURLOPT_RETURNTRANSFER => True,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_SSL_VERIFYPEER => $this->ssl_verify,
            CURLOPT_SSL_VERIFYSTATUS => $this->ssl_verify_status,
            CURLOPT_VERBOSE => False
        );
    }

    function request($options, $defaults) {
        $request_opts = $options + $defaults;
        if ($request_opts[CURLOPT_URL] == None) {
            $request_opts[CURLOPT_URL] = $this->url;
        }

        if ($this->follow_location) {
            $request_opts[CURLOPT_FOLLOWLOCATION] = $this->follow_location;
            $request_opts[CURLOPT_MAXREDIRS] = $this->max_redir;
        }

        $ch = curl_init();

        curl_setopt_array($ch, $request_opts);

        if (!$resp = curl_exec($ch)) {
            $this->curlerror = curl_error($ch);
            trigger_error(curl_error($ch));
        } else {
            $response = new python_HttpResponse($ch, $resp);
        }

        curl_close($ch);

        return $response;
    }

    function get($url, array $params = NULL, array $options = array())
    {
        if ($params) {
            $this->url = $url . (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($params);
        } else {
            $this->url = $url;
        }

        $defaults = array(
            CURLOPT_URL => $this->url,
            CURLOPT_HEADER => True,
            CURLOPT_HTTPGET => True,

        ) + $this->curl_options;

        return $this->request($options, $defaults);
    }

    function post($url, array $params = NULL, array $options = array())
    {
        $this->url = $url . (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($params);
        $defaults = array(
            CURLOPT_POST => True,
            CURLOPT_POSTFIELDS => http_build_query($params)
        )  + $this->curl_options;

        return $this->request($options, $defaults);
    }
}