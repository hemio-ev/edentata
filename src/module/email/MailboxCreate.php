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

use hemio\form;
use hemio\edentata\gui;
use \hemio\edentata\exception;

/**
 * Description of CreateAccount
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class MailboxCreate extends \hemio\edentata\Window {

    public function content() {
        $window = $this->newFormWindow(
                'create_account'
                , 'New Email Postbox'
                , null
                , _('Create')
        );

        $fieldsetEmail = new gui\Fieldset(_('New Email Address'));
        $email = new gui\FieldEmailWithSelect();

        $fieldsetPassword = new gui\Fieldset(_('Password'));
        $password = new gui\FieldNewPassword('password');

        $window->getForm()
                ->addChild($fieldsetEmail)
                ->addChild($email);

        $window->getForm()
                ->addChild($fieldsetPassword)
                ->addChild($password);

        $domains = $this->db()->getPossibleDomains();
        while ($domain = $domains->fetch()) {
            $email->getDomain()->addOption($domain['domain'], $domain['domain']);
        }

        $this->handleSubmit($window->getForm());

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form) {
        if ($form->submitted()) {
            if ($form->dataValid()) {
                $this->db()->mailboxCreate(
                        $form->getVal(['localpart', 'domain', 'password'])
                );
                
                throw new exception\Successful();
            } else {
                // find error?
            }
        }
    }

}
