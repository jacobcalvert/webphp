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
 * @file   handler_include.php
 * @Author Jacob Calvert (jacob+info@jacobncalvert.com)
 * @date   December, 2014
 * @brief  Includes all the files under the /handler/* folder 
 */
include_once 'classes/WebRequest.class.php';
include_once 'classes/StaticFileHandler.class.php';
/*
 * include all the handler defs from the handlers folder, or define the handlers here!
 */
foreach (glob("handlers/*.php") as $filename)
{
    include_once $filename;
}


