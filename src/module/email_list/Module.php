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

use hemio\edentata;
use hemio\edentata\exception;
use hemio\edentata\exception\UnknownOperation;

/**
 * Description of ModuleEmail_list
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Module extends \hemio\edentata\Module {

    public static function getName() {
        return _('Mailing Lists');
    }

    public static function getDir() {
        return __DIR__;
    } 
    
    protected function constructHook() {
        $this->db = new Db($this->pdo);
    }

    public function getContent() {
        switch ($this->request->action) {
            case '':
                $content = (new Overview($this))->content();
                break;

            case 'list_create':
                try {
                    $content = (new ListCreate($this))->content();
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'list_delete':
                try {
                    $content = (new ListDelete($this))->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }
                break;

            case 'list_details':
                $content = (new ListDetails($this))->content($this->request->subject);
                break;

            case 'list_update':
                try {
                    $content = (new ListUpdate($this))->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive('list_details', true));
                }
                break;

            case 'subscribers_create':
                try {
                    $content = (new SubscriberCreate($this))->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive('list_details', true));
                }
                break;

            case 'subscribers_unsubscribe':
                try {
                    $content = (new SubscribersUnsubscribe($this))->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive('list_details', true));
                }
                break;

            default:
                throw UnknownOperation::unknownAction($this->request->action);
        }

        return $content;
    }

}
