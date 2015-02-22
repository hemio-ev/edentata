<?php

/*
 * Copyright (C) 2015 Michael Herold <quabla@hemio.de>
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

use hemio\html;
use hemio\form;

/**
 * Description of Selectbox
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Selectbox extends html\Div {

    /**
     *
     * @var html\Div
     */
    protected $main;

    /**
     *
     * @var form\Container
     */
    protected $items;

    public function __construct() {
        $this->main = new html\Div;
        $this['scroll'] = $this->main;

        $this->items = new form\Container();
        $this->main['items'] = $this->items;

        $this->addCssClass('selectbox');
        $this->main->addCssClass('scroll');

        $this->addInheritableAppendage(
                form\FormPost::FORM_FIELD_TEMPLATE, new form\template\FormPlainControl()
        );
    }

    /**
     * 
     * @param string $name
     * @param string $title
     */
    public function addItem($name, $title, $backendStatus = null) {
        $item = new form\Container;
        $this->items->addChild($item);

        $item['checkbox'] = new form\FieldCheckbox($name, $title);
        $item['checkbox']->setDefaultValue($name);

        $item['p'] = new html\P();
        $item['p']['label'] = new html\Label();
        $item['p']['label']['span'] = new html\Span();
        $item['p']['label']['span']->addCssClass('checkbox');
        $item['p']['label']['span'][] = new html\String($title);

        $item['p']['label']->setAttribute(
                'for', $item['checkbox']->getHtmlName()
        );

        $item['p']['label'][] = new Progress($backendStatus);
    }

    public function setOptions(html\Interface_\HtmlCode $options) {
        $this->main['options'] = new html\P();
        $this->main['options']->addCssClass('options');
        $this->main['options']->addChild($options);
    }

    /**
     * 
     * @return array
     */
    public function getItemCheckboxFields() {
        $fields = [];
        foreach ($this->items as $item) {
            $fields[] = $item['checkbox'];
        }

        return $fields;
    }

}
