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

use hemio\form;
use hemio\html;

/**
 * 
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Window extends html\Article {

    public function __construct($title = null, $subtitle = null) {
        if ($title) {
            $this['header'] = new html\Header();
            $this['header']['div'] = new html\Div();
            $this['header']['div']['title'] = new html\H1();
            $this['header']['div']['title'][] = new html\String($title);
            $this->addInheritableAppendage(
                    form\FormPost::FORM_FIELD_TEMPLATE, new form\template\FormPlainControl
            );
        }
        if ($title && $subtitle) {
            $this['header']['div']['title']['br'] = new html\Br();
            $this['header']['div']['title']['subtitle'] = new html\Span();
            $this['header']['div']['title']['subtitle'][] = new html\String($subtitle);
        }
    }

    public function addButton(html\Interface_\HtmlCode $button) {
        $this['header']->addChild($button);
    }

}
