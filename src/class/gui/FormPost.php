<?php
/*
 * Copyright (C) 2015 Sophie Herold <sophie@hemio.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace hemio\edentata\gui;

use hemio\form;
use hemio\html;
use hemio\form\Abstract_\TemplateFormField;
use hemio\form\Abstract_\FormElement;
use hemio\edentata\exception;

/**
 * Description of FormPost
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class FormPost extends form\FormPost
{

    public function __construct($name, array $post = null, array $get = null,
                                array $stored = array())
    {
        parent::__construct($name, $post, $get, $stored);
        $this->setAttribute('autocomplete', 'off');

        // default template to patch
        $template = $this->getSingleControlTemplate();

        // patch template for <select> controls
        $templateSelect = $this->patchTemplateForSelect(clone $template);
        $this->addInheritableAppendage(
            FormPost::FORM_FIELD_TEMPLATE.'_SELECT', $templateSelect
        );

        $this->addHookToString(
            function (html\Interface_\HtmlCode $elem) {

            $filter = function ($elem) {
                return $elem instanceof \hemio\form\Abstract_\FormField;
            };

            foreach ($elem->getRecursiveIterator($filter) as $formElement)
                foreach ($formElement->getErrors() as $error)
                    $formElement->addChild(new Explanation(new exception\Error($error)));
        }
            , 'error_messages'
        );
    }

    protected function patchTemplateForSelect($templateSelect)
    {
        $templateSelect['P']['SPAN'] = new html\Span();
        $templateSelect['P']['SPAN']->addCssClass('select');

        $templateSelect->setPostInitHook(function (TemplateFormField $template) {
            unset($template['P']['CONTROL']);
            $template['P']['SPAN']['CONTROL'] = $template->getControl();
        });

        return $templateSelect;
    }

    public function getVal(array $keys, $prefix = 'p_')
    {
        $arr = [];

        $filter = function ($child) {
            return $child instanceof FormElement;
        };

        foreach ($this->getRecursiveIterator($filter) as $elem) {
            if (in_array($elem->getName(), $keys)) {
                $arr[$prefix.$elem->getName()] = $elem->getValueUser();
            }
        }

        return $arr;
    }
}
