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

namespace hemio\edentata\module\web;

use hemio\edentata\gui;
use hemio\html;
use hemio\form;
use hemio\edentata\exception;

/**
 * Description of SiteCreate
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class SiteDetails extends Window
{

    public function content($address)
    {
        $domain = Utils::getHost($address);
        $port   = Utils::getPort($address);

        $container = new form\Container();

        $window = $this->newWindow(_('Website'), $address);

        $window->addButtonRight($this->actions($address));

        $data = $this->db->siteSelectSingle($domain, $port)->fetch();

        if (!$data)
            throw new exception\Error(_('Website does not exist.'));

        $window->addChild(new gui\Output(_('Host'), $data['service_entity_name']));


        $user = $window->addChild(new gui\Output(_('User'), ''));

        $serverAccessRequest = $this->request
            ->deriveModule('server_access')
            ->derive('details', $data['user'], $data['service_entity_name']);


        $user['p']['output'][] = new gui\Link($serverAccessRequest,
                                              $data['user']);


        if ($data['https'])
            $window->addChild(new gui\Output(_('HTTPS'), _('enabled')));
        else
            $window->addChild(new gui\Output(_('HTTPS'), _('disabled')));

        if (!Utils::defaultPort($port, $data['https']))
            $window->addChild(new gui\Output(_('Port'), $data['port']));

        $window->addChild($this->aliases($domain, $port));

        $container->addChild($window);

        return $container;
    }

    protected function actions($address)
    {
        $menu = new gui\HeaderbarMenu();

        $menu->addEntry(
            $this->request->derive('alias_create', $address), _('Create alias'));

        $menu->addEntry(
            $this->request->derive('site_delete', $address)
            , _('Delete entire site')
        );

        return $menu;
    }

    protected function aliases($domain, $port)
    {
        $fieldset = new gui\Fieldset(_('Aliases'));

        $listbox = new gui\Listbox();

        $aliases = $this->db->aliasSelect($domain, $port)->fetchAll();
        if (empty($aliases))
            return new html\Nothing ();

        foreach ($aliases as $alias) {
            $listbox->addEntry(
                new html\Str($alias['domain'])
                , $alias['backend_status']
                ,
                                new gui\LinkButton(
                $this->request->derive('alias_delete', $domain.':'.$port,
                                       $alias['domain'])
                , _('Delete')
            ));
        }

        $fieldset->addChild($listbox);

        return $fieldset;
    }

}
