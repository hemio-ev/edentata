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

use \hemio\form;
use hemio\edentata\gui;

/**
 * Description of HandleCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class HandleCreate extends Window
{
    const HANDLE_KEYS = [
        'service_entity_name',
        'alias',
        'fname',
        'lname',
        'organization',
        'address',
        'pcode',
        'city',
        'country',
        'state',
        'email',
        'phone',
        'fax',
        'mobile_phone'
    ];

    public function content()
    {
        $window = $this->newFormWindow('handle_create', _('New Handle'), null,
                                                          _('Create'));
        $window->getForm()->addChild($this->handle());

        $this->handleSubmit($window->getForm());

        return $window;
    }

    public function handle($update = false)
    {
        $required                 = new form\Container();
        $required['registrar']    = new form\FieldSelect('service_entity_name',
                                                         _('Registar'));
        $required['alias']        = new form\FieldText('alias', _('Alias'));
        $required['fname']        = new form\FieldText('fname', _('First Name'));
        $required['lname']        = new form\FieldText('lname', _('Last Name'));
        $required['organization'] = new form\FieldText('organization',
                                                       _('Organization'));
        $required[]               = new form\FieldTextarea('address',
                                                           _('Address'));
        $required[]               = new form\FieldText('pcode', _('Zip Code'));
        $required[]               = new form\FieldText('city', _('City'));

        $required['country'] = new form\FieldSelect('country', _('Country'));
        $required['country']->addOption('');
        foreach (\hemio\edentata\Utils::getIso3166Countries() as $key => $val)
            $required['country']->addOption($key, $val);

        $required[]        = new form\FieldText('state', _('State'));
        $required[]        = new form\FieldEmail('email', _('Email'));
        $required['phone'] = new form\FieldTel('phone', _('Phone'));
        $required['phone']->setPlaceholder('+49-3-11');

        foreach ($required as $field)
            $field->setRequired();

        $required['organization']->setRequired(false);

        $required['alias']->setPlaceholder(_('First Name-Last Name'));

        foreach ($this->db->resellerResellerSelect() as $registar)
            $required['registrar']->addOption($registar['service_entity_name']);

        if ($update) {
            $required['registrar']->getControlElement()->setAttribute('readonly',
                                                                      true);
            $required['registrar']->getControlElement()->setAttribute('disabled',
                                                                      true);
            $required['registrar']->setRequired(false);
            $required['alias']->getControlElement()->setAttribute('readonly',
                                                                  true);
            $required['fname']->getControlElement()->setAttribute('readonly',
                                                                  true);
            $required['lname']->getControlElement()->setAttribute('readonly',
                                                                  true);
        }

        $optional = new form\Container();

        $optional['fax'] = new form\FieldTel('fax', _('Fax'));
        $optional['fax']->setPlaceholder('+49-3-10');

        $optional['mobile_phone'] = new form\FieldText('mobile_phone',
                                                       _('Mobile Phone'));
        $optional['mobile_phone']->setPlaceholder('+49-3-11');


        $container   = new form\Container();
        $container[] = $required;
        $container[] = $optional;

        return $container;
    }

    protected function handleSubmit(gui\FormPost $form)
    {
        if ($form->correctSubmitted()) {
            $params = $form->getVal(self::HANDLE_KEYS);

            $this->db->handleCreate($params);

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
