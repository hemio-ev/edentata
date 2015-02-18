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

use hemio\edentata\sql;
use hemio\edentata\gui;
use hemio\html\String;

/**
 * Description of Overview
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Overview extends \hemio\edentata\Window {

    public function content() {
        $window = $this->newWindow(_('Email'), null, false);

        $window->addButtonRight(
                new gui\LinkButton(
                $this->module->request->derive('address_create'), _('New Address')
                ), true
        );

        $window->addChild($this->mailboxes());
        $window->addChild($this->redirections());

        return $window;
    }

    protected function mailboxes() {
        $fieldset = new gui\Fieldset(_('Mailboxes'));
        $accounts = new gui\Listbox();

        $fieldset->addChild($accounts);

        $mailboxes = $this->db()->mailboxSelect(false);

        while ($mailbox = $mailboxes->fetch()) {
            $address = $mailbox['localpart'] . '@' . $mailbox['domain'];
            $url = $this->module->request->derive('mailbox_details', $address);

            $mailboxLi = $accounts->addLink($url, new String($address));

            // get aliases
            $aliases = $this->db()->aliasSelect($mailbox['localpart'], $mailbox['domain']);

            $ul = $mailboxLi->addList();
            while ($alias = $aliases->fetch()) {
                $ul->addLine(new String($alias['localpart'] . '@' . $alias['domain']));
            }

            $mailboxLi->setPending($mailbox['backend_status']);
        }

        return $fieldset;
    }

    protected function redirections() {
        $fieldset = new gui\Fieldset(_('Redirections'));
        $redirections = new gui\Listbox();

        $fieldset->addChild($redirections);

        $redirectionData = $this->db()->redirectionSelect()->fetchAll();

        foreach ($redirectionData as $redirection) {
            $address = $redirection['localpart'] . '@' . $redirection['domain'];
            $url = $this->module->request->derive('redirection_delete', $address);

            $li = $redirections->addLink($url, new String($address));
            $li->setPending($redirection['backend_status']);
            
            $subUl = $li->addList();
            $subUl->addLine(new String($redirection['destination']));
        }

        return $fieldset;
    }

}
