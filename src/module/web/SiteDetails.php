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
            $identifier = $data['https'];

            //$https->addChild(new gui\Hint(sprintf(_('HTTPS identifier: %s'),
            //                                        $data['https'])));
            $cert = $this->db->httpsSelect($domain, $identifier)->fetch();

            if ($cert['x509_request'] === null) {
                $status = new gui\StatusList();

                $status->addEntry(_('Waiting for certificate request of the webserver'),
                                    'error');

                $https->addChild($status);
            } elseif ($cert['x509_certificate'] === null) {
                $status = new gui\StatusList();

                $status->addEntry(_('Certificate request available'), 'ok');
                $status->addEntry(_('Waiting for HTTPS certificate'), 'error');

                $selecting = new gui\Selecting();
                $link      = $selecting->addLink($this->request->derive(
                        'https_cert', $domain, $data['https']),
                        _('Request certificate / supply HTTPS certificate'));
                $link->setSuggested();

                $https->addChild($status);
                $https->addChild($selecting);
            } else {
                $status = new gui\StatusList();

                $status->addEntry(_('Certificate request available'), 'ok');
                $status->addEntry(_('HTTPS certificate supplied'), 'ok');

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



                $https->addChild($status);
                $suggestIntermediate = false;
                if (!$certData->trusted($certChain)) {
                    $status->addEntry(
                        _('Certificate could NOT be verified to be trusted. Try adding the intermediate certificates of your trust authority.')
                        , 'error'
                    );
                    $suggestIntermediate = true;
                } else {
                    $status->addEntry(
                        _('Certificate is trusted (valid chain of trust)')
                        , 'ok'
                    );

                    $remaining = (new \DateTime())->diff($certData->validTo());

                    if ($remaining->invert)
                        $status->addEntry(
                            sprintf(_('Certificate has expired since %s days'),
                                      $remaining->days)
                            , 'error'
                        );

                    elseif ($remaining->days < 20)
                        $status->addEntry(
                            sprintf(_('Certificate is only valid for %s more days'),
                                      $remaining->days)
                            , 'warning'
                        );
                    else
                        $status->addEntry(
                            sprintf(_('Certificate is valid for another %s days'),
                                      $remaining->days)
                            , 'ok'
                        );
                }

                $selecting = new gui\Selecting();
                $selecting->addLink($this->request->derive(
                            'intermediate_create', $domain, $data['https']),
                            _('Add intermediate certificates'))
                    ->setSuggested($suggestIntermediate);

                $selecting->addLink($this->request->derive(
                        'https_cert', $domain, $data['https']),
                        _('Change HTTPS certificate'));

                $https->addChild($selecting);
            }
        }

        $window->addChild($https);

        return $window;
    }

    protected
        function handleSubmit(
    gui\FormPost $form
    , $user
    , $serviceName
    , gui\FieldSwitch $switch)
    {
        if ($form->correctSubmitted()) {

        }
    }
}
