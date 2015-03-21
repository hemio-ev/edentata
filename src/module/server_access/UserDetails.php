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

namespace hemio\edentata\module\server_access;

use hemio\edentata\gui;

/**
 * Description of UserDetails
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class UserDetails extends Window
{

    public function content($user, $serviceName)
    {
        $window = $this->newWindow(_('User'), $user.' @ '.$serviceName);

        $selecting = new gui\Selecting();

        $selecting->addLink(
            $this->request->derive('password', $user, $serviceName)
            , _('Change password authentication')
        );

        $selecting->addLink(
            $this->request->derive('delete', $user, $serviceName)
            , _('Delete user')
        );

        $userData = $this->db->userSelectSingle($user, $serviceName)->fetch();

        if ($userData === false)
            throw new \hemio\edentata\exception\Error('User not found.');

        if ($userData['password_login'])
            $status = _('Enabled');
        else
            $status = _('Disabled');

        $selecting->addChildBeginning(
            new gui\Output(_('Password Authentication'), $status)
        );

        $window->addChild($selecting);

        return $window;
    }
}
