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

/**
 * Description of AccountDetails
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class AccountDetails extends Window {

    public function content($account) {
        $window = $this->newWindow(_('Jabber Account'), $account);

        $selecting = new \hemio\edentata\gui\Selecting(_('Possible Actions'));
        
        $selecting->addLink(
                $this->request->derive('password', $account)
                , _('Change password')
        );
        
        $selecting->addLink($this->request->derive('delete', $account)
                , _('Delete account')
        );

        $window->addChild($selecting);

        return $window;
    }

}
