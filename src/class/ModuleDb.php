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
 * Description of ModuleDb
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ModuleDb
{
    /**
     *
     * @var \PDO
     */
    public $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function commit()
    {
        $this->pdo->commit();
    }

    public function availableDomains(array $services)
    {
        if (count($services) > 1)
            throw new exception\Error('Multiple services not implemented.');

        $stmt = new sql\QuerySelectFunction(
            $this->pdo, 'dns.sel_available_service'
        );
        $stmt->options('WHERE service = :service');

        return $stmt->execute(['service' => $services[0]]);
    }

    /**
     *
     * @return \PDOStatement
     */
    public function getUsableDomains($service, $subservice)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo, 'dns.sel_usable_domain',
            ['p_service' => $service, 'p_subservice' => $subservice]
        );

        return $stmt->execute();
    }
}
