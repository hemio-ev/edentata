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

use hemio\edentata\gui;
use hemio\edentata\exception;

/**
 * Description of MailboxPassword
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class MailboxPassword extends \hemio\edentata\Window {

    public function content($address) {
        $window = $this->newFormWindow(
                'mailbox_password'
                , 'Mailbox Password'
                , $address
                , _('Change Password')
        );

        $password = new gui\FieldNewPassword('password');

        $window->getForm()->addChild($password);

        $this->handleSubmit($window->getForm(), $address);

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form, $address) {

        if ($form->submitted()) {
            if ($form->dataValid()) {
                $args = $form->getVal(['password']) +
                        Db::emailAddressToArgs($address);

                $this->db()->mailboxPassword($args);

                $e = new exception\Successful(
                        _('The password of your mailbox has been changed successfully.')
                );
                $e->backTo = $this->module->request->derive('mailbox_details', true);

                throw $e;
            }
        }
    }

}
