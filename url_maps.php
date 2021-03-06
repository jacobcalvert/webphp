<?php
/**
 *  Copyright 2014 Jacob Calvert.
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
 * @file   url_maps.php
 * @Author Jacob Calvert (jacob+info@jacobncalvert.com)
 * @date   December, 2014
 * @brief  File containing the url maps
 */
include_once 'classes/UrlMap.class.php';

/*
 * declare your url maps here
 */

$url_maps = array
(
    new webphp\UrlMap("/?", array(), 'ExampleHandler', array('require_ssl'=>true)),
    
    new webphp\UrlMap("/subdir/(.+)/?", array("the_variable"),'ExampleHandler', array("subdir"=>true)),
    new webphp\UrlMap("/always_ssl/(.+)/?", array("the_variable"), 'AlwaysSSLHandler', array()),
    new webphp\UrlMap("/template/(.+)/?", array("the_variable"), 'TemplateExampleHandler', array()),
    new webphp\UrlMap("/static/(.+)", array(), "StaticFileHandler", array("root"=>dirname(__FILE__).'/static/'))
);