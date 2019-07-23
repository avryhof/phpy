<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . "stdlib.php");

class python_HttpResponse {
    var $status_code;
    var $content_type;
    var $text;
    var $headers;
    var $raw;
    var $url;
    var $encoding;
    var $is_redirect;
    var $detail;
    var $http_code;
    var $header_size;
    var $request_size;
    var $filetime;
    var $ssl_verify_result;
    var $redirect_count;
    var $total_time;
    var $namelookup_time;
    var $connect_time;
    var $pretransfer_time;
    var $size_upload;
    var $size_download;
    var $speed_download;
    var $speed_upload;
    var $download_content_length;
    var $upload_content_length;
    var $starttransfer_time;
    var $redirect_time;
    var $redirect_url;
    var $primary_ip;
    var $certinfo;
    var $primary_port;
    var $local_ip;
    var $local_port;

    function __construct($curl_handle, $curl_response)
    {
        $info = curl_getinfo($curl_handle);

        $header_size = $info['header_size'];
        $this->raw = $curl_response;
        $this->status_code = $info['http_code'];
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

    var $follow_location = true;
    var $max_redir = 1;

    var $ssl_verify = true;
    var $ssl_verify_status = true;

    function __construct($keyword_args = [])
    {
        if (gettype($keyword_args) == 'array') {
            $kwargs = new dict($keyword_args);
        } else {
            $kwargs = $keyword_args;
        }

        $this->ssl_verify = $kwargs->get('ssl_verify');
        $this->ssl_verify_status = $kwargs->get('ssl_verify_status');

        if (gettype($keyword_args) == 'array') {
            foreach($keyword_args as $keyword => $kwval) {
                $this->{$keyword} = $kwval;
            }
        }

        $this->curl_options = array(
            CURLOPT_URL => $this->url,
            CURLOPT_HEADER => True,
            CURLOPT_RETURNTRANSFER => True,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_SSL_VERIFYPEER => $this->ssl_verify,
            CURLOPT_SSL_VERIFYSTATUS => $this->ssl_verify_status,
            CURLOPT_VERBOSE => False,
        );
    }

    function request($options, $defaults) {
        $response = null;

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