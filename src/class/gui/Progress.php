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

/**
 * Description of Pending
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Progress extends \hemio\form\Container {

    public function __construct($backendStatus) {
        if ($backendStatus !== null) {

            $this['span'] = new html\Span();
            $this['span']->addCssClass('progress');

            switch ($backendStatus) {
                case 'del':
                    $msg = _('Deletion Pending');
                    break;

                case 'upd':
                    $msg = _('Changes Pending');
                    break;

                case 'ins':
                    $msg = _('Setup Pending');
                    break;

                default:
                    $msg = _('Unknown Status');
            }

            $this['span']->addChild(new html\String($msg));
        }
    }

}
