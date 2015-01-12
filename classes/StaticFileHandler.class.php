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
 * @file   StaticFileHandler.class.php
 * @Author Jacob Calvert (jacob+info@jacobncalvert.com)
 * @date   December, 2014
 * @brief  defines a static file handler
 * 
 * This class is a static file handler.
 * Configuration expects a static parameter with key = "root" and
 * value = "/the/local/filesystem/path/root/"
 * 
 * For example if you have files that reside in /var/www/static/* location, but you want to map that
 * to http://your-domain.com/s/* your UrlMap for this class would look like:
 * 
 * new webphp\UrlMap("/s/(.+)", array(), 'StaticFileHandler', array("root"=>"/var/www/test_html/static/"))
 * 
 */
include_once 'WebRequest.class.php';

class StaticFileHandler extends webphp\WebRequest
{
    public function head()
    {
        $path = $this->file_path();
        if(!$path)
        {
            $this->set_response_code(404);
            $this->write(NULL);
        }
        else
        {
            $meta = $this->get_file_meta();
            $this->add_header("Content-Type", $meta["mime"]);
            $this->add_header("Content-Length", $meta["size"]);
            $this->add_header("Accept-Ranges", "bytes");
            
            $this->write(NULL);
            
        }
    }
    public function get()
    {
        $path = $this->file_path();
        if(!$path)
        {
            $this->set_response_code(404);
            $this->write(NULL);
        }
        else
        {
            $meta = $this->get_file_meta();
            $range = $this->get_range();
            $this->add_header("Content-Type", $meta["mime"]);
            $this->add_header("Accept-Ranges", "bytes");
            
            if($range[0] > $meta["size"] || $range[1] > $meta["size"])
            {
                $this->set_response_code(416); //not satisfiable
                $this->write(NULL);
                exit;
            }
            else
            {
                $bytes_start = $range[0];
                $bytes_end = $range[1];
                $content_length = $bytes_end - $bytes_start ;
                $this->add_header("Content-Length", $content_length);
                $this->add_header("Content-Range", "bytes $bytes_start-$bytes_end/".$meta['size']);
                
                if($bytes_end != $meta["size"])
                {
                    $this->set_response_code(206); //partial
                }
                
                $file_ptr = fopen($path, "rb");
               
                
                fseek($file_ptr, $bytes_start);
                
                $buffer = 1024 * 8;
                $this->send_headers();
                $p = ftell($file_ptr);
                
                
                while(!feof($file_ptr) && $p <= $bytes_end) 
                {
                    if ($p + $buffer > $bytes_end)
                    {
                        
                            $buffer = $bytes_end - $p + 1;
                    }
                    set_time_limit(0);
                    $this->raw_write( fread($file_ptr, $buffer));
                    flush();
                    $p = ftell($file_ptr);
                }
                
                fclose($file_ptr);
                
                
            }
             
        }
    }
    
    private function get_range()
    {
        $return = NULL;
        $meta = $this->get_file_meta();
        if(array_key_exists("HTTP_RANGE", $this->request_headers))
        {
            
            $range = explode("=", $this->request_headers['HTTP_RANGE']);
            $range_parts = explode("-", $range[1]);
            
            if(substr($range[1], -1) != "-")
            {
                $range_parts[0] = (int) $range_parts[0];
                $range_parts[1] = (int) $range_parts[1];
            }
            else
            {
                
                $range_parts[0] = (int) substr($range[1],0, strlen($range[1])-1);
                $range_parts[1] = $meta["size"]; //min(array($meta["size"], ($range_parts[0] + 1048576)));
                               
            }
            
        }
        else
        {
            $range_parts = array(0=>0, 1=>$meta["size"]);
        }
        $return = $range_parts;
        
        return $return;
    }
    
    private function file_path()
    {
        $pos = strrpos($this->params["url_map"], "/", -1);
        
        $fp = substr($this->params["url"], $pos);
        
        return realpath($this->params["root"]."/".$fp);
    }
    
    private function file_exists()
    {
        return $this->file_path();
        
    }
    
    private function get_file_meta()
    {
        //file must exist for this to work
        $finfo = finfo_open(FILEINFO_MIME_TYPE); 
        
        $return = array
        (
            "size" => filesize($this->file_path()),
            "mime" => finfo_file($finfo, $this->file_path())
        );
        return $return;
    }
}