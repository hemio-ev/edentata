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
use hemio\edentata\exception\Successful;

/**
 * Description of UserCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class UserPassword extends Window
{

    public function content($user, $serviceName)
    {
        $window = $this->newFormWindow(
            'user_password'
            , _('User Password')
            , $user.' @ '.$serviceName
            , _('Save')
        );

        $usePassword = new gui\FieldSwitch('use_password',
                                           _('Enable Password Login'));
        $usePassword->getControlElement()->setAttribute('checked', true);
        $usePassword->getControlElement()->addCssClass('display_control');
        $usePassword->getControlElement()->addCssClass('display_control_2');

        $password = new gui\FieldNewPassword('password');
        $password->getPassword()->setRequired(false);
        $password->getPasswordRepeat()->setRequired(false);

        $window->getForm()->addChild($usePassword);
        $window->getForm()->addChild($password);

        $this->handleSubmit(
            $window->getForm()
            , $usePassword
            , $password
            , $user
            , $serviceName
        );

        return $window;
    }

    protected function handleSubmit(
    gui\FormPost $form
    , gui\FieldSwitch $usePassword
    , gui\FieldNewPassword $password
    , $user
    , $serviceName
    )
    {
        if ($form->submitted()) {
            if (!$usePassword->getValueUser() || $password->dataValid()) {
                $params = [
                    'p_user' => $user,
                    'p_service_name' => $serviceName
                ];

                if ($usePassword->getValueUser())
                    $params += $form->getVal(['password']);

                $this->db->userPassword($params);

                $e = new Successful(
                    _('Your password authentification settings haven been updated.')
                );

                $e->backTo = $this->request->derive('details', true, true);

                throw $e;
            }
        }
    }
}
