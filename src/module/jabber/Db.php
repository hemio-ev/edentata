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

namespace hemio\edentata\module\jabber;

use hemio\edentata\sql;

/**
 * Description of Db
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Db extends \hemio\edentata\ModuleDb {

    public static function accountToArgs($address) {
        return [
            'p_node' => explode('@', $address)[0],
            'p_domain' => explode('@', $address)[1]
        ];
    }

    public function accountDelete($params) {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'jabber.del_account'
        , $params
        ))->execute();
    }

    public function accountCreate($params) {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'jabber.ins_account'
        , $params
        ))->execute();
    }

    public function accountPassword($params) {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'jabber.upd_account'
        , $params
        ))->execute();
    }

    public function accountSelect() {
        return
                (new sql\QuerySelectFunction(
                $this->pdo
                , 'jabber.sel_account'
                ))->execute();
    }
   
}
