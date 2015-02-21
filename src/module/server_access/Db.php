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

namespace hemio\edentata\module\server_access;

use hemio\edentata\sql;

/**
 * Description of Db
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Db extends \hemio\edentata\ModuleDb {

    public function userDelete($params) {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'server_access.del_user'
        , $params
        ))->execute();
    }

    public function userCreate($params) {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'server_access.ins_user'
        , $params
        ))->execute();
    }

    public function userPassword($params) {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'server_access.upd_user'
        , $params
        ))->execute();
    }

    public function userSelect() {
        return
                (new sql\QuerySelectFunction(
                $this->pdo
                , 'server_access.sel_user'
                ))->execute();
    }

    public function userSelectSingle($user, $serviceName) {
        $stmt = new sql\QuerySelectFunction(
                $this->pdo
                , 'server_access.sel_user'
        );

        $stmt->options('WHERE "user" = :user AND service_name = :name');

        return $stmt->execute(['user' => $user, 'name' => $serviceName]);
    }

    public function activatableServiceSelect($service) {
        $stmt = new sql\QuerySelectFunction(
                $this->pdo
                , 'dns.sel_activatable_service')
        ;

        $stmt->select(['service_name']);
        $stmt->options('WHERE service = :service');

        return $stmt->execute(['service' => $service]);
    }

}
