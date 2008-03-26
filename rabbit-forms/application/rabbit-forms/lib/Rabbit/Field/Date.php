<?php

/**
 * Rabbit Forms
 *
 * Copyright (c) 2008 Wilker Lúcio
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author   Wilker Lúcio da Silva
 * @version  $Id$
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0
 */

class Rabbit_Field_Date extends Rabbit_Field
{
    /**
     * @see Rabbit_Field::loadAssets()
     *
     */
    public function initialize()
    {
        $this->form->addAsset('jquery-1.2.3.pack.js');
        $this->form->addAsset('ui.datepicker.js');
        $this->form->addAsset('ui.datepicker.css');

        $lang = $this->getAttribute('lang', '');

        if($lang) {
            $this->form->addAsset('ui.datepicker-' . $lang . '.js');
        }

        $options = $this->getAttribute('options', array());

        if(!isset($options['dateFormat'])) {
            $options['dateFormat'] = $this->getAttribute('display_format', 'yy-mm-dd');
        }

        $options = json_encode($options);

        $this->form->addClientExec('
            $(document).ready(function() {
                $("#' . $this->getName() . '").datepicker(' . $options . ');
            });
        ');
    }

    /**
     * Parse a date and get timestamp based on format
     *
     * Formats accepted by this method
     *
     * d  - day of month (no leading zero)
     * dd - day of month (two digit)
     * m  - month of year (no leading zero)
     * mm - month of year (two digit)
     * y  - year (two digit)
     * yy - year (four digit)
     * '' - single quote
     * '...' - string literal
     *
     * @param string $format
     * @param string $value
     * @return integer
     */
    public function parseFormat($format, $value)
    {
        $day   = -1;
        $month = -1;
        $year  = -1;

        $iFormat = 0;
        $iValue  = 0;
        $length  = strlen($format);
        $literal = false;

        while($iFormat < $length) {
            if($literal) {
                if($format[$iFormat] == "'") {
                    $literal = false;
                    $iFormat++;
                    continue;
                }

                if($format[$iFormat] == $value[$iValue]) {
                    $iFormat++;
                    $iValue++;
                } else {
                    show_error('Date error: Unexpect literal at position ' . $iValue);
                }
            } else {
                switch($format[$iFormat]) {
                    case 'd':
                        if(@$format[$iFormat + 1] == 'd') {
                            $iFormat++;
                        }

                        $size = 2;
                        $num  = 0;

                        while($size > 0 && $iValue < strlen($value) && $value[$iValue] >= '0' && $value[$iValue] <= '9') {
                            $num = $num * 10 + $value[$iValue++];
                            $size--;
                        }

                        if($size == 2) {
                            show_error('Date error: Missing number at position ' . $iValue);
                        }

                        $day = $num;
                        $iFormat++;
                        break;
                    case 'm':
                        if(@$format[$iFormat + 1] == 'm') {
                            $iFormat++;
                        }

                        $size = 2;
                        $num  = 0;

                        while($size > 0 && $iValue < strlen($value) && $value[$iValue] >= '0' && $value[$iValue] <= '9') {
                            $num = $num * 10 + $value[$iValue++];
                            $size--;
                        }

                        if($size == 2) {
                            show_error('Date error: Missing number at position ' . $iValue);
                        }

                        $month = $num;
                        $iFormat++;
                        break;
                    case 'y':
                        if(@$format[$iFormat + 1] == 'y') {
                            $iFormat++;
                        }

                        $size = 4;
                        $num  = 0;

                        while($size > 0 && $iValue < strlen($value) && $value[$iValue] >= '0' && $value[$iValue] <= '9') {
                            $num = $num * 10 + $value[$iValue++];
                            $size--;
                        }

                        if($size == 4) {
                            show_error('Date error: Missing number at position ' . $iValue);
                        }

                        if($size > 1) {
                            if($num < 70) {
                                $year = 2000 + $num;
                            } else {
                                $year = 1900 + $num;
                            }
                        } else {
                            $year = $num;
                        }
                        $iFormat++;
                        break;
                    case "'":
                        if(@$format[$iFormat + 1] == "'") {
                            $iFormat += 2;
                            $iValue++;
                        } else {
                            $literal = true;
                            $iFormat++;
                        }

                        break;
                    default:
                        $iFormat++;
                        $iValue++;
                }
            }
        }

        if($day == -1 || $month == -1 || $year == -1) {
            show_error('Date error: Error while parsing date');
        }

        return mktime(0, 0, 0, $month, $day, $year);
    }

    /**
     * Create a formated string based on time and format
     *
     * Formats accepted by this method
     *
     * d  - day of month (no leading zero)
     * dd - day of month (two digit)
     * m  - month of year (no leading zero)
     * mm - month of year (two digit)
     * y  - year (two digit)
     * yy - year (four digit)
     * '' - single quote
     * '...' - string literal
     *
     * @param string $format
     * @param integer $time
     * @return string
     */
    public function formatTime($format, $time)
    {
        $iFormat = 0;
        $length  = strlen($format);
        $output  = '';
        $literal = false;

        while($iFormat < $length) {
            if($literal) {
                if($format[$iFormat] != "'") {
                    $output .= $format[$iFormat];
                } else {
                    $literal = false;
                }

                $iFormat++;
            } else {
                switch($format[$iFormat]) {
                    case 'd':
                        if(@$format[$iFormat + 1] == 'd') {
                            $output .= date('d', $time);
                            $iFormat++;
                        } else {
                            $output .= date('j', $time);
                        }

                        $iFormat++;
                        break;
                    case 'm':
                        if(@$format[$iFormat + 1] == 'm') {
                            $output .= date('m', $time);
                            $iFormat++;
                        } else {
                            $output .= date('n', $time);
                        }

                        $iFormat++;
                        break;
                    case 'y':
                        if(@$format[$iFormat + 1] == 'y') {
                            $output .= date('Y', $time);
                            $iFormat++;
                        } else {
                            $output .= date('y', $time);
                        }

                        $iFormat++;
                        break;
                    case "'":
                        if(@$format[$iFormat + 1] == "'") {
                            $iFormat += 2;
                            $output .= "'";
                        } else {
                            $literal = true;
                            $iFormat++;
                        }

                        break;
                    default:
                        $output .= $format[$iFormat];
                        $iFormat++;
                }
            }
        }

        return $output;
    }

    /**
     * @see Rabbit_Field::getValue()
     *
     * @return mixed
     */
    public function getValue()
    {
        $displayFormat = $this->getAttribute('display_format', null);
        $saveFormat    = $this->getAttribute('save_format', null);

        if($this->value && $displayFormat && $saveFormat) {
            $time = $this->parseFormat($saveFormat, $this->value);
            return $this->formatTime($displayFormat, $time);
        } else {
            return $this->value;
        }
    }

    /**
     * @see Rabbit_Field::setValue()
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $displayFormat = $this->getAttribute('display_format', null);
        $saveFormat    = $this->getAttribute('save_format', null);

        if($value && $displayFormat && $saveFormat) {
            $time = $this->parseFormat($displayFormat, $value);
            $this->value = $this->formatTime($saveFormat, $time);
        } else {
            $this->value = $value;
        }
    }

    /**
     * @see Rabbit_Field::getFieldHtml()
     *
     * @return string
     */
    public function getFieldHtml()
    {
        $ci =& get_instance();
        $ci->load->helper('rabbit');

        $attr['type']  = 'text';
        $attr['id']    = $this->getName();
        $attr['name']  = $this->getName();
        $attr['value'] = $this->getValue();
        $attr['class'] = $this->getAttribute('class', '');
        $attr['style'] = $this->getAttribute('style', '');

        return sprintf(
            '<input %s />',
            rabbit_attributes_build($attr)
        );
    }
}