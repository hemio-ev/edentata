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

namespace hemio\edentata\module\email_list;

use hemio\edentata\gui;
use hemio\form;

/**
 * Description of ListCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ListCreate extends Window {

    public function content() {
        $window = $this->newFormWindow(
                'list_create'
                , _('New Mailing List')
                , null
                , _('Create')
        );

        $address = new gui\FieldEmailWithSelect();
        $admin = new form\FieldEmail('admin', _('List Owner'));
        $admin->setRequired();

        $window->getForm()->addChild($address);
        $window->getForm()->addChild($admin);

        $hint = _('The address of the list owner is used for unsubscriber requests.');
        $window->getForm()->addChild(new gui\Hint($hint));


        foreach ($this->db->availableDomains(['email__list']) as $domain) {
            $address->getDomain()->addOption($domain['domain']);
        }

        $this->handleSubmit($window->getForm());

        return $window;
    }

    public function handleSubmit(gui\FormPost $form) {
        if ($form->correctSubmitted()) {
            $listParams = $form->getVal(['localpart', 'domain', 'admin']);

            // Add the list admin as initial subscriber
            $memeberParams = [
                'p_list_localpart' => $listParams['p_localpart'],
                'p_list_domain' => $listParams['p_domain'],
                'p_address' => $listParams['p_admin']
            ];

            $this->db->beginTransaction();
            $this->db->listCreate($listParams);
            $this->db->subscriberCreate($memeberParams);
            $this->db->commit();

            throw new \hemio\edentata\exception\Successful;
        }
    }

}
