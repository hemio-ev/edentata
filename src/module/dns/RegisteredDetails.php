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

namespace hemio\edentata\module\dns;

use hemio\edentata\gui;
use hemio\form;
use hemio\html;

/**
 * Description of RegisteredDetails
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class RegisteredDetails extends Window {

    public function content($registered) {
        $window = $this->newWindow(_('Domain'), $registered);

        $window->addButtonRight(new gui\LinkButton($this->request->derive('service_create', $registered), _('Add Sub-Domain')));

        $service = $this->db->serviceDomainSelect($registered)->fetchAll();

        $list = new gui\Listbox();
        foreach ($service as $domain) {
            $dom = $domain['domain'];
            $container = new form\Container;

            $container->addChild(new html\String($dom));

            $servicesActive = $this->db->serviceSelect($dom)->fetchAll();
            if (!empty($servicesActive)) {
                $ul = new html\Ul;
                $container->addChild($ul);

                foreach ($servicesActive as $act) {
                    $li = $ul->addLine();
                    $li->addChild(new html\String($act['service']));
                    $li->addChild(new gui\Progress($act['backend_status']));
                }
            }


            $list->addLinkEntry(
                    $this->request->derive(
                            'service_details'
                            , $registered
                            , $dom
                    )
                    , $container
            );
        }

        $window->addChild($list);

        return $window;
    }

}
