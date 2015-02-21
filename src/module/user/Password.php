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

namespace hemio\edentata\module\user;

use hemio\edentata\gui;
use hemio\form;
use hemio\edentata\exception\Successful;

/**
 * Description of UserCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Password extends Window {

    public function content() {
        $window = $this->newFormWindow(
                'password'
                , _('User Password')
                , null
                , _('Change')
        );

        $password = new gui\FieldNewPassword('password');


        $window->getForm()->addChild($password);

        $this->handleSubmit(
                $window->getForm()
        );

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form) {
        if ($form->correctSubmitted()) {

            $params = $form->getVal(['password']);

            $this->db->password($params);

            $e = new Successful(
                    _('Your password has been updated.')
            );

            $e->backTo = $this->request->derive();

            throw $e;
        }
    }

}
