<?php

/**
 * Copyright 2014 Jacob Calvert.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * @file   server.php
 * @Author Jacob Calvert (jacob+info@jacobncalvert.com)
 * @date   December, 2014
 * @brief  Sets up the webphp_app and executes the request.
 * 
 * This file does a little magic on the url and then executes the app
 */
include_once 'classes/WebPHPApp.class.php'; // the main app
include_once 'handler_include.php'; // handler master include
include_once 'url_maps.php'; // your url maps

$url = $_GET["url"]; // get the url from the request
$url_string = $url;
$strip_last = true;
if(strpos($url, ".")) //is a file request
{
    //do nothing!
    $strip_last=false;
}
else if(substr($url, -1) != "/")//let's do some tweaking...
{
    $url_string.="/?";
}
else
{
    $url_string.="?";
}

$url_params = array();
$_PUT = array();
parse_str(file_get_contents('php://input'), $_PUT);
foreach(array_merge($_GET, $_POST, $_REQUEST, $_PUT) as $k=>$v) // get ALLLLLL the variables
{
    if($k != "url")
    {
        $url_string.=$k."=".$v."&";
        $url_params[$k] = $v; 
    }
}
if($strip_last)
{
    $url_string = substr($url_string, 0, strlen($url_string)-1); // fix the trailing &
}
$url_params["url_request_string"] = $url_string;

$app = new webphp\WebPHPApp($url_maps,$url, $url_params); // construct the app

$app->run(); // run it!