<?php
/**
 * ----------------------------------------------------------------------
 *  
 * Copyright (c) 2006-2016 Khaled Al-Sham'aa.
 *  
 * http://www.ar-php.org
 *  
 * PHP Version 5 
 *  
 * ----------------------------------------------------------------------
 *  
 * LICENSE
 *
 * This program is open source product; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License (LGPL)
 * as published by the Free Software Foundation; either version 3
 * of the License, or (at your option) any later version.
 *  
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *  
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/lgpl.txt>.
 *  
 * ----------------------------------------------------------------------
 *  
 * Class Name: Arabic Glyphs is a simple class to render Arabic text
 *  
 * Filename:   Glyphs.php
 *  
 * Original    Author(s): Khaled Al-Sham'aa <khaled@ar-php.org>
 *  
 * Purpose:    This class takes Arabic text (encoded in Windows-1256 character 
 *             set) as input and performs Arabic glyph joining on it and outputs 
 *             a UTF-8 hexadecimals stream that is no longer logically arranged 
 *             but in a visual order which gives readable results when formatted 
 *             with a simple Unicode rendering just like GD and UFPDF libraries 
 *             that does not handle basic connecting glyphs of Arabic language 
 *             yet but simply outputs all stand alone glyphs in left-to-right 
 *             order.
 *              
 * ----------------------------------------------------------------------
 *  
 * Arabic Glyphs is class to render Arabic text
 *
 * PHP class to render Arabic text by performs Arabic glyph joining on it,
 * then output a UTF-8 hexadecimals stream gives readable results on PHP
 * libraries supports UTF-8.
 *
 * Example:
 * <code>
 *   include('./I18N/Arabic.php');
 *   $obj = new I18N_Arabic('Glyphs');
 *
 *   $text = $obj->utf8Glyphs($text);
 *      
 *   imagettftext($im, 20, 0, 200, 100, $black, $font, $text);
 * </code>
 *
 * @category  I18N 
 * @package   I18N_Arabic
 * @author    Khaled Al-Sham'aa <khaled@ar-php.org>
 * @copyright 2006-2016 Khaled Al-Sham'aa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */

/**
 * This PHP class render Arabic text by performs Arabic glyph joining on it
 *  
 * @category  I18N 
 * @package   I18N_Arabic
 * @author    Khaled Al-Sham'aa <khaled@ar-php.org>
 * @copyright 2006-2016 Khaled Al-Sham'aa
 *    
 * @license   LGPL <http://www.gnu.org/licenses/lgpl.txt>
 * @link      http://www.ar-php.org 
 */ 
use Illuminate\Support\Str;
class I18N_Arabic_Glyphs
{
    private $_glyphs   = null;
    private $_hex      = null;
    private $_prevLink = null;
    private $_nextLink = null;
    private $_vowel    = null;

    /**
     * Loads initialize values
     *
     * @ignore
     */         
    public function __construct()
    {
        $this->_prevLink  = '،؟؛ـئبتثجحخسشصضطظعغفقكلمنهي';
        $this->_nextLink  = 'ـآأؤإائبةتثجحخدذرز';
        $this->_nextLink .= 'سشصضطظعغفقكلمنهوىي';
        $this->_vowel     = 'ًٌٍَُِّْ';

        /*
         $this->_glyphs['ً']  = array('FE70','FE71');
         $this->_glyphs['ٌ']  = array('FE72','FE72');
         $this->_glyphs['ٍ']  = array('FE74','FE74');
         $this->_glyphs['َ']  = array('FE76','FE77');
         $this->_glyphs['ُ']  = array('FE78','FE79');
         $this->_glyphs['ِ']  = array('FE7A','FE7B');
         $this->_glyphs['ّ']  = array('FE7C','FE7D');
         $this->_glyphs['ْ']  = array('FE7E','FE7E');
         */
         
        $this->_glyphs = 'ًٌٍَُِّْٰ';
        $this->_hex    = '064B064B064B064B064C064C064C064C064D064D064D064D064E064E';
        $this->_hex   .= '064E064E064F064F064F064F06500650065006500651065106510651';
        $this->_hex   .= '06520652065206520670067006700670';

        $this->_glyphs .= 'ءآأؤإئاب';
        $this->_hex    .= 'FE80FE80FE80FE80FE81FE82FE81FE82FE83FE84FE83FE84FE85FE86';
        $this->_hex    .= 'FE85FE86FE87FE88FE87FE88FE89FE8AFE8BFE8CFE8DFE8EFE8DFE8E';
        $this->_hex    .= 'FE8FFE90FE91FE92';

        $this->_glyphs .= 'ةتثجحخدذ';
        $this->_hex    .= 'FE93FE94FE93FE94FE95FE96FE97FE98FE99FE9AFE9BFE9CFE9DFE9E';
        $this->_hex    .= 'FE9FFEA0FEA1FEA2FEA3FEA4FEA5FEA6FEA7FEA8FEA9FEAAFEA9FEAA';
        $this->_hex    .= 'FEABFEACFEABFEAC';

        $this->_glyphs .= 'رزسشصضطظ';
        $this->_hex    .= 'FEADFEAEFEADFEAEFEAFFEB0FEAFFEB0FEB1FEB2FEB3FEB4FEB5FEB6';
        $this->_hex    .= 'FEB7FEB8FEB9FEBAFEBBFEBCFEBDFEBEFEBFFEC0FEC1FEC2FEC3FEC4';
        $this->_hex    .= 'FEC5FEC6FEC7FEC8';

        $this->_glyphs .= 'عغفقكلمن';
        $this->_hex    .= 'FEC9FECAFECBFECCFECDFECEFECFFED0FED1FED2FED3FED4FED5FED6';
        $this->_hex    .= 'FED7FED8FED9FEDAFEDBFEDCFEDDFEDEFEDFFEE0FEE1FEE2FEE3FEE4';
        $this->_hex    .= 'FEE5FEE6FEE7FEE8';

        $this->_glyphs .= 'هوىيـ،؟؛';
        $this->_hex    .= 'FEE9FEEAFEEBFEECFEEDFEEEFEEDFEEEFEEFFEF0FEEFFEF0FEF1FEF2';
        $this->_hex    .= 'FEF3FEF40640064006400640060C060C060C060C061F061F061F061F';
        $this->_hex    .= '061B061B061B061B';

        // Support the extra 4 Persian letters (p), (ch), (zh) and (g)
        // This needs value in getGlyphs function to be 52 instead of 48
        // $this->_glyphs .= chr(129).chr(141).chr(142).chr(144);
        // $this->_hex    .= 'FB56FB57FB58FB59FB7AFB7BFB7CFB7DFB8AFB8BFB8AFB8BFB92';
        // $this->_hex    .= 'FB93FB94FB95';
        //
        // $this->_prevLink .= chr(129).chr(141).chr(142).chr(144);
        // $this->_nextLink .= chr(129).chr(141).chr(142).chr(144);
        //
        // Example:     $text = 'نمونة قلم: لاگچ ژافپ';
        // Email Yossi Beck <yosbeck@gmail.com> ask him to save that example
        // string using ANSI encoding in Notepad
        $this->_glyphs .= '';
        $this->_hex    .= '';
        
        $this->_glyphs .= 'لآلألإلا';
        $this->_hex    .= 'FEF5FEF6FEF5FEF6FEF7FEF8FEF7FEF8FEF9FEFAFEF9FEFAFEFBFEFC';
        $this->_hex    .= 'FEFBFEFC';
    }
    
    /**
     * Get glyphs
     * 
     * @param string  $char Char
     * @param integer $type Type
     * 
     * @return string
     */                                  
    protected function getGlyphs($char, $type)
    {

        $pos = mb_strpos($this->_glyphs, $char);
        
        if ($pos > 49) {
            $pos = ($pos-49)/2 + 49;
        }
        
        $pos = $pos*16 + $type*4;
        
        return substr($this->_hex, $pos, 4);
    }
    
    /**
     * Convert Arabic Windows-1256 charset string into glyph joining in UTF-8 
     * hexadecimals stream
     *      
     * @param string $str Arabic string in Windows-1256 charset
     *      
     * @return string Arabic glyph joining in UTF-8 hexadecimals stream
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    protected function preConvert($str)
    {
        $crntChar = null;
        $prevChar = null;
        $nextChar = null;
        $output   = '';
        
        $_temp = mb_strlen($str);

        for ($i = 0; $i < $_temp; $i++) {
            $chars[] = mb_substr($str, $i, 1);
        }

        $max = count($chars);

        for ($i = $max - 1; $i >= 0; $i--) {
            $crntChar = $chars[$i];
            $prevChar = ' ';
            
            if ($i > 0) {
                $prevChar = $chars[$i - 1];
            }
            
            if ($prevChar && mb_strpos($this->_vowel, $prevChar) !== false) {
                $prevChar = $chars[$i - 2];
                if ($prevChar && mb_strpos($this->_vowel, $prevChar) !== false) {
                    $prevChar = $chars[$i - 3];
                }
            }
            
            $Reversed    = false;
            $flip_arr    = ')]>}';
            $ReversedChr = '([<{';
            
            if ($crntChar && mb_strpos($flip_arr, $crntChar) !== false) {
                $crntChar = $ReversedChr[mb_strpos($flip_arr, $crntChar)];
                $Reversed = true;
            } else {
                $Reversed = false;
            }
            
            if ($crntChar && !$Reversed 
                && (mb_strpos($ReversedChr, $crntChar) !== false)
            ) {
                $crntChar = $flip_arr[mb_strpos($ReversedChr, $crntChar)];
            }
            
            if (ord($crntChar) < 128) {
                $output  .= $crntChar;
                $nextChar = $crntChar;
                continue;
            }
            
            if ($crntChar == 'ل' && isset($chars[$i + 1]) 
                && (mb_strpos('آأإا', $chars[$i + 1]) !== false)
            ) {
                continue;
            }
            
            if ($crntChar && mb_strpos($this->_vowel, $crntChar) !== false) {
                if (isset($chars[$i + 1]) 
                    && (mb_strpos($this->_nextLink, $chars[$i + 1]) !== false) 
                    && (mb_strpos($this->_prevLink, $prevChar) !== false)
                ) {
                    $output .= '&#x' . $this->getGlyphs($crntChar, 1) . ';';
                } else {
                    $output .= '&#x' . $this->getGlyphs($crntChar, 0) . ';';
                }
                continue;
            }
            
            $form = 0;
            
            if (($prevChar == 'لا' || $prevChar == 'لآ' || $prevChar == 'لأ' 
                || $prevChar == 'لإ' || $prevChar == 'ل') 
                && (mb_strpos('آأإا', $crntChar) !== false)
            ) {
                if (mb_strpos($this->_prevLink, $chars[$i - 2]) !== false) {
                    $form++;
                }
                
                if (mb_strpos($this->_vowel, $chars[$i - 1])) {
                    $output .= '&#x';
                    $output .= $this->getGlyphs($crntChar, $form).';';
                } else {
                    $output .= '&#x';
                    $output .= $this->getGlyphs($prevChar.$crntChar, $form).';';
                }
                $nextChar = $prevChar;
                continue;
            }
            
            if ($prevChar && mb_strpos($this->_prevLink, $prevChar) !== false) {
                $form++;
            }
            
            if ($nextChar && mb_strpos($this->_nextLink, $nextChar) !== false) {
                $form += 2;
            }
            
            $output  .= '&#x' . $this->getGlyphs($crntChar, $form) . ';';
            $nextChar = $crntChar;
        }
        
        // from Arabic Presentation Forms-B, Range: FE70-FEFF, 
        // file "UFE70.pdf" (in reversed order)
        // into Arabic Presentation Forms-A, Range: FB50-FDFF, file "UFB50.pdf"
        // Example: $output = str_replace('&#xFEA0;&#xFEDF;', '&#xFCC9;', $output);
        // Lam Jeem

        $output = $this->decodeEntities($output, $exclude = array('&'));
        return $output;
    }
    
    /**
     * Regression analysis calculate roughly the max number of character fit in 
     * one A4 page line for a given font size.
     *      
     * @param integer $font Font size
     *      
     * @return integer Maximum number of characters per line
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public function a4MaxChars($font)
    {
        $x = 381.6 - 31.57 * $font + 1.182 * pow($font, 2) - 0.02052 * 
             pow($font, 3) + 0.0001342 * pow($font, 4);
        return floor($x - 2);
    }
    
    /**
     * Calculate the lines number of given Arabic text and font size that will 
     * fit in A4 page size
     *      
     * @param string  $str  Arabic string you would like to split it into lines
     * @param integer $font Font size
     *                    
     * @return integer Number of lines for a given Arabic string in A4 page size
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public function a4Lines($str, $font)
    {
        $str = str_replace(array("\r\n", "\n", "\r"), "\n", $str);
        
        $lines     = 0;
        $chars     = 0;
        $words     = explode(' ', $str);
        $w_count   = count($words);
        $max_chars = $this->a4MaxChars($font);
        
        for ($i = 0; $i < $w_count; $i++) {
            $w_len = mb_strlen($words[$i]) + 1;
            
            if ($chars + $w_len < $max_chars) {
                if (mb_strpos($words[$i], "\n") !== false) {
                    $words_nl = explode("\n", $words[$i]);
                    
                    $nl_num = count($words_nl) - 1;
                    for ($j = 1; $j < $nl_num; $j++) {
                        $lines++;
                    }
                    
                    $chars = mb_strlen($words_nl[$nl_num]) + 1;
                } else {
                    $chars += $w_len;
                }
            } else {
                $lines++;
                $chars = $w_len;
            }
        }
        $lines++;
        
        return $lines;
    }
    
    /**
     * Convert Arabic Windows-1256 charset string into glyph joining in UTF-8 
     * hexadecimals stream (take care of whole the document including English 
     * sections as well as numbers and arcs etc...)
     *                    
     * @param string  $str       Arabic string in Windows-1256 charset
     * @param integer $max_chars Max number of chars you can fit in one line
     * @param boolean $hindo     If true use Hindo digits else use Arabic digits
     *                    
     * @return string Arabic glyph joining in UTF-8 hexadecimals stream (take
     *                care of whole document including English sections as well
     *                as numbers and arcs etc...)
     * @author Khaled Al-Sham'aa <khaled@ar-php.org>
     */
    public function utf8Glyphs($str, $max_chars = 150, $hindo = true)
    {
        $str = str_replace(array("\r\n", "\n", "\r"), " \n ", $str);
        $str = str_replace("\t", "        ", $str);
        $a = array('°','À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 
        'Ç', 
        'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 
        'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 
        'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
		$b = array('&#176;','&#192;', '&#193;', '&#194;', '&#195;', '&#196;', '&#197;', '&#198;',
        '&#199;', '&#200;', '&#201;', '&#202;', '&#203;',
         '&#204;', '&#205;', '&#206;', '&#207;', '&#208;',
          '&#209;', '&#210;', '&#211;', '&#212;', '&#213;',
           '&#214;', '&#216;', '&#217;', '&#218;', '&#219;',
            '&#220;', '&#221;', '&#223;', '&#224;', '&#225;',
             '&#226;', '&#227;', '&#228;', '&#229;', '&#230;',
              '&#231;', '&#232;', '&#233;', '&#234;', '&#235;',
               '&#236;', '&#237;', '&#238;', '&#239;', '&#241;',
                '&#242;', '&#243;', '&#244;', '&#245;', '&#246;',
                 '&#248;', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
        
      
        $c = array('0ا','0أ','0إ','0آ', '0ب', '0ت','0ة', '0ث', '0ج', '0ح', '0خ', 'د0', '0ذ', '0ر' , '0ز', '0س',
         '0ش', '0ص', '0ض', '0ط', '0ظ',
         'ع0', '0غ', '0ف', '0ق', '0ك', '0ل', 'م0', '0ن', '0ه', '0و', '0ي', '0ئ','0ء','0ؤ','0لا','0ى',
         '1ا','1أ','1إ','1آ', '1ب', '1ت','1ة', '1ث', '1ج', '1ح', '1خ', 'د1', '1ذ', '1ر', '1ز', '1س',
         '1ش', '1ص', '1ض', '1ط', '1ظ',
         'ع1', '1غ', '1ف', '1ق', '1ك', '1ل', 'م1', '1ن', '1ه', '1و', '1ي', '1ئ','1ء','1ؤ','1لا','1ى',
         '2ا','2أ','2إ','2آ', '2ب', '2ت','2ة', '2ث', '2ج', '2ح', '2خ', 'د2', '2ذ', '2ر', '2ز', '2س',
         '2ش', '2ص', '2ض', '2ط', '2ظ',
         'ع2', '2غ', '2ف', '2ق', '2ك', '2ل', 'م2', '2ن', '2ه', '2و', '2ي', '2ئ','2ء','2ؤ','2لا','2ى',
         '3ا','3أ','3إ','3آ', '3ب', '3ت','3ة', '3ث', '3ج', '3ح', '3خ', 'د3', '3ذ', '3ر', '3ز', '3س',
         '3ش', '3ص', '3ض', '3ط', '3ظ',
         'ع3', '3غ', '3ف', '3ق', '3ك', '3ل', 'م3', '3ن', '3ه', '3و', '3ي', '3ئ','3ء','3ؤ','3لا','3ى',
         '4ا','4أ','4إ','4آ', '4ب', '4ت','4ة', '4ث', '4ج', '4ح', '4خ', 'د4', '4ذ', '4ر', '4ز', '4س',
         '4ش', '4ص', '4ض', '4ط', '4ظ',
         'ع4', '4غ', '4ف', '4ق', '4ك', '4ل', 'م4', '4ن', '4ه', '4و', '4ي', '4ئ','4ء','4ؤ','4لا','4ى',
         '5ا','5أ','5إ','5آ', '5ب', '5ت','5ة', '5ث', '5ج', '5ح', '5خ', 'د5', '5ذ', '5ر', '5ز', '5س',
         '5ش', '5ص', '5ض', '5ط', '5ظ',
         'ع5', '5غ', '5ف', '5ق', '5ك', '5ل', 'م5', '5ن', '5ه', '5و', '5ي', '5ئ','5ء','5ؤ','5لا','5ى',
         '6ا','6أ','6إ','6آ', '6ب', '6ت','6ة', '6ث', '6ج', '6ح', '6خ', 'د6', '6ذ', '6ر', '6ز', '6س',
         '6ش', '6ص', '6ض', '6ط', '6ظ',
         'ع6', '6غ', '6ف', '6ق', '6ك', '6ل', 'م6', '6ن', '6ه', '6و', '6ي', '6ئ','6ء','6ؤ','6لا','6ى',
         '7ا','7أ','7إ','7آ', '7ب', '7ت','7ة', '7ث', '7ج', '7ح', '7خ', 'د7', '7ذ', '7ر', '7ز', '7س',
         '7ش', '7ص', '7ض', '7ط', '7ظ',
         'ع7', '7غ', '7ف', '7ق', '7ك', '7ل', 'م7', '7ن', '7ه', '7و', '7ي', '7ئ','7ء','7ؤ','7لا','7ى',
         '8ا','8أ','8إ','8آ', '8ب', '8ت','8ة', '8ث', '8ج', '8ح', '8خ', 'د8', '8ذ', '8ر', '8ز', '8س',
         '8ش', '8ص', '8ض', '8ط', '8ظ',
         'ع8', '8غ', '8ف', '8ق', '8ك', '8ل', 'م8', '8ن', '8ه', '8و', '8ي', '8ئ','8ء','8ؤ','8لا','8ى',
         '9ا','9أ','9إ','9آ', '9ب', '9ت','9ة', '9ث', '9ج', '9ح', '9خ', 'د9', '9ذ', '9ر', '9ز', '9س',
         '9ش', '9ص', '9ض', '9ط', '9ظ',
         'ع9', '9غ', '9ف', '9ق', '9ك', '9ل', 'م9', '9ن', '9ه', '9و', '9ي', '9ئ','9ء','9ؤ','9لا','9ى');
         $c1 = array();
         foreach($c as $char) {
            $char1 = Str::reverse($char);
            array_push($c1,$char1);
         }
         $d = array('0 ا','0 أ','0 إ ','0 آ', '0 ب', '0 ت','0 ة', '0 ث', '0 ج', '0 ح', '0 خ', 'د0 ', '0 ذ', '0 ر', '0 ز', '0 س',
         '0 ش', '0 ص', '0 ض', '0 ط', '0 ظ',
         'ع0 ', '0 غ', '0 ف', '0 ق', '0 ك', '0 ل', 'م0 ', '0 ن', '0 ه', '0 و', '0 ي', '0 ئ','0 ء','0 ؤ','0 لا','0 ى',
         '1 ا','1 أ','1 إ','1 آ', '1 ب', '1 ت','1 ة', '1 ث', '1 ج', '1 ح', '1 خ', 'د1 ', '1 ذ', '1 ر', '1 ز', '1 س',
         '1 ش', '1 ص', '1 ض', '1 ط', '1 ظ',
         'ع1 ', '1 غ', '1 ف', '1 ق', '1 ك', '1 ل', 'م1 ', '1 ن', '1 ه', '1 و', '1 ي', '1 ئ','1 ء','1 ؤ','1 لا','1 ى',
         '2 ا','2 أ','2 إ','2 آ', '2 ب', '2 ت','2 ة', '2 ث', '2 ج', '2 ح', '2 خ', 'د2 ', '2 ذ', '2 ر', '2 ز', '2 س',
         '2 ش', '2 ص', '2 ض', '2 ط', '2 ظ',
         'ع2 ', '2 غ', '2 ف', '2 ق', '2 ك', '2 ل', 'م2 ', '2 ن', '2 ه', '2 و', '2 ي', '2 ئ','2 ء','2 ؤ','2 لا','2 ى',
         '3 ا','3 أ','3 إ','3 آ', '3 ب', '3 ت','3 ة', '3 ث', '3 ج', '3 ح', '3 خ', 'د3 ', '3 ذ', '3 ر', '3 ز', '3 س',
         '3 ش', '3 ص', '3 ض', '3 ط', '3 ظ',
         'ع3 ', '3 غ', '3 ف', '3 ق', '3 ك', '3 ل', 'م3 ', '3 ن', '3 ه', '3 و', '3 ي', '3 ئ','3 ء','3 ؤ','3 لا','3 ى',
         '4 ا','4 أ','4 إ','4 آ', '4 ب', '4 ت','4 ة', '4 ث', '4 ج', '4 ح', '4 خ', 'د4 ', '4 ذ', '4 ر', '4 ز', '4 س',
         '4 ش', '4 ص', '4 ض', '4 ط', '4 ظ',
         'ع4 ', '4 غ', '4 ف', '4 ق', '4 ك', '4 ل', 'م4 ', '4 ن', '4 ه', '4 و', '4 ي', '4 ئ','4 ء','4 ؤ','4 لا','4 ى',
         '5 ا','5 أ','5 إ','5 آ', '5 ب', '5 ت','5 ة', '5 ث', '5 ج', '5 ح', '5 خ', 'د5 ', '5 ذ', '5 ر', '5 ز', '5 س',
         '5 ش', '5 ص', '5 ض', '5 ط', '5 ظ',
         'ع5 ', '5 غ', '5 ف', '5 ق', '5 ك', '5 ل', 'م5 ', '5 ن', '5 ه', '5 و', '5 ي', '5 ئ','5 ء','5 ؤ','5 لا','5 ى',
         '6 ا','6 أ','6 إ','6 آ', '6 ب', '6 ت','6 ة', '6 ث', '6 ج', '6 ح', '6 خ', 'د6 ', '6 ذ', '6 ر', '6 ز', '6 س',
         '6 ش', '6 ص', '6 ض', '6 ط', '6 ظ',
         'ع6 ', '6 غ', '6 ف', '6 ق', '6 ك', '6 ل', 'م6 ', '6 ن', '6 ه', '6 و', '6 ي', '6 ئ','6 ء','6 ؤ','6 لا','6 ى',
         '7 ا','7 أ','7 إ','7 آ', '7 ب', '7 ت','7 ة', '7 ث', '7 ج', '7 ح', '7 خ', 'د7 ', '7 ذ', '7 ر', '7 ز', '7 س',
         '7 ش', '7 ص', '7 ض', '7 ط', '7 ظ',
         'ع7 ', '7 غ', '7 ف', '7 ق', '7 ك', '7 ل', 'م7 ', '7 ن', '7 ه', '7 و', '7 ي', '7 ئ','7 ء','7 ؤ','7 لا','7 ى',
         '8 ا','8 أ','8 إ','8 آ', '8 ب', '8 ت','8 ة', '8 ث', '8 ج', '8 ح', '8 خ', 'د8 ', '8 ذ', '8 ر', '8 ز', '8 س',
         '8 ش', '8 ص', '8 ض', '8 ط', '8 ظ',
         'ع8 ', '8 غ', '8 ف', '8 ق', '8 ك', '8 ل', 'م8 ', '8 ن', '8 ه', '8 و', '8 ي', '8 ئ','8 ء','8 ؤ','8 لا','8 ى',
         '9 ا','9 أ','9 إ','9 آ', '9 ب', '9 ت','9 ة', '9 ث', '9 ج', '9 ح', '9 خ', 'د9 ', '9 ذ', '9 ر', '9 ز', '9 س',
         '9 ش', '9 ص', '9 ض', '9 ط', '9 ظ',
         'ع9 ', '9 غ', '9 ف', '9 ق', '9 ك', '9 ل', 'م9 ', '9 ن', '9 ه', '9 و', '9 ي', '9 ئ','9 ء','9 ؤ','9 لا','9 ى');
		
         $d1 = array();
         foreach ($d as $char) {
            $char2 = Str::reverse($char);
            array_push($d1, $char2);
         }
         $str = str_replace($a, $b, $str); 
		$str = str_replace($c1, $d1, $str); 
        $lines   = array();
        $words   = explode(' ', $str);
        $w_count = count($words);
        $c_chars = 0;
        $c_words = array();
        
        $english  = array();
        $en_index = -1;
        
        $en_words = array();
        $en_stack = array();

        for ($i = 0; $i < $w_count; $i++) {
            $pattern  = '/^(\n?)';
            $pattern .= '[a-zA-Z0-9\/\@\#\$\%\^\&\*\(\)\_\~\"\'\[\]\{\}\;\,\|\-\.\:!]*';
            $pattern .= '([\.\:\+\=\-\!،؟]?)$/i';
           
            if (preg_match($pattern, $words[$i], $matches)) {
                if ($matches[1]) {
                    $words[$i] = mb_substr($words[$i], 1).$matches[1];
                }
                if ($matches[2]) {
                    $words[$i] = $matches[2].mb_substr($words[$i], 0, -1);
                }
                $words[$i] = strrev($words[$i]);
                array_push($english, $words[$i]);
                if ($en_index == -1) {
                    $en_index = $i;
                }
                $en_words[] = true;
            } elseif ($en_index != -1) {
                $en_count = count($english);
                
                for ($j = 0; $j < $en_count; $j++) {
                    $words[$en_index + $j] = $english[$en_count - 1 - $j];
                }
                
                $en_index = -1;
                $english  = array();
                
                $en_words[] = false;
            } else {
                $en_words[] = false;
            }
        }

        if ($en_index != -1) {
            $en_count = count($english);
            
            for ($j = 0; $j < $en_count; $j++) {
                $words[$en_index + $j] = $english[$en_count - 1 - $j];
            }
        }

        // need more work to fix lines starts by English words
        if (isset($en_start)) {
            $last = true;
            $from = 0;
            
            foreach ($en_words as $key => $value) {
                if ($last !== $value) {
                    $to = $key - 1;
                    array_push($en_stack, array($from, $to));
                    $from = $key;
                }
                $last = $value;
            }
            
            array_push($en_stack, array($from, $key));
            
            $new_words = array();
            
            while (list($from, $to) = array_pop($en_stack)) {
                for ($i = $from; $i <= $to; $i++) {
                    $new_words[] = $words[$i];
                }
            }
            
            $words = $new_words;
        }

        for ($i = 0; $i < $w_count; $i++) {
            $w_len = mb_strlen($words[$i]) + 1;
            
            if ($c_chars + $w_len < $max_chars) {
                if (mb_strpos($words[$i], "\n") !== false) {
                    $words_nl = explode("\n", $words[$i]);
                    
                    array_push($c_words, $words_nl[0]);
                    array_push($lines, implode(' ', $c_words));
                    
                    $nl_num = count($words_nl) - 1;
                    for ($j = 1; $j < $nl_num; $j++) {
                        array_push($lines, $words_nl[$j]);
                    }
                    
                    $c_words = array($words_nl[$nl_num]);
                    $c_chars = mb_strlen($words_nl[$nl_num]) + 1;
                } else {
                    array_push($c_words, $words[$i]);
                    $c_chars += $w_len;
                }
            } else {
                array_push($lines, implode(' ', $c_words));
                $c_words = array($words[$i]);
                $c_chars = $w_len;
            }
        }
        array_push($lines, implode(' ', $c_words));
        
        $maxLine = count($lines);
        $output  = '';
        
        for ($j = $maxLine - 1; $j >= 0; $j--) {
            $output .= $lines[$j] . "\n";
        }
        
        $output = rtrim($output);
        
        $output = $this->preConvert($output);
        if ($hindo) {
            $nums   = array(
                '0', '1', '2', '3', '4', 
                '5', '6', '7', '8', '9'
            );
            $arNums = array(
                '٠', '١', '٢', '٣', '٤',
                '٥', '٦', '٧', '٨', '٩'
            );
            
            foreach ($nums as $k => $v) {
                $p_nums[$k] = '/'.$v.'/ui';
            }
            $output = preg_replace($p_nums, $arNums, $output);
            
            foreach ($arNums as $k => $v) {
                $p_arNums[$k] = '/([a-z-\d]+)'.$v.'/ui';
            }
            foreach ($nums as $k => $v) {
                $r_nums[$k] = '${1}'.$v;
            }
            $output = preg_replace($p_arNums, $r_nums, $output);
            
            foreach ($arNums as $k => $v) {
                $p_arNums[$k] = '/'.$v.'([a-z-\d]+)/ui';
            }
            foreach ($nums as $k => $v) {
                $r_nums[$k] = $v.'${1}';
            }
            $output = preg_replace($p_arNums, $r_nums, $output);
        }

        return $output;
    }
    
    /**
     * Decode all HTML entities (including numerical ones) to regular UTF-8 bytes. 
     * Double-escaped entities will only be decoded once 
     * ("&amp;lt;" becomes "&lt;", not "<").
     *                   
     * @param string $text    The text to decode entities in.
     * @param array  $exclude An array of characters which should not be decoded.
     *                        For example, array('<', '&', '"'). This affects
     *                        both named and numerical entities.
     *                        
     * @return string           
     */
    protected function decodeEntities($text, $exclude = array())
    {
        static $table;
        
        // We store named entities in a table for quick processing.
        if (!isset($table)) {
            // Get all named HTML entities.
            $table = array_flip(get_html_translation_table(HTML_ENTITIES));
            
            // PHP gives us ISO-8859-1 data, we need UTF-8.
            $table = array_map('utf8_encode', $table);
            
            // Add apostrophe (XML)
            $table['&apos;'] = "'";
        }
        $newtable = array_diff($table, $exclude);
        
        // Use a regexp to select all entities in one pass, to avoid decoding 
        // double-escaped entities twice.
        //return preg_replace('/&(#x?)?([A-Za-z0-9]+);/e', 
        //                    '$this->decodeEntities2("$1", "$2", "$0", $newtable, 
        //                                             $exclude)', $text);

        $pieces = explode('&', $text);
        $text   = array_shift($pieces);
        foreach ($pieces as $piece) {
            if ($piece[0] == '#') {
                if ($piece[1] == 'x') {
                    $one = '#x';
                } else {
                    $one = '#';
                }
            } else {
                $one = '';
            }
            $end   = mb_strpos($piece, ';');
            $start = mb_strlen($one);
            
            $two   = mb_substr($piece, $start, $end - $start);
            $zero  = '&'.$one.$two.';';
            $text .= $this->decodeEntities2($one, $two, $zero, $newtable, $exclude).
                     mb_substr($piece, $end+1);
        }
        return $text;
    }
    
    /**
     * Helper function for decodeEntities
     * 
     * @param string $prefix    Prefix      
     * @param string $codepoint Codepoint         
     * @param string $original  Original        
     * @param array  &$table    Store named entities in a table      
     * @param array  &$exclude  An array of characters which should not be decoded
     * 
     * @return string                  
     */
    protected function decodeEntities2(
        $prefix, $codepoint, $original, &$table, &$exclude
    ) {
        // Named entity
        if (!$prefix) {
            if (isset($table[$original])) {
                return $table[$original];
            } else {
                return $original;
            }
        }
        
        // Hexadecimal numerical entity
        if ($prefix == '#x') {
            $codepoint = base_convert($codepoint, 16, 10);
        }
        
        // Encode codepoint as UTF-8 bytes
        if ($codepoint < 0x80) {
            $str = chr($codepoint);
        } elseif ($codepoint < 0x800) {
            $str = chr(0xC0 | ($codepoint >> 6)) . 
                   chr(0x80 | ($codepoint & 0x3F));
        } elseif ($codepoint < 0x10000) {
            $str = chr(0xE0 | ($codepoint >> 12)) . 
                   chr(0x80 | (($codepoint >> 6) & 0x3F)) . 
                   chr(0x80 | ($codepoint & 0x3F));
        } elseif ($codepoint < 0x200000) {
            $str = chr(0xF0 | ($codepoint >> 18)) . 
                   chr(0x80 | (($codepoint >> 12) & 0x3F)) . 
                   chr(0x80 | (($codepoint >> 6) & 0x3F)) . 
                   chr(0x80 | ($codepoint & 0x3F));
        }
        
        // Check for excluded characters
        if (in_array($str, $exclude)) {
            return $original;
        } else {
            return $str;
        }
    }
}

