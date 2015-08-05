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
 * Description of Module
 *
 * @author Michael Herold <quabla@hemio.de>
 */
abstract class Module implements ModuleInterface
{
    /**
     *
     * @var Request
     */
    public $request;

    /**
     * Database connection
     *
     * @var sql\Connection
     */
    public $pdo;

    /**
     *
     * @var I10n
     */
    public $i10n;

    /**
     * @return \hemio\html\Interface_\HtmlCode HTML code for module
     */
    abstract public function getContent();

    abstract protected function constructHook();

    public function __construct(Request $request, sql\Connection $pdo,
                                I10n $i10n)
    {
        $this->request = $request;
        $this->pdo     = $pdo;
        $this->i10n    = $i10n;
        $this->constructHook();
    }
}
