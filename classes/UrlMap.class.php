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
 * @file   UrlMap.class.php
 * @Author Jacob Calvert (jacob+info@jacobncalvert.com)
 * @date   December, 2014
 * @brief  defines the UrlMap class
 * 
 * The UrlMap class is a utility class that contains the regex matching scheme for
 * processing urls.
 */
namespace webphp;
class UrlMap
{
    private $map_url, $named_params, $handler_class, $static_params;
    
    /*
     * constructor
     * @param $url_map the regex-like string that defines the map
     * @param $named_paramters an array of string ids that match up with the extracted groups from the $url_map regex
     * @param $handler_class a string of the name of the handler class for this particular map
     * @param $static_parameters an array of key=>value pairs that will be passed to the handler class constructor
     */
    public function __construct($url_map, $named_parameters, $handler_class, $static_parameters)
    {
        $this->map_url = $url_map;
        $this->named_params = $named_parameters;
        $this->handler_class = $handler_class;
        $this->static_params = $static_parameters;
    }
    /*
     * returns the integer match_level of the given url
     * compares the $url param to the member variable $map_url and returns an
     * integer representing how well it matches. Higher ints means better match
     * @param $url the url to matched on
     * @return int the match level
     */
    public function match_level($url)
    {
        $num_groups = $this->num_match_groups($this->map_url);
        if($num_groups == 0)
        {
            $regex = "/".str_replace("/", "\/", $this->map_url)."/";
            $regex2 = "/".str_replace("/", "\/", $url)."/"; 

            if(preg_match($regex, $url) && preg_match($regex2, $this->map_url))
            {
              return 1000; //exact match
            }
           return -1;
        }
        else
        {
            $regex = "/".str_replace("/", "\/", $this->map_url)."/";
            $matches = array();
        
            preg_match($regex, $url, $matches);
        
            return count($matches);
        }
        

    }
    private function num_match_groups($regex)
    {
        $count1 = substr_count($regex, "(");
        $count2 = substr_count($regex, ")");
        
        if($count1 != $count2)
        {
            //error
        }
        return $count1;
    }
    
    /*
     * parses the params out of the given url
     * @param $url the url to parse
     * @returns array an array of key=>value pairs consisting of the parsed values and static parameters
     */
    public function parse_params($url)
    {
        $ret = array();
        $ret["url"] = $url;
        $regex = "/".str_replace("/", "\/", $this->map_url)."/";
        $matches = array();
        
        preg_match($regex, $url, $matches);
        
        
        for($i = 0; $i < count($this->named_params); $i++)
        {
            $ret[$this->named_params[$i]] = ($matches[$i+1]);
        }
        
        
        
        return array_merge($ret, $this->static_params);
    }
    
    /*
     * returns the handler class
     * @return string the handler class name
     */
    public function get_handler_class()
    {
        return $this->handler_class;
    }
    /*
     * returns the url_map
     * @return string the url_map
     */
    public function get_url_map()
    {
        return $this->map_url;
    }
    
   
}