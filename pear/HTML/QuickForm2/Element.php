<?php
/**
 * Base class for simple HTML_QuickForm2 elements (not Containers)
 *
 * PHP version 5
 *
 * LICENSE:
 *
 * Copyright (c) 2006-2011, Alexey Borzov <avb@php.net>,
 *                          Bertrand Mansion <golgote@mamasam.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *    * Redistributions of source code must retain the above copyright
 *      notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright
 *      notice, this list of conditions and the following disclaimer in the
 *      documentation and/or other materials provided with the distribution.
 *    * The names of the authors may not be used to endorse or promote products
 *      derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
 * IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   HTML
 * @package    HTML_QuickForm2
 * @author     Alexey Borzov <avb@php.net>
 * @author     Bertrand Mansion <golgote@mamasam.com>
 * @license    http://opensource.org/licenses/bsd-license.php New BSD License
 * @version    SVN: $Id: Element.php 311435 2011-05-26 10:30:06Z avb $
 * @link       http://pear.php.net/package/HTML_QuickForm2
 */

/**
 * Base class for all HTML_QuickForm2 elements
 */
require_once 'HTML/QuickForm2/Node.php';

/**
 * Abstract base class for simple QuickForm2 elements (not Containers)
 *
 * @category   HTML
 * @package    HTML_QuickForm2
 * @author     Alexey Borzov <avb@php.net>
 * @author     Bertrand Mansion <golgote@mamasam.com>
 * @version    Release: 0.6.1
 */
abstract class HTML_QuickForm2_Element extends HTML_QuickForm2_Node
{
    public function setName($name)
    {
        $this->attributes['name'] = (string)$name;
        $this->updateValue();
        return $this;
    }

   /**
    * Generates hidden form field containing the element's value
    *
    * This is used to pass the frozen element's value if 'persistent freeze'
    * feature is on
    *
    * @return string
    */
    protected function getPersistentContent()
    {
        if (!$this->persistent || null === ($value = $this->getValue())) {
            return '';
        }
        return '<input type="hidden"' . self::getAttributesString(array(
            'name'  => $this->getName(),
            'value' => $value,
            'id'    => $this->getId()
        )) . ' />';
    }

   /**
    * Called when the element needs to update its value from form's data sources
    *
    * The default behaviour is to go through the complete list of the data
    * sources until the non-null value is found.
    */
    protected function updateValue()
    {
        $name = $this->getName();
        foreach ($this->getDataSources() as $ds) {
            if (null !== ($value = $ds->getValue($name))) {
                $this->setValue($value);
                return;
            }
        }
    }

   /**
    * Renders the element using the given renderer
    *
    * @param    HTML_QuickForm2_Renderer    Renderer instance
    * @return   HTML_QuickForm2_Renderer
    */
    public function render(HTML_QuickForm2_Renderer $renderer)
    {
        $renderer->renderElement($this);
        $this->renderClientRules($renderer->getJavascriptBuilder());
        return $renderer;
    }

   /**
    * Returns Javascript code for getting the element's value
    *
    * @param  bool  Whether it should return a parameter for qf.form.getContainerValue()
    * @return string
    */
    public function getJavascriptValue($inContainer = false)
    {
        return $inContainer? "'{$this->getId()}'": "qf.\$v('{$this->getId()}')";
    }

    public function getJavascriptTriggers()
    {
        return array($this->getId());
    }

    /**
     * Applies recursive and non-recursive filters on element value
     *
     * @param    mixed   Element value
     * @return   mixed   Filtered value
     */
    protected function applyFilters($value)
    {
        $recursive = $this->recursiveFilters;
        $container = $this->getContainer();
        while (!empty($container)) {
            $recursive = array_merge($container->recursiveFilters, $recursive);
            $container = $container->getContainer();
        }
        foreach ($recursive as $filter) {
            if (is_array($value)) {
                array_walk_recursive($value,
                    array('HTML_QuickForm2_Node', 'applyFilter'), $filter);
            } else {
                self::applyFilter($value, null, $filter);
            }
        }
        return parent::applyFilters($value);
    }
}
?>
