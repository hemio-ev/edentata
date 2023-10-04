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

namespace hemio\edentata\exception;

/**
 * Description of UnknownOperation
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class UnknownOperation extends Error {

    public static function unknownAction($action) {
        $msg = sprintf(_('Unkown module action "%s".'), $action);
        return new UnknownOperation($msg, 90404);
    }

}
