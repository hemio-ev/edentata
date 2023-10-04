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

/**
 * Description of UserService
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class UserService extends Window {

    public function content() {
        $window = $this->newWindow(_('Create User'));

        $selecting = new \hemio\edentata\gui\Selecting(
                _('Choose Access Type')
        );

        $selecting->addLink(
                $this->request->derive('create', 'sftp')
                , _('SFTP: File transfer via SSH')
        )->setSuggested();

        $selecting->addLink(
                $this->request->derive('create', 'ssh')
                , _('SSH: Full shell access')
        );
        
        $window->addChild($selecting);

        return $window;
    }

}
