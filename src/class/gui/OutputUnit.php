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

class OutputUnit extends form\Container
{
    const NARROW_SPACE = ' ';

    public function __construct($title, $value, $unit)
    {
        $formatter = new \NumberFormatter(
            \Locale::getDefault()
            , \NumberFormatter::DECIMAL
        );

        $spellout = new \NumberFormatter(
            \Locale::getDefault()
            , \NumberFormatter::SPELLOUT
        );

        $str   = $formatter->format($value).self::NARROW_SPACE.$unit;
        $spell = sprintf('%s (%s)%s%s'
            , $formatter->format($value)
            , $spellout->format($value)
            , self::NARROW_SPACE
            , $unit
        );

        $this['p'] = new html\P();
        $this['p']->addCssClass('output');

        $this['p']['label'] = new html\Label();
        $this['p']['label']->addChild(new html\Str($title));

        $this['p']['output'] = new html\Output();
        $this['p']['output']->addChild(new html\Str($str));
        $this['p']['output']->setAttribute('title', $spell);
    }
}
