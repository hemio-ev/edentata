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

/**
 * Description of HandleDetails
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class HandleDetails extends Window
{

    public function content($alias)
    {
        $window = $this->newFormWindow(
            'handle_details'
            , _('Handle')
            , $alias
            , _('Update')
        );

        $menu = new gui\HeaderbarMenu();
        $menu->addEntry(
            $this->request->derive('handle_delete', true)
            , _('Delete Handle')
        );

        $window->addButtonRight($menu, false, true);

        $data = $this->db->handleSelectSingle($alias)->fetch();
        $window->getForm()->setStoredValues($data);

        $window->getForm()->addChild(new gui\Output(_('ID'), $data['id']));

        $helper = new HandleCreate($this->module);

        $window->getForm()->addChild($helper->handle(true));

        $this->handleSubmit($alias, $window->getForm());

        return $window;
    }

    protected function handleSubmit($alias, gui\FormPost $form)
    {
        if ($form->correctSubmitted()) {
            $params = $form->getVal(HandleCreate::HANDLE_KEYS);

            $params['p_alias'] = $alias;
            unset($params['p_service_entity_name']);
            unset($params['p_fname']);
            unset($params['p_lname']);

            $this->db->handleUpdate($params);

            throw new \hemio\edentata\exception\Successful();
        }
    }
}
