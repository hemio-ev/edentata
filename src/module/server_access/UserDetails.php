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
        $window = $this->newWindow(_('Server Access'), $user.'@'.$serviceName);

        $userData = $this->db->userSelectSingle($user, $serviceName)->fetch();

        if ($userData === false)
            throw new \hemio\edentata\exception\Error('User not found.');

        $window->addChild($this->details($userData));
        $window->addChild($this->actions($user, $serviceName));

        return $window;
    }

    protected function details($userData)
    {
        if ($userData['password_login'] === null)
            $status = _('Disabled');
        else
            $status = _('Enabled');

        $proto = Utils::serviceToProto($userData['service']);

        $fieldset = new gui\Fieldset(_('Details'));

        $fieldset->addChild(
            new gui\Output(_('User'), $userData['user'])
        );

        $fieldset->addChild(
            new gui\Output(_('Host'), $userData['service_name'])
        );

        $fieldset->addChild(
            new gui\Output(_('Communication Protocol'), strtoupper($proto))
        );

        $fieldset->addChild(
            new gui\Output(_('Password Authentication'), $status)
        );

        $fieldset->addChild(
            new gui\Output(
            _('Connection URI')
            ,
              sprintf(
                '%s://%s@%s'
                , $proto
                , $userData['user']
                , $userData['service_name']
            )
            )
        );

        return $fieldset;
    }

    protected function actions($user, $serviceName)
    {
        $fieldset = new gui\Fieldset(_('Possible Actions'));

        $selecting = new gui\Selecting();

        $selecting->addLink(
            $this->request->derive('password', $user, $serviceName)
            , _('Change password authentication')
        );

        $selecting->addLink(
            $this->request->derive('delete', $user, $serviceName)
            , _('Delete user')
        );

        $fieldset->addChild($selecting);

        return $fieldset;
    }
}
