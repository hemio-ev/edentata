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

use hemio\edentata\exception;
use hemio\edentata;
use hemio\edentata\exception\UnknownOperation;

/**
 * Description of ModuleUnixuser
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Module extends \hemio\edentata\Module {

    /**
     *
     * @var Db
     */
    public $db;

    protected function constructHook() {
        $this->db = new Db($this->pdo);
    }

    public function getContent() {
        switch ($this->request->action) {
            case '':
                $content = (new Overview($this))->content();
                break;

            case 'create':
                try {
                    $content = (new UserCreate($this))->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'delete':
                try {
                    $content = (new UserDelete($this))->content($this->request->subject, $this->request->item);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'details':
                $content = (new UserDetails($this))->content($this->request->subject, $this->request->item);
                break;

            case 'password':
                $content = (new UserPassword($this))->content($this->request->subject, $this->request->item);
                break;

            case 'service':
                $content = (new UserService($this))->content();
                break;

            default:
                throw UnknownOperation::unknownAction($this->request->action);
        }

        return $content;
    }

    public static function getDir() {
        return __DIR__;
    }

    public static function getName() {
        return _('Server Access');
    }

}
