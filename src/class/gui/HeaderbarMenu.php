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
use hemio\edentata;

class HeaderbarMenu extends form\Container
{

    public function __construct()
    {
        $this['button'] = new html\Button('button');
        $this['button']->addCssClass('popover');
        $this['button']->addCssClass('headerbar_menu');

        $this['ul'] = new html\Ul();
        $this['ul']->addCssClass('popover');
    }

    public function addEntry(edentata\Request $request = null, $text = null)
    {
        if ($request !== null)
            return $this['ul']->addLine(new Link($request, $text));
        else
            return $this['ul']->addLine();
    }
}