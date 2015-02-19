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

namespace hemio\edentata\module\email_list;

use hemio\edentata\sql;
use hemio\edentata\module\email;

/**
 * Description of Db
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Db extends \hemio\edentata\ModuleDb {

    public function listCreate($args) {
        $stmt = new sql\QuerySelectFunction(
                $this->pdo
                , 'email.ins_list'
                , $args
        );

        return $stmt->execute();
    }

    public function listSelect($activeOnly = true) {
        $stmt = new sql\QuerySelectFunction(
                $this->pdo
                , 'email.sel_list'
        );

        return $stmt->execute();
    }

    public function subscriberCreate(array $params) {
        $stmt = new sql\QuerySelectFunction(
                $this->pdo
                , 'email.ins_list_subscriber'
                , $params
        );

        return $stmt->execute();
    }

    public function subscriberSelect($list) {
        $stmt = new sql\QuerySelectFunction(
                $this->pdo
                , 'email.sel_list_subscriber'
        );

        $params = email\Db::emailAddressToArgs($list, 'list_');
        $stmt->options('WHERE list_localpart = :p_list_localpart AND list_domain = :p_list_domain');

        return $stmt->execute($params);
    }

    public function subscriberDelete($list, $subscriberAddr) {
        $args = email\Db::emailAddressToArgs($list, 'list_');
        $args['p_address'] = $subscriberAddr;

        $stmt = new sql\QuerySelectFunction(
                $this->pdo
                , 'email.del_list_subscriber'
                , $args
        );

        return $stmt->execute();
    }

    public function availableDomains() {
        $stmt = new sql\QuerySelectFunction(
                $this->pdo, 'dns.sel_available_service'
        );
        $stmt->options('WHERE service = :service');

        return $stmt->execute(['service' => 'email__list']);
    }

}
