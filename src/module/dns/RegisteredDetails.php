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

namespace hemio\edentata\module\dns;

use hemio\edentata\gui;
use hemio\form;
use hemio\html;
use hemio\edentata\Utils;

/**
 * Description of RegisteredDetails
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class RegisteredDetails extends Window
{

    public function content($registered)
    {
        $window = $this->newWindow(_('Domain'), $registered);

        $menu = new gui\HeaderbarMenu();
        $menu->addEntry(
            $this->request->derive('custom_create', true)
            , _('Create custom DNS record')
        );
        $menu->addEntry(
            $this->request->derive('registered_delete', true)
            , _('Delete registered domain')
        );

        $window->addButtonRight($menu);

        $window->addButtonRight(
            new gui\LinkButton(
            $this->request->derive(
                'service_choose_domain'
                        , $registered
            )
            , _('Activate Service')
            )
        );

        $window->addChild($this->domain($registered));

        $window->addChild($this->service($registered));

        $custom = $this->custom($registered);
        if (!($custom instanceof html\Nothing))
            $window
                ->addChild(new gui\Fieldset(_('Custom DNS Entries')))
                ->addChild($custom);

        return $window;
    }

    protected function domain($registered)
    {
        $fieldset = new gui\Fieldset(_('Domain Details'));

        $dataRegist = $this->db->resellerRegisteredSelectSingle(Utils::idnToAscii($registered))->fetch();
        $dataDomain = $this->db->registeredSelectSingle(Utils::idnToAscii($registered))->fetch();

        if ($dataDomain['subservice'] == 'unmanaged') {
            $fieldset->addChild(
                new gui\Hint(_('Domain not managed via this system.')));
        } else {
            $fieldset->addChild(
                new gui\Output(_('Primary Nameserver')
                , $dataDomain['service_entity_name']));

            if ($dataRegist['payable'])
                $fieldset[] = new gui\Output(_('Payable')
                    , $dataRegist['payable']);

            if ($dataRegist['period'])
                $fieldset[] = new gui\Output(_('Booking Period (Years)')
                    , $dataRegist['period']);

            if ($dataRegist['registrar_status'])
                $fieldset[] = new gui\Output(_('Registrar Status')
                    , $dataRegist['registrar_status']);

            if ($dataRegist['registry_status'])
                $fieldset[] = new gui\Output(_('Registry Status (Transfer Protection)')
                    , $dataRegist['registry_status']);

            if ($dataRegist['last_status'])
                $fieldset[] = new gui\Output(_('Last Update Status')
                    , $dataRegist['last_status']);

            $list = new gui\Listbox();

            if ($dataRegist === false)
                return new gui\Hint(_('No detailed domain information available'));

            $list->addEntry(new gui\Fstr('%s: %s',
                                         [_('Registrant (Owner)'),
                $dataRegist['registrant']]));
            $list->addEntry(
                new gui\Fstr('%s: %s',
                             [_('Admin Contact'), $dataRegist['admin_c']])
                , $dataRegist['backend_status']
                ,
                                new gui\LinkButton(
                $this->request->derive('adminc', $registered)
                , _('Change')
            ));

            $fieldset->addChild($list);
        }

        return $fieldset;
    }

    protected function service($registered)
    {
        $fieldset = new gui\Fieldset(_('Activated Services'));

        $list = new gui\Listbox();
        foreach ($this->db->serviceDomainSelect(Utils::idnToAscii($registered)) as $domain) {
            $dom       = $domain['domain'];
            $domUtf8   = Utils::idnToUtf8Bijection($dom);
            $container = new form\Container;

            $container->addChild(new html\Str($domUtf8));

            $servicesActive = $this->db->serviceSelect($dom)->fetchAll();
            if (!empty($servicesActive)) {
                $ul = new html\Ul;
                $container->addChild($ul);

                foreach ($servicesActive as $act) {
                    $li = $ul->addLine();
                    $li->addChild(new html\Str($act['service']));
                    $li->addChild(new gui\Progress($act['backend_status']));
                }
            }


            $list->addLinkEntry(
                $this->request->derive(
                    'service_details'
                    , $registered
                    , $domUtf8
                )
                , $container
            );
        }

        if (!count($list))
            return new html\Nothing;

        $fieldset->addChild($list);

        return $fieldset;
    }

    protected function custom($registered)
    {
        $service = $this->db->customSelect(Utils::idnToAscii($registered))->fetchAll();

        if (empty($service))
            return new html\Nothing;

        $list = new gui\Listbox();
        foreach ($service as $record) {
            $domain = Utils::idnToUtf8Bijection($record['domain']);
            $span   = new html\Span;
            $ul     = new html\Ul();

            $rdata = (array) json_decode($record['rdata']);
            ksort($rdata);
            foreach ($rdata as $key => $value) {
                $ul->addLine(new html\Str(sprintf('%s = %s', $key,
                                                  print_r($value, true))));
            }

            $span->addChild(new html\Str($domain.' '.$record['type']));
            $span->addChild($ul);

            $list->addLinkEntry(
                $this->request->derive(
                    'custom_details'
                    , $registered
                    , $record['id']
                )
                , $span
                , $record['backend_status']
            );
            $span->setCssProperty('font-family', 'monospace');
        }

        return $list;
    }
}
