<?php
/*
 * Copyright (C) 2015 Sophie Herold <sophie@hemio.de>
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
 * Description of UserDelete
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class UserDelete extends Window
{

    public function content($user, $serviceName)
    {
        $message = _msg(
            _('Are you sure you want to delete the server access "{user}" at host "{host}"?')
            , ['user' => $user, 'host' => $serviceName]
        );

        $window = $this->newDeleteWindow(
            'user_delete'
            , _('Delete User')
            , $user.'@'.$serviceName
            , $message
            , _('Delete User')
            , true
        );

        $this->handleSubmit($window->getForm(), $user, $serviceName);

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form, $user, $serviceName)
    {
        if ($form->correctSubmitted()) {
            $params = [
                'p_user' => $user,
                'p_service_entity_name' => $serviceName
            ];

            $this->db->userDelete($params);

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
