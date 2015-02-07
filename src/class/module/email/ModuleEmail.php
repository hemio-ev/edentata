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

use hemio\edentata;
use hemio\edentata\exception;
use hemio\edentata\exception\UnknownOperation;

/**
 * Description of Module
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ModuleEmail extends edentata\Module {

    public static function getName() {
        return _('Email');
    }

    public function getContent() {

        switch ($this->request->action) {
            case '':
                $content = (new Overview($this))->content();
                break;

            case 'edit_mailbox':
                $content = (new MailboxDetails($this))->content($this->request->subject);
                break;

            case 'mailbox_password':
                $content = (new MailboxPassword($this))->content($this->request->subject);
                break;

            case 'mailbox_delete':
                $content = (new MailboxDelete($this))->content($this->request->subject);
                break;

            case 'create':
                $content = (new Create($this))->content();
                break;

            case 'create_mailbox':
                try {
                    $content = (new MailboxCreate($this))->content();
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }

                break;

            case 'create_alias':
                try {
                    $content = (new AliasCreate($this))->content($this->request->subject);
                } catch (exception\Successful $e) {
                    edentata\Utils::htmlRedirect($this->request->derive());
                }

                break;

            default:
                throw UnknownOperation::unknownAction($this->request->action);
        }

        return $content;
    }

}
