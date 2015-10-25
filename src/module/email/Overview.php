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
use hemio\html\Str;
use hemio\html;
use hemio\form;

/**
 * Description of Overview
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Overview extends Window
{

    public function content()
    {
        $window = $this->newWindow(_('Email'), null, false);

        $window->addButtonRight(
            new gui\LinkButton(
            $this->module->request->derive('address_create'), _('New Address')
            ), true
        );

        $mailboxes    = $window->addChild($this->mailboxes());
        $redirections = $window->addChild($this->redirections());

        if (
            $mailboxes instanceof html\Nothing &&
            $redirections instanceof html\Nothing
        ) {
            $window->addChild($this->start());
        }

        return $window;
    }

    protected function start()
    {
        $c   = new form\Container();
        $c[] = new gui\Hint(_('You do not own an email address.'));

        return $c;
    }

    protected function mailboxes()
    {
        $mailboxes = $this->db->mailboxSelect(false)->fetchAll();

        if (!count($mailboxes)) {
            return new html\Nothing();
        } else {
            $fieldset = new gui\Fieldset(_('Mailboxes'));
            $accounts = new gui\Listbox();
            $fieldset->addChild($accounts);

            foreach ($mailboxes as $mailbox) {
                $address = $mailbox['localpart'].'@'.$mailbox['domain'];
                $url     = $this->module->request->derive('mailbox_details',
                                                          $address);

                $mailboxEntry = $accounts->addLinkEntry(
                    $url
                    , new Str($address)
                    , $mailbox['backend_status']
                );

                // get aliases
                $aliases = $this->db->aliasSelect($mailbox['localpart'],
                                                  $mailbox['domain']);

                $ul    = $mailboxEntry->addChild(new html\Ul);
                while ($alias = $aliases->fetch()) {
                    $li = $ul->addLine(new Str($alias['localpart'].'@'.$alias['domain']));
                    $li->addChild(new gui\Progress($alias['backend_status']));
                }
            }

            return $fieldset;
        }
    }

    protected function redirections()
    {
        $redirectionData = $this->db->redirectionSelect()->fetchAll();

        if (!count($redirectionData)) {
            return new html\Nothing();
        } else {
            $fieldset     = new gui\Fieldset(_('Redirections'));
            $redirections = new gui\Listbox();
            $fieldset->addChild($redirections);

            foreach ($redirectionData as $redirection) {
                $address = Utils::toAddr($redirection);
                $url     = $this->module->request->derive('redirection_delete',
                                                          $address);
                $button  = new gui\LinkButton($url, _('Delete'));

                $span = new form\Container;
                $span->addChild(new Str($address));
                $span
                    ->addChild(new html\Ul)
                    ->addLine(new Str($redirection['destination']));

                $redirections->addEntry(
                    $span
                    , $redirection['backend_status']
                    , $button
                );
            }

            return $fieldset;
        }
    }
}
