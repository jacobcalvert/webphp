<?php
 /* Copyright 2014 Jacob Calvert.
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
 */

class ExampleHandler extends webphp\WebRequest
{
    /*
     * example handler for webphp
     */
    public function get()
    {
        $this->add_header("Content-Type", "application/json");
        $this->write(json_encode($this->params, JSON_UNESCAPED_SLASHES)); 
    }
    
    public function post()
    {
        $this->add_header("Content-Type", "application/json");
        $this->set_response_code(201); // 201 is the "CREATED" response -- could be used for REST impl's
        $this->write(json_encode($this->params, JSON_UNESCAPED_SLASHES)); 
    }
    
    public function put()
    {
        $this->add_header("Content-Type", "application/json");
        $this->write(json_encode($this->params, JSON_UNESCAPED_SLASHES)); 
    }
    
    public function delete()
    {
        $this->set_response_code(403);
        $this->write(NULL);        
    }
    
    public function head()
    {
        // just an example of adding a custom header
        // the HEAD verb expects just the headers back about 
        // the requested document
        // or in other words, HEAD wants a GET response without the body
        $this->add_header("Custom-Header", "custom-value");
        $this->write(NULL);
    }
}

class AlwaysSSLHandler extends webphp\WebRequest
{
    public function pre_init()
    {
        $this->params["require_ssl"] = true; // setting this will require all urls handled by this handler to use ssl
    }
    public function get()
    {
        $this->add_header("Content-Type", "application/json");
        $this->write(json_encode($this->params, JSON_UNESCAPED_SLASHES)); 
    }
    
}