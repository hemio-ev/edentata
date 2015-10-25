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
use hemio\html;
use hemio\edentata\exception;
use hemio\form;

/**
 * Description of EditAccount
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class MailboxDetails extends Window
{

    public function content($address)
    {
        $window = $this->newFormWindow(
            'edit_account'
            , _('Email Mailbox')
            , $address
        );

        $mailbox = $this->db->mailboxSelectSingle($address)->fetch();
        if (!$mailbox)
            throw new exception\Error(_('Mailbox does not exist.'));

        $menu = $window->addHeaderbarMenu();
        $this->addActions($menu, $address);

        $window->addChild($this->details($mailbox));
        $window->addChild($this->aliases($address));

        return $window;
    }

    protected function details(array $mailbox)
    {
        $container = new form\Container();

        $container[] = new gui\OutputStatus($mailbox);
        if ($mailbox['quota'] !== null)
            $container[] = new gui\OutputUnit(
                _('Quota')
                , $mailbox['quota']
                , 'MB'
            );

        return $container;
    }

    protected function aliases($address)
    {
        $resAliases = $this->db->aliasSelect(
            Utils::addrLocalpart($address)
            , Utils::addrDomain($address)
        );

        $list = new gui\Listbox();
        foreach ($resAliases as $alias) {
            $aliasAddr = $alias['localpart'].'@'.$alias['domain'];
            $button    = new gui\LinkButton(
                $this->module->request->derive(
                    'alias_delete'
                    , $address
                    , $aliasAddr
                )
                , _('Delete')
            );

            $list->addEntry(
                new html\Str($aliasAddr)
                , $alias['backend_status']
                , $button
            );
        }

        if (!$list->count())
            return new html\Nothing;

        $fieldset = new gui\Fieldset(_('Aliases'));
        $fieldset->addChild($list);

        return $fieldset;
    }

    protected function addActions($menu, $address)
    {
        $menu->addEntry(
            $this->module->request->derive('mailbox_password', $address)
            , _('Change password')
        );

        $menu->addEntry(
            $this->module->request->derive('alias_create', $address)
            , _('Create alias for this mailbox')
        );

        $menu->addEntry(
            $this->module->request->derive('mailbox_delete', $address)
            , _('Delete entire mailbox')
        );
    }
}
