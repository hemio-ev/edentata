<?php

/*
 * Copyright (C) 2017 Sophie Herold <sophie@hemio.de>
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

namespace hemio\edentata\module\dns;

use hemio\edentata\gui;

/**
 * Description of ServiceChooseDomain
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class ServiceChooseDomain extends Window {

    public function content($registered) {
        $window = $this->newWindow(_('Activate Service'));

        $selecting = new gui\Selecting(_('Activate Service for'));
        $window->addChild($selecting);

        $selecting->addLink(
                $this->module->request->derive('service_details', $registered, $registered)
                , _msg(_('Domain: {domain}'), ['domain' => $registered])
        )->setSuggested();

        $selecting->addLink(
                $this->module->request->derive('service_choose_subdomain', $registered)
                , _msg(_('Subdomain: <custom>.{domain}'), ['domain' => $registered])
        );

        return $window;
    }

}
