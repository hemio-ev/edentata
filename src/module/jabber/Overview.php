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

namespace hemio\edentata\module\jabber;

use hemio\edentata\gui;

/**
 * Description of Overview
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Overview extends Window
{

    public function content()
    {
        $window = $this->newWindow(_('Jabber Accounts'), null, false);

        $window->addButtonRight(
            new gui\LinkButton(
            $this->request->derive('create')
            , _('Create Account')
            )
            , true
        );

        $window->addChild($this->accounts());

        return $window;
    }

    protected function accounts()
    {
        $accounts = $this->db->accountSelect()->fetchAll();

        if (empty($accounts))
            return new gui\Hint(
                _('You do not own a jabber account.')
            );

        $list = new gui\Listbox();
        foreach ($accounts as $account) {
            $addr = $account['node'].'@'.$account['domain'];
            $list->addLinkEntry(
                $this->request->derive('details', $addr)
                , new \hemio\html\Str($addr)
                , $account['backend_status']
            );
        }

        return $list;
    }
}
