<?php

/* 
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
 * @file   HTMLTemplate.class.php
 * @Author Jacob Calvert (jacob+info@jacobncalvert.com)
 * @date   December, 2014
 * @brief  Defines the base class for a template
 * 
 * This class is a VERY simple templating class. You can write an HTML file with
 * template parameters like {{tables.table1.title}} and that will be populated by a data 
 * array formatted by array("tables"=>array("table1"=>array("title"=>"some title")));
 * 
 * If you have a numerically indexed array you can iterate it by templating 
 * 
 * {{for user in users}}
 * 
 * <div>{{user.name}} - {{user.contactinfo.phone}}</div>
 * 
 * {{endfor}}
 * 
 * 
 * see the example file in the /templates/* directory for more example details
 */
namespace webphp;
class HTMLTemplate
{
    protected $template_path;
    protected $file_contents;
    /*
     * HTMLTemplate constructor
     * @param $absolute_template_path the file path of the template file
     */
    public function __construct($absolute_template_path)
    {
        $this->template_path = $absolute_template_path;
        $this->read_template();
    }
    /*
     * void function that tries to read the file specified by the constructor and 
     * loads it into memory
     */
    private function read_template()
    {
        $file_ptr = fopen($this->template_path, "r");
       if(!$file_ptr)
       {
           $this->file_contents = "Error reading template file.";
       }
       else
       {
           $this->file_contents = fread($file_ptr, filesize($this->template_path));
           fclose($file_ptr);
       }
    }
    /*
     * this function walks the provided data structure given a dot
     * separated string and returns the value at the specified
     * position string
     * @param $place_str a dotted string indicating a specific location in the structure
     * @param $var_array the data structure
     * @returns the value of $place_str in $var_array or false if not found
     */
    private function get_var($place_str, $var_array)
    {
        $keys = explode(".", $place_str);
        $i = 0; 
        $walk = $var_array;
        while($i < count($keys))
        {
            $key = $keys[$i];
            if(is_array($walk) && array_key_exists($key, $walk))
            {
                $walk = $walk[$key];
                $i++;
            }
            else
            {
                error_log("[webphp//HTMLTemplateClass] property '$place_str' was not found");
                return false;
            }
           
        }
        
        return $walk;
    }
    /*
     * renders the foreach blocks in the template
     * @param $var_array the data array
     */
    private function render_foreach($var_array)
    {
        $regex = "|\{\{\s*for (.+) in (.+)\s*\}\}|";
        $matches = array();
        $num = preg_match_all($regex,$this->file_contents, $matches);
        
        
        for($i = 0; $i < $num; $i++)
        {
            $match_text = $matches[0][$i];
            $match_key = $matches[1][$i];
            $match_parent = $matches[2][$i];
            
            $st = strpos($this->file_contents,$match_text);
            
            $end = strpos( $this->file_contents,"{{endfor}}");
            
            
            
            
            $html = substr($this->file_contents, $st, ($end + strlen("{{endfor}}"))- $st);
            
            $html = str_replace($match_text,"", $html);
            $html = str_replace("{{endfor}}","", $html);
            
            $output = "";
            
            $parent_array = $this->get_var($match_parent, $var_array);
            
            for($j = 0; $j < count($parent_array); $j++)
            {
                $submatches = array();
                $subnum = preg_match_all("|\{\{(.+)\}\}|", $html, $submatches);
                $result = $html;
                for($k = 0; $k < $subnum; $k++)
                {
                    $key = str_replace("$match_key.", "", $submatches[1][$k]);
                    
                    $the_key = "$match_parent.$j.$key";
                    
                    $val = $this->get_var($the_key, $var_array);
                    
                    
                   
                    
                    $result = str_replace($submatches[0][$k], $val , $result);
                    
                    
                    
                    
                    
                }
                
                $output.= $result;
                    
            }
            $this->file_contents = substr($this->file_contents, 0, $st).$output.substr($this->file_contents, $end+strlen("{{endfor}}"));
            
            
            
            
        }
        
    }
    /*
     * public facing render call
     * @param $var_array the data array
     * @returns a string of the rendered HTML. 
     */
    public function render($var_array)
    {
     
        $this->render_foreach($var_array);
        
        $matches = array();
        $num = preg_match_all("|\{\{(.+)\}\}|", $this->file_contents, $matches);
        for($i = 0; $i < $num; $i++)
        {
            $r_key = $matches[0][$i];
            $m_key = $matches[1][$i];
            $val = $this->get_var($m_key, $var_array);
            
            $this->file_contents = str_replace($r_key, $val, $this->file_contents);
        }
        
        return $this->file_contents;
    }
}


