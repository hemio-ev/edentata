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

namespace hemio\edentata\module\email;

use hemio\edentata\sql;

/**
 * Description of DbQueries
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Db {

    /**
     * 
     * @param \PDO $pdo
     * @return \PDOStatement
     */
    public static function getMailAccounts(\PDO $pdo) {
        $stmt = new sql\QuerySelectFunction($pdo, 'email.frontend_account');
        return $stmt->execute();
    }

    /**
     * 
     * @param \PDO $pdo
     * @return \PDOStatement
     */
    public function getPossibleDomains(\PDO $pdo) {
        $stmt = new sql\QuerySelectFunction($pdo, 'dns.fs_service_domain');
        $stmt->options('WHERE service = :service');
        return $stmt->execute(['service' => 'email']);
    }

}
