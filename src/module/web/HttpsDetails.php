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

/**
 * Description of HttpsDetails
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class HttpsDetails extends Window
{

    public function content($address, $identifier)
    {
        $domain = Utils::getHost($address);
        $port   = Utils::getPort($address);

        $window = $this->newWindow(_('HTTPS Configuration'), $address);

        $window->addChild(new gui\Output(_('Identifier'), $identifier));
        $window->addChild($this->https($domain, $port, $identifier));

        return $window;
    }

    protected function tryFindingTrustChain($domain, $port, $identifier)
    {
        $cert = $this->db->httpsSelectSingle(
                $domain, $port, $identifier)->fetch();

        $certData = new Cert($cert['x509_certificate']);

        $newChain = $certData->suggestChain($this->db);

        if (!$certData->trusted($newChain))
            return;

        $this->db->beginTransaction();
        $this->db->intermediateChainDelete([
            'p_domain' => $domain,
            'p_port' => $port,
            'p_identifier' => $identifier
        ]);
        $i = 0;
        foreach ($newChain as $chainCert) {
            $params = [
                'p_domain' => $domain,
                'p_port' => $port,
                'p_identifier' => $identifier,
                'p_order' => $i++,
                'p_subject_key_identifier' => $chainCert->subjectKeyIdentifier()
            ];
            $this->db->intermediateChainInsert($params);
        }

        $this->db->commit();

        $e         = new \hemio\edentata\exception\Successful(
            _('The system has generated a valid chain of trust for your certificate.'
                .' It is now ready for use with your website.'));
        $e->backTo = $this->request->derive(true, true, true);

        throw $e;
    }

    public function https($domain, $port, $identifier)
    {
        $https = new gui\Fieldset(_('HTTPS Certificate Status'));

        $identifier = $identifier;

        $cert = $this->db->httpsSelectSingle(
                $domain, $port, $identifier)->fetch();

        $status     = Utils::certStatus($domain, $port, $identifier, $this->db);
        $statusList = new gui\StatusList();
        $selecting  = new gui\Selecting();

        if ($cert['x509_request'] !== null)
            $statusList->addEntry(_('Certificate request available'), 'ok');

        $suggestIntermediate = false;

        switch ($status['code']) {
            case 'no_config':
                $statusList->addEntryArray($status);
                break;

            case 'no_csr':
                $statusList->addEntryArray($status);
                break;

            case 'no_cert':
                $statusList->addEntryArray($status);

                $link = $selecting->addLink($this->request->derive(
                        'https_cert', $domain.':'.$port, $identifier),
                        _('Request certificate / Supply HTTPS certificate'));
                $link->setSuggested();

                break;

            case 'not_trusted':
                $this->tryFindingTrustChain($domain, $port, $identifier);
                $suggestIntermediate = true;
                $statusList->addEntryArray($status);
                break;

            case 'expired':
                $statusList->addEntryArray($status);
                break;

            case 'expiring':
                $statusList->addEntryArray($status);
                break;

            case 'ok':
                $statusList->addEntryArray($status);
                break;

            default:
                $statusList->addEntry('Unknown status!', 'error');
        }

        if ($status['cert']) {
            $diff = Utils::certUncoveredNames($domain, $port, $identifier,
                                              $this->db);
            if (!empty($diff))
                $statusList->addEntry(
                    sprintf(
                        _('The following names are not covered by the certificate: %s.')
                        , implode(', ', $diff)
                    )
                    , 'error'
                );
        }


        $https->addChild($statusList);

        $selecting->addLink($this->request->derive(
                    'intermediate_create', $domain.':'.$port, $identifier),
                    _('Add intermediate certificates'))
            ->setSuggested($suggestIntermediate);

        $https->addChild($selecting);

        return $https;
    }
}
