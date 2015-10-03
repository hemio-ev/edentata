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

namespace hemio\edentata;

/**
 * Description of System
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class System
{
    static public $requestTime;
    static public $startTime;
    static public $endTime;

    static public function init()
    {
        self::$startTime   = microtime(true);
        self::$requestTime = filter_input(
            INPUT_SERVER, 'REQUEST_TIME_FLOAT', FILTER_VALIDATE_FLOAT,
            [ 'options' => ['default' => self::$startTime]]
        );
    }

    static public function reportString()
    {
        return sprintf("body_time: %s\nbody_time_request: %s"
            , round(microtime(true) - self::$startTime, 4)
            , round(microtime(true) - self::$requestTime, 4)
        );
    }
}
