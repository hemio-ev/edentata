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

use hemio\edentata\exception;
use hemio\html;

/**
 * Description of Explanation
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Explanation extends \hemio\form\Container
{

    public function __construct(exception\Printable $event)
    {
        $this['div'] = new html\Div;
        $this['div']->addCssClass('explanation');

        if ($event instanceof exception\Error) {
            $this['div']->setAttribute('role', 'alert');
        }

        $this['div']['h2']   = new html\H2();
        $this['div']['h2'][] = new html\String($event::title());

        $this['div']['p']   = new html\P();
        $this['div']['p'][] = new html\String($event->getMessage());
    }
}
