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
 * Description of UserCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class UserCreate extends Window {

    public function content($service) {
        $window = $this->newFormWindow('user_create', _('New User'), $service, _('Create'));

        $user = new form\FieldText('user', _('User Name'));
        $user->setRequired();

        $usePassword = new gui\FieldSwitch('use_password', _('Enable Password Login'));
        $usePassword->getControlElement()->setAttribute('checked', true);
        $usePassword->getControlElement()->addCssClass('display_control');
        $usePassword->getControlElement()->addCssClass('display_control_2');

        $password = new gui\FieldNewPassword('password');
        $password->getPassword()->setRequired(false);
        $password->getPasswordRepeat()->setRequired(false);

        $serviceName = new form\FieldSelect('service_entity_name', _('Host/Server'));

        $activatableServices = $this->db->activatableServiceSelect($service)->fetchAll();
        foreach ($activatableServices as $serv) {
            $serviceName->addOption($serv['service_entity_name']);
        }

        $window->getForm()->addChild($serviceName);
        $window->getForm()->addChild($user);
        $window->getForm()->addChild($usePassword);
        $window->getForm()->addChild($password);

        $this->handleSubmit(
                $window->getForm()
                , $serviceName
                , $user
                , $usePassword
                , $password
                , $service
        );

        return $window;
    }

    protected function handleSubmit(
    gui\FormPost $form
    , form\FieldSelect $serviceName
    , form\FieldText $user
    , gui\FieldSwitch $usePassword
    , gui\FieldNewPassword $password
    , $service
    ) {
        if (
                $form->submitted() &&
                $user->dataValid() &&
                $serviceName->dataValid()
        ) {
            if (!$usePassword->getValueUser() || $password->dataValid()) {
                $params = $form->getVal(['user', 'service_entity_name']);
                $params['p_service'] = $service;

                if ($usePassword->getValueUser())
                    $params += $form->getVal(['password']);

                $this->db->userCreate($params);

                throw new \hemio\edentata\exception\Successful;
            }
        }
    }

}
