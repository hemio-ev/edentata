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

namespace hemio\edentata\module\web;

use hemio\edentata\gui;
use hemio\html;
use hemio\form;
use hemio\edentata\exception;

/**
 * Description of SiteCreate
 *
 * @author Michael Herold <quabla@hemio.de>
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

        $details = new gui\Fieldset(_('Site Details'));

        $details->addChild(new gui\Output(_('Host'), $data['service_entity_name']));
        $user = $details->addChild(new gui\Output(_('User'), ''));

        $serverAccessRequest = $this->request
            ->deriveModule('server_access')
            ->derive('details', $data['user'], $data['service_entity_name']);


        $user['p']['output'][] = new gui\Link($serverAccessRequest,
                                              $data['user']);


        if ($data['https'] === null)
            $details->addChild(new gui\Output(_('HTTPS'), _('disabled')));
        else
            $details->addChild(new gui\Output(_('HTTPS'), _('enabled')));

        if (!Utils::defaultPort($port, $data['https']))
            $details->addChild(new gui\Output(_('Port'), $data['port']));

        if ($data['https'] === null)
            $windowHttps = new html\Nothing;
        else
            $windowHttps = $this->httpsWindow($data);

        $window->addChild($details);
        $window->addChild($this->aliases($domain, $port));

        $container->addChild($window);
        $container->addChild($windowHttps);

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
                new html\String($alias['domain'])
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

    protected function httpsWindow(array $site)
    {
        $domain = $site['domain'];
        $port   = $site['port'];

        $window = $this->newWindow(_('HTTPS'), null, false);

        $menu = new gui\HeaderbarMenu();
        $menu->addEntry(
            $this->request->derive(
                'intermediate_create', $domain.':'.$port, $site['https'])
            , _('Add intermediate certificates')
        );
        $window->addButtonRight($menu);

        $httpsDetails  = new HttpsDetails($this->module);
        $httpsCertInfo = $httpsDetails->https($domain, $port, $site['https']);

        $window->addChild($this->httpsActiveCert($site));
        $window->addChild($httpsCertInfo);
        $window->addChild($this->httpsAvailableCerts($domain, $port));

        return $window;
    }

    protected function httpsActiveCert(array $site)
    {
        $fieldset = new gui\Fieldset(_('Active Certificate'));

        $list = new gui\Listbox();
        $fieldset->addChild($list);

        $url = $this->request->derive('site_https', true);
        $list->addEntry(new html\String($site['https']), null,
                                        new gui\LinkButton($url, _('Change')));

        return $fieldset;
    }

    protected function httpsAvailableCerts($domain, $port)
    {
        $address  = $domain.':'.$port;
        $fieldset = new gui\Fieldset(_('Available HTTPS Configurations'));

        $selecting = new gui\Selecting();

        $selecting->addLink(
            $this->request->derive('https_create', $address)
            , _('New configuration (new SSL key an certificate)')
        );

        $certs = $this->db->httpsSelect($domain, $port)->fetchAll();

        $list = new gui\Listbox();
        foreach ($certs as $cert) {
            $url = $this->request->derive('https_details', $address,
                                          $cert['identifier']);

            $container   = new form\Container;
            $container[] = new html\String($cert['identifier']);
            $container[] = Utils::certSummary($domain, $port,
                                              $cert['identifier'], $this->db);

            $list->addLinkEntry($url, $container, $cert['backend_status']);
        }

        $fieldset->addChild($selecting);
        $fieldset->addChild($list);

        return $fieldset;
    }
}
