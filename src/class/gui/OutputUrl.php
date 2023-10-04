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

class OutputUrl extends form\Container
{

    public function __construct($title, $url)
    {
        $this['p'] = new html\P();
        $this['p']->addCssClass('output');

        $this['p']['label'] = new html\Label();
        $this['p']['label']->addChild(new html\Str($title));

        $this['p']['output'] = new html\Output();
        $this['p']['output']->setCssProperty('font-family', 'monospace');

        $a = new html\A();
        $a->setAttribute('href', $url);
        $a->addChild(new html\Str($url));

        $this['p']['output'][] = $a;
    }
}
