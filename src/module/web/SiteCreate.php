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

/**
 * Description of SiteCreate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class SiteCreate extends Window
{

    public function content($user, $serviceName)
    {
        if (!$user || !$serviceName)
            return $this->chooseServerAccess();
        else
            return $this->siteCreate($user, $serviceName);
    }

    protected function chooseServerAccess()
    {
        $window = $this->newWindow(_('New Site'), _('Choose Server Access'));

        $users = $this->db->userSelect()->fetchAll();

        if (empty($users)) {
            $window->addChild(
                new gui\Hint(
                _('You need to create a server access first.')
                )
            );
        } elseif (count($users) === 1) {
            $data = array_pop($users);
            \hemio\edentata\Utils::htmlRedirect(
                $this->request->derive(
                    true
                    , $data['user']
                    , $data['service_entity_name']
                )
            );
        } else {
            $window->addChild(
                new gui\Hint(
                _('Select a server access under which to construct the new site')
                )
            );
            $listbox = new gui\Listbox();
            foreach ($users as $user) {
                $container = new form\Container();

                $container->addChild(new html\Str($user['user']));

                $ul = new html\Ul();
                $container->addChild($ul);

                $ul->addLine(new html\Str(
                    sprintf(_('Host: %s'), $user['service_entity_name'])));

                $listbox->addLinkEntry(
                    $this->request->derive(
                        true
                        , $user['user']
                        , $user['service_entity_name']
                    )
                    , $container
                );
            }
            $window->addChild($listbox);
        }

        return $window;
    }

    protected function siteCreate($user, $serviceName)
    {
        $window = $this->newFormWindow(
            'site_create'
            , _('New Site')
            , $user.' @ '.$serviceName
            , _('Create')
        );

        $domain = new \hemio\form\FieldSelect('domain', _('Domain'));

        $httpsSwitch = new gui\FieldSwitch('https-enabled', _('Enable HTTPS'));
        $httpsSwitch->getControlElement()->addCssClass('display_control');

        $identifier = new form\FieldText('identifier',
                                         _('Certificate Identifier'));
        $identifier->setDefaultValue((new \DateTime)->format('Y'));

        $hint = new gui\Hint(_('The certificate identifier helps you to mange your certificates. It can be chosen arbitrarily.'));

        $https = new html\Div();

        $window->getForm()->addChild($domain);
        $window->getForm()->addChild($httpsSwitch);

        $https->addChild($identifier);
        $https->addChild($hint);

        $window->getForm()->addChild($https);


        $domains = $this->db->getUsableDomains('web', 'site')->fetchAll();
        foreach ($domains as $data) {
            $domain->addOption($data['domain']);
        }

        $this->handleSubmit($window->getForm(), $user, $serviceName,
                            $httpsSwitch);

        return $window;
    }

    protected function handleSubmit(
    gui\FormPost $form
    , $user
    , $serviceName
    , gui\FieldSwitch $httpsSwitch)
    {
        if ($form->correctSubmitted()) {
            if ($httpsSwitch->getValueUser())
                $port = 443;
            else
                $port = 80;

            $siteParams = [
                'p_port' => $port,
                'p_user' => $user,
                'p_service_entity_name' => $serviceName
                ] + $form->getVal(['domain']);

            $httpsParams      = ['p_port' => $port] + $form->getVal(['domain', 'identifier']);
            $siteUpdateParams = ['p_port' => $port] + $form->getVal(['domain', 'identifier']);

            $this->db->beginTransaction();

            $this->db->siteCreate($siteParams);

            if ($httpsSwitch->getValueUser()) {
                $this->db->httpsCreate($httpsParams);
                $this->db->siteUpdate($siteUpdateParams);
            }

            $this->db->commit();

            throw new \hemio\edentata\exception\Successful;
        }
    }
}
