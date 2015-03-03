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
use hemio\form;
use hemio\edentata\exception;

/**
 * Description of CreateAlias
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class AliasCreate extends Window
{

    public function content($mailboxName = '')
    {
        $window = $this->newFormWindow(
            'create_alias'
            , _('Create Email Alias')
            , null
            , _('Create')
        );

        $fieldsetEmail = new gui\Fieldset(_('New Email Address'));
        $email         = new gui\FieldEmailWithSelect();

        $fieldsetMailbox = new gui\Fieldset(_('Deliver emails to'));
        $mailbox         = new form\FieldSelect('mailbox', _('Mailbox'));
        $mailbox->setDefaultValue($mailboxName);

        $window->getForm()
            ->addChild($fieldsetEmail)
            ->addChild($email);

        $window->getForm()
            ->addChild($fieldsetMailbox)
            ->addChild($mailbox);

        $domains = $this->db->getPossibleDomains();
        while ($domain  = $domains->fetch()) {
            $email->getDomain()->addOption($domain['domain'], $domain['domain']);
        }

        $mailboxes = $this->db->mailboxSelect();
        while ($mbox      = $mailboxes->fetch()) {
            $addr = $mbox['localpart'].'@'.$mbox['domain'];
            $mailbox->addOption($addr, $addr);
        }

        $this->handleSubmit($window->getForm(), $mailbox);

        return $window;
    }

    protected function handleSubmit(
    gui\FormPost $form
    , form\FieldSelect $mailbox)
    {

        if ($form->submitted()) {
            if ($form->dataValid()) {
                $args = $form->getVal(['localpart', 'domain']) +
                    Db::emailAddressToArgs(
                        $mailbox->getValueUser()
                        , 'mailbox_'
                );

                $this->db->aliasCreate($args);

                throw new exception\Successful();
            } else {
                // find error?
            }
        }
    }
}