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

/**
 * Description of QuerySelectFunction
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class QuerySelectFunctionTest extends \Helpers {

    public function test1() {

        $pdo = new Connection('pgsql:dbname=test1', 'postgres');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $qryAuth = new QuerySelectFunction($pdo, 'user.login', ['p_name' => 'user1',
            'p_password' => 'pw'
                ]
        );
        echo $qryAuth->getFunctionCall();
        $qryAuth->execute();

        $params = [
            'p_local_part' => 'hasi82',
            'p_domain' => 'my.example.com.'
        ];

        $qry = new QuerySelectFunction($pdo, 'email.frontend_account_create', $params);
        //echo $qry->getFunctionCall();
        $stmt = $qry->execute();
        $x = $stmt->fetch(\PDO::FETCH_ASSOC);
        print_r($x);
        echo $x . 'a';
    }

}
