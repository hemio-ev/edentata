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

namespace hemio\edentata\module\example;

use hemio\edentata\gui;

/**
 * Example module without funcionality
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class Module extends \hemio\edentata\Module
{

    public static function getDir()
    {
        return __DIR__;
    }

    public static function getName()
    {
        return _('Example');
    }

    protected function constructHook()
    {

    }

    public function getContent()
    {
        $window = new gui\Window(_('Example Module'));

        $window->addChild(new gui\Hint(
            _('Simple example module without functionality.')));

        return $window;
    }
}
