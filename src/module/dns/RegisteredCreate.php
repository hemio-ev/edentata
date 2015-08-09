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
 * Description of RegisteredCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class RegisteredCreate extends Window
{

    public function content()
    {
        $window = $this->newFormWindow(
            'registered_create'
            , _('New Domain')
            , null
            , _('Register')
        );

        $domain = new \hemio\form\FieldText('domain', _('Domain'));
        $domain->setRequired();

        $subservice = new form\FieldSelect('subservice',
                                           _('Managed by this System'));

        $nameserver = new form\FieldSelect('service_entity_name',
                                           _('Nameserver'));

        $registrant = new form\FieldSelect('registrant', _('Registrant (Owner)'));
        $registrant->setRequired();

        $adminC = new form\FieldSelect('admin_c', _('Admin Contact'));
        $adminC->setRequired();

        $registrant->addOption('');
        $adminC->addOption('');

        foreach ($this->db->registeredNameserverSelect() as $ns) {
            $subservice->addOption($ns['subservice']);
            $nameserver->addOption($ns['service_entity_name']);
        }

        foreach ($this->db->handleSelect() as $handle) {
            $text = Utils::handleOut($handle);

            $registrant->addOption($handle['alias'], $text);
            $adminC->addOption($handle['alias'], $text);
        }

        $window->getForm()->addChild($domain);
        $window->getForm()->addChild($subservice);
        $window->getForm()->addChild($nameserver);
        $window->getForm()->addChild($registrant);
        $window->getForm()->addChild($adminC);

        $this->handleSubmit($window->getForm());

        return $window;
    }

    protected function handleSubmit(gui\FormPost $form)
    {
        if ($form->correctSubmitted()) {
            $this->db->beginTransaction();

            $paramsDns                    = $form->getVal(['domain']);
            $split                        = explode('.', $paramsDns['p_domain']);
            $paramsDns['p_public_suffix'] = array_pop($split);
            $paramsDns += $form->getVal(['subservice', 'service_entity_name']);

            $paramsReseller = $form->getVal(['domain', 'registrant', 'admin_c']);

            $this->db->registeredCreate($paramsDns);
            $this->db->resellerRegisteredCreate($paramsReseller);

            $this->db->commit();
            throw new \hemio\edentata\exception\Successful;
        }
    }
}
