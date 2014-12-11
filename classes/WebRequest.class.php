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
 * @file   WebRequest.class.php
 * @Author Jacob Calvert (jacob+info@jacobncalvert.com)
 * @date   December, 2014
 * @brief  Defines the base class for a webrequest
 * 
 * This class defines all the methods that are expected to exists from
 * the apps perspective. To use this class, extend it and override the 
 * methods that you want to implement. Unimplemented methods will return 
 * a 501 error.
 */
namespace webphp;
class WebRequest
{
    protected $resp_code = 200;
    protected $extra_headers = array();
    protected $request_headers = array();
    protected $params = array();
    /*
     * WebRequest constructor
     * @param $request_headers the HTTP request headers
     * @param $params optional parameters
     */
    public function __construct($request_headers, $params = array())
    {
        $this->request_headers = $request_headers;
        $this->params = $params;
    }
    /*
     * the handler for a GET request
     */ 
    public function get()
    {
        $this->not_supported_response();
    } 
    /*
     * the handler for a POST request
     */    
    public function post()
    {
        $this->not_supported_response();
    }
    /*
     * the handler for a DELETE request
     */
    public function delete()
    {
        $this->not_supported_response();
    }
    /*
     * the handler for a PUT request
     */
    public function put()
    {
        $this->not_supported_response();
    }
    /*
     * the handler for an OPTIONS request
     */
    public function options()
    {
        $this->not_supported_response();
    }
    /*
     * the handler for a HEAD request
     */
    public function head()
    {
        $this->not_supported_response();
    }
    /*
     * the handler for a CONNECT request
     */
    public function connect()
    {
        $this->not_supported_response();
    }
    /*
     * the handler for a TRACE request
     */
    public function trace()
    {
        $this->not_supported_response();
    }
    /*
     * writes a message and sets the response code to 501 to inform
     * the client that this method is not supported
     * @param $msg a default message, can be changed.
     */
    protected function not_supported_response($msg="This method is not supported.")
    {
        $this->set_response_code(501);
        $this->write($msg);        
    }

    /*
     * sets the HTTP response code
     * @param $resp_code an int represeting the response code (i.e 206, 404, 302, etc)
     */
    public function set_response_code($resp_code)
    {
        $this->resp_code = $resp_code;
    }
    /*
     * a function to add a header to the response to the client
     * @param $header_key the header key (i.e 'Content-Length')
     * @param $header_val the header value (i.e 1244) 
     */
    public function add_header($header_key, $header_val)
    {
        $this->extra_headers[$header_key] = $header_val;
    }
    /*
     * write data to the client, ends this request
     * this function assembles the extra headers and writes the data to the client
     * @param $data the data to be writter
     */
    public function write($data)
    {
        http_response_code($this->resp_code);
        foreach($this->extra_headers as $k=>$v)
        {
            header($k.": ".$v);
        }
        echo $data;
    }
    
}