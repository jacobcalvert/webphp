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
            $this->write("");
        }
        else
        {
            $finfo = finfo_open();
            $mime = finfo_file($finfo, $path, FILEINFO_MIME);
            $this->add_header("Content-Length", filesize($path));
            $this->add_header("Content-Type", $mime);
            $this->add_header("Accept-Ranges", "bytes");
            $this->write(NULL);
        }
    }
    public function get()
    {
        $range = $this->get_range();
        $path = $this->file_path();
        if(!$path)
        {
            $this->set_response_code(404);
            $this->write("");
        }
        else
        {
            $file_len = filesize($path);
            $this->add_header("Content-Length", $file_len);
            $finfo = finfo_open();
            $mime = finfo_file($finfo, $path, FILEINFO_MIME);
            
            $handle = fopen($path, 'rb');
            $start = $range["range_begin"];
            
            if($start >= $file_len)
            {
                $this->write("");
            }
            fseek($handle, $start);
            $read_len = ($range["range_end"]-$start < $file_len-$start)?$range["range_end"]-$start:$file_len-$start;
            if($start + $read_len < $file_len)
            {
                $this->set_response_code(206); //partial file
            }
            $this->add_header("Content-Type", $mime);
            $this->add_header("Content-Range:", "$start-".($start+$read_len)."/$file_len");
            
            $buffer = 1024*8;
            while(!feof($handle) && (($p = ftell($handle))) <= ($start+$read_len))
            {
                if ($p + $buffer > $start+$read_len) 
                {
                    $buffer = $start+$read_len - $p + 1;
                }
                $this->write(fread($handle, $buffer));
                flush();
            }
                
           
            
        }
    }
    
    private function get_range()
    {
        $parts2 = NULL;
        if(array_key_exists("HTTP_RANGE", $this->request_headers))
        {
            $range_str = $this->request_headers["HTTP_RANGE"];
            $parts1 = explode("=", $range_str);
            $parts2 = explode("-", $parts1[1]);
            if($parts2[1] == "")
            {
                $parts2[1] = filesize($this->file_path());
            }
        }
        else
        {
           $parts2[0] = 0;
           $parts2[1] = filesize($this->file_path());
        }
        
        return array("range_begin"=> (int) $parts2[0], "range_end"=> (int) $parts2[1]);
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
}