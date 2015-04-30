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
use hemio\edentata\exception;

/**
 * Description of AccountDetails
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class AccountDetails extends Window
{

    public function content($account)
    {
        $window = $this->newWindow(_('Jabber Account'), $account);

        $details = $this->db->accountSelectSingle($account)->fetch();
        if (!$details)
            throw new exception\Error('Jabber Account does not exist');

        $window[] = new gui\OutputStatus($details);
        $window[] = new gui\Output(_('Account'), $account);

        $menu = $window->addHeaderbarMenu();

        $menu->addEntry(
            $this->request->derive('password', $account)
            , _('Change password')
        );

        $menu->addEntry(
            $this->request->derive('delete', $account)
            , _('Delete account')
        );

        return $window;
    }
}
