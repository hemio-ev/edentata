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
        $window = new \hemio\edentata\gui\Window(_('Email'));
        $window->addButtonRight(
                new gui\LinkButton(
                $this->module->request->derive('create'), _('New Email Address')
                ), true
        );

        $fieldset = new gui\Fieldset(_('Mailboxes'));
        $accounts = new gui\Listbox();

        $window
                ->addChild($fieldset)
                ->addChild($accounts);


        $mailboxes = $this->db()->getMailboxes();

        while ($mailbox = $mailboxes->fetch()) {
            $address = $mailbox['localpart'] . '@' . $mailbox['domain'];
            $url = $this->module->request->derive('edit_account', $address);

            $container = new \hemio\form\Container();
            $container['div'] = new \hemio\html\Div;
            $container['div'][] = new String($address);

            // get aliases
            $aliases = $this->db()->getAliases($mailbox['localpart'], $mailbox['domain']);
            $ul = new \hemio\html\Ul();
            $container['div'][] = $ul;
            while ($alias = $aliases->fetch()) {
                $ul->addLine(new String($alias['localpart'].'@'.$alias['domain']));
            }

            $accounts->addLink($url, $container);
        }

        return $window;
    }

}
