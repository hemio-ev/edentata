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
 * Description of ExceptionMapper
 *
 * @author Michael Herold <quabla@hemio.de>
 */
abstract class ExceptionMapper
{
    protected $defaultRequest;

    public function __construct(\hemio\edentata\Request $defaultRequest)
    {
        $this->defaultRequest = $defaultRequest;
    }

    abstract public function map(exception\SqlSpecific $e);

    /**
     *
     * @param string $message
     * @param int $code
     * @param exception\SqlSpecific $previous
     */
    public function error($message, $code, exception\SqlSpecific $previous)
    {
        $e         = new exception\Error($message, $code, $previous);
        $e->backTo = $this->defaultRequest;

        return $e;
    }
}
