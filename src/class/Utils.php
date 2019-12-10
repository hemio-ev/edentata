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

namespace hemio\edentata;

/**
 * Description of Utils
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Utils
{

    public static function getIdnaErrorMessage($int)
    {
        switch ($int) {
            case IDNA_ERROR_EMPTY_LABEL:
                return _('IDNA_ERROR_EMPTY_LABEL');

            case IDNA_ERROR_LABEL_TOO_LONG:
                return _('IDNA_ERROR_LABEL_TOO_LONG');

            case IDNA_ERROR_DOMAIN_NAME_TOO_LONG:
                return _('IDNA_ERROR_DOMAIN_NAME_TOO_LONG');

            case IDNA_ERROR_LEADING_HYPHEN:
                return _('IDNA_ERROR_LEADING_HYPHEN');

            case IDNA_ERROR_TRAILING_HYPHEN:
                return _('IDNA_ERROR_TRAILING_HYPHEN');

            case IDNA_ERROR_HYPHEN_3_4:
                return _('IDNA_ERROR_HYPHEN_3_4');

            case IDNA_ERROR_LEADING_COMBINING_MARK:
                return _('IDNA_ERROR_LEADING_COMBINING_MARK');

            case IDNA_ERROR_DISALLOWED:
                return _('IDNA_ERROR_DISALLOWED');

            case IDNA_ERROR_PUNYCODE:
                return _('IDNA_ERROR_PUNYCODE');

            case IDNA_ERROR_LABEL_HAS_DOT:
                return _('IDNA_ERROR_LABEL_HAS_DOT');

            case IDNA_ERROR_INVALID_ACE_LABEL:
                return _('IDNA_ERROR_INVALID_ACE_LABEL');

            case IDNA_ERROR_BIDI:
                return _('IDNA_ERROR_BIDI');

            case IDNA_ERROR_CONTEXTJ:
                return _('IDNA_ERROR_CONTEXTJ');

            default:
                return _('Unkown IDNA Error');
        }
    }

    public static function idnToAscii($domain)
    {
        $idnaInfo = [];
        $ascii    = idn_to_ascii($domain
            ,
                                 IDNA_CHECK_BIDI | IDNA_CHECK_CONTEXTJ | IDNA_NONTRANSITIONAL_TO_ASCII
            | IDNA_DEFAULT
            , INTL_IDNA_VARIANT_UTS46, $idnaInfo);


        if ($ascii === false)
            throw new exception\Error(self::getIdnaErrorMessage($idnaInfo['errors']),
                                                                $idnaInfo['errors']);

        return $ascii;
    }

    public static function idnToUtf8($domain)
    {
        $idnaInfo = [];
        $utf8     = idn_to_utf8($domain,
                                IDNA_CHECK_BIDI | IDNA_CHECK_CONTEXTJ | IDNA_NONTRANSITIONAL_TO_UNICODE,
                                INTL_IDNA_VARIANT_UTS46, $idnaInfo);

        if ($utf8 === false)
            throw new exception\Error(self::getIdnaErrorMessage($idnaInfo['errors']),
                                                                $idnaInfo['errors']);

        return $utf8;
    }

    public static function idnKeepUtf8Bijection($domain)
    {
        if (self::idnToUtf8(self::idnToAscii($domain)) === $domain)
            return $domain;
        else
            return self::idnToAscii($domain);
    }

    public static function idnToUtf8Bijection($domain)
    {
        if (self::idnToAscii(self::idnToUtf8($domain)) === $domain)
            return self::idnToUtf8($domain);
        else
            return $domain;
    }

    public static function getPost()
    {
        $input = file_get_contents('php://input');

        if ($input === '')
            return [];

        $pairs = explode('&', $input);
        $post  = [];
        foreach ($pairs as $pair) {
            $nv          = explode('=', $pair);
            $name        = urldecode($nv[0]);
            $value       = urldecode($nv[1]);
            $post[$name] = $value;
        }

        return $post;
    }

    public static function htmlRedirect(Request $request)
    {
        global $config;
        header(
            sprintf('Location: %s', $config['base_url'].$request->getUrl())
            , true
            , 303
        );
        exit(0);
    }

    public static function sysExec($command, $pipe = '')
    {
        $descriptorspec = [
            0 => ['pipe', 'r'], // STDIN
            1 => ['pipe', 'w'] // STDOUT
        ];

        $pipes   = [];
        $process = proc_open($command, $descriptorspec, $pipes);

        if (!is_resource($process))
            throw new exception\Error('Failed to proc_open().');

        $status = proc_get_status($process);
        if (
            $status === false ||
            $status['running'] === false
        )
            throw new exception\Error('Process is not running.');

        fwrite($pipes[0], $pipe);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $returnStatus = proc_close($process);

        if ($returnStatus === -1) {
            throw new exception\Error('Internal error on proc_close().');
        } elseif ($returnStatus !== 0) {
            throw new exception\Error('External error on proc_close().');
        }

        return $stdout;
    }

    public static function fmtDate(\DateTime $date)
    {
        return strftime('%x', $date->getTimestamp());
    }

    public static function fmtDateTime(\DateTime $date)
    {
        return strftime('%x %X', $date->getTimestamp());
    }

    public static function getIso3166Countries()
    {
        $xml = simplexml_load_file('/usr/share/xml/iso-codes/iso_3166.xml');

        $countries = [];
        foreach ($xml->iso_3166_entry as $elem) {
            $code    = (string) $elem['alpha_2_code'];
            $country = (string) $elem['name'];

            $countries[$code] = $country;
        }

        return $countries;
    }
}
