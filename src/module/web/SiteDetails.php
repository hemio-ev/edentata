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
class SiteDetails extends Window
{

    public function content($domain)
    {
        $window = $this->newWindow(_('Site'), $domain);

        $https = new gui\Fieldset(_('HTTPS'));

        $data = $this->db->siteSelectSingle($domain)->fetch();

        if ($data['https'] === null) {
            $https->addChild(new gui\Hint(_('HTTPS disabled')));
        } else {

            $https->addChild(new gui\Hint(sprintf(_('HTTPS identifier: %s'),
                                                    $data['https'])));
            $cert = $this->db->httpsSelect($domain)->fetch();

            if ($cert['x509_request'] === null) {
                $https->addChild(new gui\Hint(_('Waiting for Request …')));
            } elseif ($cert['x509_certificate'] === null) {
                #$https->addChild();
                $https->addChild(new gui\Hint(_('Waiting for Cert …')));
                $selecting = new gui\Selecting();
                $selecting->addLink($this->request->derive(
                        'https_cert', $domain, $data['https']),
                        _('Supply certificate'));
                $https->addChild($selecting);
            } else {
                $identifier = $data['https'];

                $selecting = new gui\Selecting();
                $selecting->addLink($this->request->derive(
                        'https_cert', $domain, $data['https']),
                        _('Change certificate'));
                $selecting->addLink($this->request->derive(
                        'intermediate_create', $domain, $data['https']),
                        _('Add intermediates'));

                $certData = new Cert($cert['x509_certificate']);

                $newChain = $certData->suggestChain($this->db);

                $this->db->beginTransaction();
                $this->db->intermediateChainDelete([
                    'p_domain' => $domain,
                    'p_identifier' => $identifier
                ]);

                foreach ($newChain as $subjectKeyIdentifier) {
                    $params = [
                        'p_domain' => $domain,
                        'p_identifier' => $identifier,
                        'p_subject_key_identifier' => $subjectKeyIdentifier
                    ];
                    $this->db->intermediateChainInsert($params);
                }
                $this->db->commit();

                $chain     = $this->db->intermediateChainSelect($domain,
                                                                $identifier)->fetchAll();
                $certChain = [];
                foreach ($chain as $intCert) {
                    $certChain[] = new Cert($intCert['x509_certificate']);
                }

                if ($certData->trusted($certChain))
                    $https->addChild(new gui\Hint(_('TRUSTED')));
                else
                    $https->addChild(new gui\Hint(_('UNTRUSTED')));

                $https->addChild(new gui\Hint(_('We have a cert')));
                $https->addChild($selecting);
                $https
                    ->addChild(new html\Pre)
                    ->addChild(new \hemio\html\String($certData->formatted()));
            }
        }

        $window->addChild($https);

        return $window;
    }

    protected function handleSubmit(
    gui\FormPost $form
    , $user
    , $serviceName
    , gui\FieldSwitch $switch)
    {
        if ($form->correctSubmitted()) {

        }
    }
}
