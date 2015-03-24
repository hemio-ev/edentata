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

/**
 * Description of AdminC
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class AdminC extends Window
{

    public function content($domain)
    {
        $window = $this->newFormWindow('adminc', _('Admin Contact'), $domain,
                                                   _('Change'));

        $adminc = new form\FieldSelect('admin_c', _('Admin Contact'));
        $window->getForm()->addChild($adminc);

        foreach ($this->db->handleSelect() as $handle) {
            $adminc->addOption($handle['alias'], Utils::handleOut($handle));
        }

        $data = $this->db->resellerRegisteredSelectSingle($domain)->fetch();
        $adminc->setDefaultValue($data['admin_c']);

        $this->handleSubmit($domain, $window->getForm());

        return $window;
    }

    protected function handleSubmit($domain, gui\FormPost $form)
    {
        if ($form->correctSubmitted()) {
            $params = ['p_domain' => $domain] + $form->getVal(['admin_c']);
            $this->db->adminC($params);


            throw new \hemio\edentata\exception\Successful;
        }
    }
}
