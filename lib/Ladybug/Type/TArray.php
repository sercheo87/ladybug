<?php
/*
 * Ladybug: Simple and Extensible PHP Dumper
 * 
 * Type/TArray variable type
 *
 * (c) Raúl Fraile Beneyto <raulfraile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ladybug\Type;

use Ladybug\Variable;
use Ladybug\Options;
use Ladybug\CLIColors;
use Ladybug\Type\TFactory;

class TArray extends Variable {
    
    protected $length;
    
    public function __construct($var, $level, Options $options) {
        parent::__construct('array', array(), $level, $options);
        
        $this->length = count($var);
        
        if ($this->level < $this->options->getOption('array.max_nesting_level')) {
            foreach ($var as $k=>$v) {
                $this->add($v, $k);
            }
        }
    }
    
    public function getLength() {
        return $this->length;
    }
    
    public function setLength(int $length) {
        $this->length = $length;
    }
    
    public function add($var, $index = NULL) {
        $this->value[$index] = TFactory::factory($var, $this->level, $this->options);
    }
    
    // override
    protected function _renderHTML($array_key = NULL) {
        $label = $this->type . '(' . $this->length . ')';
        
        $result = $this->renderTreeSwitcher($label, $array_key);
        
        if ($this->length > 0) {
            $result .= '<ol>';
        
            foreach ($this->value as $k=>$v) {
                $result .= '<li>'.$v->render($k).'</li>';
            }
            $result .= '</ol>';
        }
        
        return $result;
    }
    
    // override
    protected function _renderCLI($array_key = NULL) {
        $label = $this->type . '(' . $this->length . ')';
        
        $result = '';
        
        if (!is_null($array_key)) $result .= '[' . $array_key . ']: ';
            
        $result .= CLIColors::getColoredString($label, 'yellow') . "\n";
        
        foreach ($this->value as $k=>$v) {
            $result .= $this->indentCLI().$v->render($k, 'cli');
        }
        
        return $result;
    }
}