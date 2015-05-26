<?php

/*
 * PHP Client for Pop Up Archive API
 * Copyright 2015 Pop Up Archive
 * Licensed under the Apache 2 terms -- see LICENSE
 */

class PopUpArchive_Client {

    private $access_token;
    private $host;
    private $version = '1.0.0';
    private $user_agent = 'popuparchive-client-php';

    /**
     *
     *
     * @param unknown $args (optional)
     */
    public function __construct($args=array()) {
        $client_key = isset($args['id'])
            ? $args['id']
            : (
            isset($args['key'])
            ? $args['key']
            : getenv('PUA_ID')
        );
        $client_secret = isset($args['secret'])
            ? $args['secret']
            : getenv('PUA_SECRET');
        $this->host = isset($args['host'])
            ? $args['host']
            : (
            getenv('PUA_HOST')
            ? getenv('PUA_HOST')
            : 'https://www.popuparchive.com'
        );

        if (!$client_key or !$client_secret) {
            throw new Exception("Must define client key and secret");
        }

        // get auth token
        $signature = base64_encode("$client_key:$client_secret");
        $auth_url = $this->host . '/oauth/token';
        $params = array('grant_type' => 'client_credentials');
        $resp = Requests::post($auth_url, array('Authorization' => "Basic $signature"), $params);
        $resp_json = json_decode($resp->body);
        $this->access_token = $resp_json->access_token;

        // create persistent agent for convenience
        $this->agent = new Requests_Session($this->host);
        $this->agent->useragent = $this->user_agent . '/' . $this->version;
        $this->agent->headers['Authorization'] = "Bearer " . $this->access_token;

    }


    /**
     *
     *
     * @param string  $path
     * @param array   $params (optional)
     * @return object
     */
    public function get($path, $params=false) {
        $uri = sprintf("%s/api/%s", $this->host, $path);
        if ($params) {
            $uri .= '?' . http_build_query($params);
        }
        $resp = $this->agent->get($uri);
        return json_decode($resp->body);
    }


    /**
     *
     *
     * @param string  $path
     * @param array $payload
     * @return $response object
     */
    public function post($path, $payload) {
        $uri = sprintf("%s/api/%s", $this->host, $path);
        $resp = $this->agent->post($uri, array('Content-Type'=>'application/json'), json_encode($payload));
        return json_decode($resp->body);
    }


    /**
     *
     *
     * @return $collections array
     */
    public function get_collections() {
        return $this->get('/collections')->collections;
    }


    /**
     *
     *
     * @param unknown $coll_id
     * @return $collection object
     */
    public function get_collection($coll_id) {
        return $this->get("/collections/$coll_id");
    }


    /**
     *
     *
     * @param unknown $coll_id
     * @param unknown $item_id
     * @return $item object
     */
    public function get_item($coll_id, $item_id) {
        return $this->get("/collections/$coll_id/items/$item_id");
    }


    /**
     * Search Items.
     *
     * @param array   $params
     * @return object
     */
    public function search($params) {
        return $this->get("/search", $params);
    }


    /**
     *
     *
     * @param unknown $coll_id
     * @param unknown $item
     * @return unknown
     */
    public function create_item($coll_id, $item) {
        return $this->post("/collections/$coll_id/items", $item);
    }


    /**
     *
     *
     * @param unknown $item_id
     * @param unknown $audio
     * @return unknown
     */
    public function create_audio_file($item_id, $audio) {
        return $this->post("/items/$item_id/audio_files", array("audio_file" => $audio));
    }


}
