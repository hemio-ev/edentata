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
use hemio\form;

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

        if (!$userData)
            throw new \hemio\edentata\exception\Error('Server access not found.');

        $menu = $window->addHeaderbarMenu();

        $menu->addEntry(
            $this->request->derive('password', $user, $serviceName)
            , _('Change password authentication')
        );

        $menu->addEntry(
            $this->request->derive('delete', $user, $serviceName)
            , _('Delete server access')
        );

        $window->addChild($this->basics($userData));
        $window->addChild($this->details($userData));

        return $window;
    }

    protected function basics($userData)
    {
        $container = new form\Container();

        $container->addChild(new gui\OutputStatus($userData));

        $container->addChild(
            new gui\Output(_('User'), $userData['user'])
        );

        $container->addChild(
            new gui\Output(_('Host'), $userData['service_entity_name'])
        );

        return $container;
    }

    protected function details($userData)
    {
        if ($userData['password_login'])
            $status = _('Enabled');
        else
            $status = _('Disabled');

        $proto = $userData['subservice'];

        $fieldset = new gui\Fieldset(_('Details'));

        $fieldset->addChild(
            new gui\Output(_('Communication Protocol'), strtoupper($proto))
        );

        $fieldset->addChild(
            new gui\Output(_('Password Authentication'), $status)
        );

        $fieldset->addChild(
            new gui\OutputUrl(
            _('Connection URI')
            ,
              sprintf(
                '%s://%s@%s'
                , $proto
                , $userData['user']
                , $userData['service_entity_name']
            )
            )
        );

        return $fieldset;
    }
}
