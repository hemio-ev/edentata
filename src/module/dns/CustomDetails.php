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

use hemio\form;
use hemio\edentata\gui;
use hemio\edentata\exception;

/**
 * Description of CustomDetails
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class CustomDetails extends Window
{

    public function content($domain, $recordId)
    {
        $window = $this->newFormWindow(
            'custom_details'
            , _('Custom DNS Record')
            , null
            , _('Update')
        );

        $menu = new gui\HeaderbarMenu();
        $menu->addEntry(
            $this->request->derive('custom_delete', $domain, $recordId)
            , _('Delete Record')
        );
        $window->addButtonRight($menu, false, true);

        $raw = $this->db->customSelectSingle($recordId)->fetch();

        $data = $raw + (array) json_decode($raw['rdata']);

        if (isset($data['txtdata']))
            $data['txtdata'] = implode('', $data['txtdata']);

        $window->getForm()->setStoredValues($data);

        $x = (new CustomCreate($this->module))->formType($domain, $data['type']);

        $name = new gui\Output(_('Name (Domain)'), $data['domain']);
        $window->getForm()->addChild($name);

        $type = new gui\Output(_('Type'), $data['type']);
        $window->getForm()->addChild($type);

        $window->getForm()->addChild($x);

        $ttl = new form\FieldNumber('ttl', _('Time to Live'));
        $window->getForm()->addChild($ttl);

        $this->handleSubmit($recordId, $data['type'], $window->getForm());

        return $window;
    }

    protected function handleSubmit($recordId, $type, gui\FormPost $form)
    {
        if ($form->correctSubmitted()) {
            $rdata = CustomCreate::getRdata($type, $form);

            $params = ['p_id' => $recordId, 'p_rdata' => $rdata] + $form->getVal(['ttl']);

            if (!$params['p_ttl'])
                $params['p_ttl'] = null;

            $this->db->customUpdate($params);

            throw new exception\Successful();
        }
    }
}
