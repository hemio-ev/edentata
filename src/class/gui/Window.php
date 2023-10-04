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

/**
 *
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class Window extends html\Article
{

    public function __construct($title = null, $subtitle = null)
    {
        if ($title) {
            $this['header']                 = new html\Header();
            $this['header']['button_right'] = new html\Div();
            $this['header']['button_left']  = new html\Div();
            $this['header']['title']        = new html\H1();
            $this['header']['title'][]      = new html\Str($title);
            $this['header']->addInheritableAppendage(
                form\FormPost::FORM_FIELD_TEMPLATE,
                new form\template\FormPlainControl
            );
        }
        if ($title && $subtitle) {
            $this['header']['title']['br']         = new html\Br();
            $this['header']['title']['subtitle']   = new html\Span();
            $this['header']['title']['subtitle'][] = new html\Str($subtitle);
        }
    }

    public function addButtonLeft(html\Interface_\HtmlCode $button)
    {
        $this['header']['button_left']->addChild($button);
    }

    public function addButtonRight(html\Interface_\HtmlCode $button,
                                   $suggested = false, $beginning = false)
    {
        if ($beginning)
            $this['header']['button_right']->addChildBeginning($button);
        else
            $this['header']['button_right']->addChild($button);

        if ($button instanceof LinkButton)
            $button->setSuggested($suggested);
    }

    /**
     *
     * @return HeaderbarMenu
     */
    public function addHeaderbarMenu()
    {
        $menu = new HeaderbarMenu();
        $this->addButtonRight($menu);

        return $menu;
    }
}
