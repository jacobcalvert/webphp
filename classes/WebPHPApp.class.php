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
 *  
 * @file   app.php
 * @Author Jacob Calvert (jacob+info@jacobncalvert.com)
 * @date   December, 2014
 * @brief  Implementation of the main webphp application
 *
 * The main implemetation resides here.
 */
namespace webphp;
include_once 'classes/WebRequest.class.php';

class WebPHPApp
{
    private $url, $maps, $url_params;
    /*
     * constructs the webphp_app object
     * @param $url_maps an array of UrlMap objects that defines your routes
     * @param $url the currently requested url
     * @param $url_params an optional array of key=>value pairs that represents request options from GET, POST, etc..
     */
    public function __construct($url_maps, $url, $url_params=array())
    {
        $this->url = $url;
        $this->maps = $url_maps;
        $this->url_params = $url_params;
    }
    /*
     * the main loop of the app
     * We first check the url against each map and select the url with the highest
     * match value. Then we setup the neccessary parameters, decide what HTTP verb 
     * was used, and call the appropriate handler.
     */
    public function run()
    {
        
        $method = $_SERVER['REQUEST_METHOD'];
        
        $request_headers = array();
        foreach($_SERVER as $k => $v)
        {
          if (strpos($k, 'HTTP_') !== false)
          {
            $request_headers[$k] = $v;
          }
        }
        
        $match_level = -1;
        $match_class = NULL;
        $match_obj = NULL;
        foreach($this->maps as $url_map)
        {
            if($url_map->match_level($this->url) >= $match_level)
            {
                $match_obj = $url_map;
                $match_level = $url_map->match_level($this->url);
                $match_class = $url_map->get_handler_class();
            }
        }
        
        $web_request_obj = NULL;
        
        if($match_class == NULL || $match_level == -1)
        {
            //error no match
            $web_request_obj = new WebRequest($request_headers);
            $web_request_obj->set_response_code(500);
            $web_request_obj->write(NULL);
            return;
        }
        else
        {
            $params = $match_obj->parse_params($this->url);
            $params["url_map"] = $match_obj->get_url_map();
            $web_request_obj = new $match_class($request_headers, array_merge($params, $this->url_params));
        }
        
        if($web_request_obj->require_ssl() && !$this->is_ssl())
        {
            $web_request_obj->set_response_code(501);
            $web_request_obj->write("Connection must be over SSL.");
            return;
        }
        
        switch($method)
        {
            case "GET":         $web_request_obj->get();break;
            case "HEAD":        $web_request_obj->head();break;
            case "POST":        $web_request_obj->post();break;
            case "PUT":         $web_request_obj->put();break;
            case "DELETE":      $web_request_obj->delete();break;
            case "OPTIONS":     $web_request_obj->options();break;
            case "CONNECT":     $web_request_obj->connect();break;
            case "TRACE":       $web_request_obj->trace();break;
            default:
            {
                $web_request_obj->set_response_code(501);
                $web_request_obj->write("Unknown request method.");
                break;   
            }
        }
        
        
    }
        /*
     * returns if the connection is over https or not
     * @return boolean is the connection https or not
     */
    final protected function is_ssl() 
    {
        if ( isset($_SERVER['HTTPS']) ) 
        {
            // check two ways if the SERVER superglobal reports HTTPS
            if (strtolower($_SERVER['HTTPS']) == "on")
            {
               return true; 
            }
                
            if ($_SERVER['HTTPS'] == '1')
            {
              return true;  
            }
                
        } 
        //otherwise, check the port... this could be a bad way, but it works
        elseif (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == '443'))
        {
            return true;
        }
        
        return false;
    }
}
