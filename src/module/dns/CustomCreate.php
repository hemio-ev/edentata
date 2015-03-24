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
use hemio\edentata\exception;

/**
 * Description of CustomCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class CustomCreate extends Window
{
    const TYPE_KEYS = [
        'A' => ['address'],
        'AAAA' => ['address'],
        'CNAME' => ['cname'],
        'MX' => ['priority', 'exchange'],
        'SRV' => ['service', 'proto', 'priority', 'weight', 'port', 'target']
    ];

    public function content($domain, $type)
    {
        if (!$type)
            return $this->type($domain);
        else
            return $this->details($domain, $type);
    }

    protected function type($domain)
    {
        $window = $this->newWindow(
            _('New Custom DNS Record')
            , $domain, _('Create')
        );

        $types = [
            'A' => _('Host Address (IPv4)'),
            'AAAA' => _('Host Address (IPv6)'),
            'CNAME' => _('Canonical Name for an Alias'),
            'MX' => _('Mail Exchange'),
            'SRV' => _('Service Locator')
        ];

        $list = new gui\Listbox();

        foreach ($types as $type => $desc)
            $list->addLinkEntry(
                $this->request->derive(true, true, $type)
                , new html\String(sprintf('%s: %s', $type, $desc))
            );

        $fieldset = new gui\Fieldset(_('Record Type'));
        $fieldset->addChild($list);

        $window->addChild($fieldset);

        return $window;
    }

    protected function details($domain, $type)
    {
        $window = $this->newFormWindow(
            'custom_create'
            , sprintf(_('New DNS %s-Record'), $type)
            , $domain
            , _('Create')
        );

        $name = new form\FieldText('domain', _('Name (Domain)'));
        $name->setPlaceholder('sub-domain.'.$domain);
        $window->getForm()->addChild($name);

        $container = new form\Container();
        $container->addChild($this->formType($domain, $type));
        $window->getForm()->addChild($container);

        $filter = function ($child) {
            return $child instanceof \hemio\form\Abstract_\FormFieldDefault;
        };

        foreach ($container->getRecursiveIterator($filter) as $field)
            $field->setRequired();

        $ttl = new form\FieldNumber('ttl', _('Time to Live'));
        $window->getForm()->addChild($ttl);

        $this->handleSubmit($domain, $type, $window->getForm());

        return $window;
    }

    public function formType($domain, $type)
    {
        switch ($type) {
            case 'A':
                return $this->formA();

            case 'AAAA':
                return $this->formAaaa();

            case 'CNAME':
                return $this->formCname();

            case 'MX':
                return $this->formMx($domain);

            case 'SRV':
                return $this->formSrv($domain);

            default:
                throw new exception\Error(_('Unkown record type'));
        }
    }

    protected function handleSubmit($domain, $type, gui\FormPost $form)
    {
        if ($form->correctSubmitted()) {
            $keys   = self::TYPE_KEYS;
            $rdata  = json_encode($form->getVal($keys[$type], ''));
            $params = ['p_registered' => $domain, 'p_type' => $type, 'p_rdata' => $rdata]
                + $form->getVal(['domain', 'ttl']);

            if (!$params['p_ttl'])
                $params['p_ttl'] = null;

            $this->db->customCreate($params);

            throw new exception\Successful();
        }
    }

    protected function formA()
    {
        $address = new form\FieldText('address', _('Address (IPv4)'));
        $address->setPlaceholder('198.51.100.1');

        return $address;
    }

    protected function formAaaa()
    {
        $address = new form\FieldText('address', _('Address (IPv6)'));
        $address->setPlaceholder('2001:db8::1428:57ab');

        return $address;
    }

    protected function formCname()
    {
        $cname = new form\FieldText('cname', _('Cname'));
        $cname->setPlaceholder('foreign.example.com.');

        return $cname;
    }

    protected function formMx($domain)
    {
        $container = new form\Container();

        $exchange = new form\FieldText('exchange',
                                       _('Exchange (Mailserver Domain Name)'));
        $exchange->setPlaceholder('mail.'.$domain.'.');

        $priority = new form\FieldNumber('priority', _('Priority/Preference'));
        $priority->setDefaultValue(20);

        $hint = new gui\Hint(_('Lower priority values represent preferred records'));

        $container[] = $exchange;
        $container[] = $priority;
        $container[] = $hint;

        return $container;
    }

    protected function formSrv($domain)
    {
        $container = new form\Container();

        $service = new form\FieldText('service', _('Service'));
        $service->setPlaceholder('service-name');

        $proto = new form\FieldText('proto', _('Protocoll'));
        $proto->setPlaceholder('tcp');

        $priority = new form\FieldNumber('priority', _('Priority/Preference'));
        $priority->setDefaultValue(5);

        $weight = new form\FieldNumber('weight', _('Weight'));
        $weight->setDefaultValue(5);

        $port = new form\FieldNumber('port', _('Port'));

        $target = new form\FieldText('target', _('Target (Domain Name)'));
        $target->setPlaceholder('service.'.$domain.'.');

        $container[] = $service;
        $container[] = $proto;
        $container[] = $priority;
        $container[] = $weight;
        $container[] = $port;
        $container[] = $target;

        return $container;
    }
}
