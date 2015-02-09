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

namespace hemio\edentata\sql;

use hemio\edentata\exception;

/**
 * Description of ExceptionMapping
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ExceptionMapping {

    public static function throwMapped(exception\SqlSpecific $e) {
        switch ($e->getMessage()) {
            case 'login_invalid':
                throw new exception\Error(
                _('Invalid unser login.')
                , 1001
                , $e
                );

            case 'contingent_exceeded':
                throw new exception\Error(
                _('The operation you want to perform would exceed your current contingent.'
                        . ' Please contact the support to extend your contingent.')
                , 1002
                , $e
                );

            case 'inaccessible_or_missing':
                throw new exception\Error(
                _('The object you tried to change is inaccessible or missing.')
                , 1003
                , $e
                );

            default:
                throw $e;
        }
    }

}
