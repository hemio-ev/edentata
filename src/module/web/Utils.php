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
 * Description of Utils
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Utils
{

    public static function getPort($str)
    {
        return explode(':', $str)[1];
    }

    public static function getHost($str)
    {
        return explode(':', $str)[0];
    }

    public static function defaultPort($port, $https)
    {
        return
            ($https !== null && $port == 443) ||
            ($https === null && $port == 80);
    }

    public static function certSummary($domain, $port, $identifier, Db $db)
    {
        $statusList = new gui\StatusList();

        $status = Utils::certStatus($domain, $port, $identifier, $db);
        $statusList->addEntryArray($status);

        if ($status['cert'] &&
            !empty(Utils::certUncoveredNames(
                    $domain, $port, $identifier, $db)))
            $statusList->addEntry(_('Not all names are covered by the certificate'),
                                    'error');

        return $statusList;
    }

    public static function certUncoveredNames(
    $domain, $port, $identifier, Db $db)
    {
        $aliases = $db->aliasSelect($domain, $port)->fetchAll();

        $names = [$domain];
        foreach ($aliases as $alias) {
            $names[] = $alias['domain'];
        }

        $cert     = $db->httpsSelectSingle(
                $domain, $port, $identifier)->fetch();
        $certData = new Cert($cert['x509_certificate']);

        $certNames = [$certData->commonName()] + $certData->altNames();

        return array_diff($names, $certNames);
    }

    public static function certStatus($domain, $port, $identifier, Db $db)
    {
        $cert = $db->httpsSelectSingle(
                $domain, $port, $identifier)->fetch();

        if ($cert === false)
            return [
                'status' => 'error',
                'text' => _('Certificate configuration does not exist'),
                'code' => 'no_config',
                'cert' => false
            ];

        if ($cert['x509_request'] === null && $cert['x509_certificate'] === null)
            return [
                'status' => 'error',
                'text' => _('Waiting for certificate signing request from webserver'),
                'code' => 'no_csr',
                'cert' => false
            ];

        if ($cert['x509_certificate'] === null)
            return [
                'status' => 'error',
                'text' => _('You must supply a HTTPS certificate'),
                'code' => 'no_cert',
                'cert' => false
            ];

        $certData = new Cert($cert['x509_certificate']);

        $chain = $db->intermediateChainSelect(
                $domain
                , $port
                , $identifier
            )->fetchAll();

        $certChain = [];
        foreach ($chain as $intCert) {
            $certChain[] = new Cert($intCert['x509_certificate']);
        }

        if (!$certData->trusted($certChain)) {
            return [
                'status' => 'error',
                'text' => _('Certificate could NOT be verified to be trusted'),
                'code' => 'not_trusted',
                'cert' => true
            ];
        } else {
            $remaining = (new \DateTime())->diff($certData->validTo());

            if ($remaining->invert)
                return [
                    'status' => 'error',
                    'text' =>
                    sprintf(_('Certificate has expired since %s days'),
                              $remaining->days),
                    'code' => 'expired',
                    'cert' => true
                ];

            elseif ($remaining->days < 30)
                return [
                    'status' => 'warning',
                    'text' => sprintf(
                        _('Certificate is only valid for %s more days'),
                          $remaining->days),
                    'code' => 'expiring',
                    'cert' => true
                ];
            else
                return [
                    'status' => 'ok',
                    'text' => sprintf(
                        _('Certificate is valid for another %s days'),
                          $remaining->days),
                    'code' => 'ok',
                    'cert' => true
                ];
        }
    }

    public static function shortHash($hash)
    {
        return substr($hash, 0, 14).' … '.substr($hash, -14);
    }
}
