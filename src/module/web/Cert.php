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

/**
 * X.509 Certificate
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Cert
{
    protected $formatted;
    protected $raw;
    protected $parsed;

    const BEGIN = '-----BEGIN CERTIFICATE-----';
    const END   = '-----END CERTIFICATE-----';

    public static function extract($str)
    {
        $pattern = '/'.self::BEGIN.'(?<cert>[a-zA-Z\d+=\/\s]+)'.self::END.'/s';
        $matches = [];
        preg_match_all($pattern, $str, $matches);

        if (count($matches['cert']) > 0) {
            $certs = [];

            foreach ($matches['cert'] as $cert) {
                $certs[] = new Cert(self::clean($cert));
            }
            return $certs;
        }

        return [new Cert(self::clean($str))];
    }

    public static function clean($str)
    {
        return preg_replace('/\s+/', '', $str);
    }

    public function __construct($certificate)
    {
        if ($certificate != self::clean($certificate))
            throw new \hemio\edentata\exception\Error('Expecting cleaned string');

        $this->formatted = self::BEGIN.PHP_EOL.chunk_split($certificate, 64,
                                                           PHP_EOL).self::END;
        $this->raw       = $certificate;

        $this->parsed = openssl_x509_parse($this->formatted, true);

        if (!$this->parsed) {
            throw new \hemio\edentata\exception\Error('Invalid Cert');
        }
    }

    /**
     *
     * @param string $hashAlg
     * @return string
     */
    public function fingerprint($hashAlg)
    {
        $str   = openssl_x509_fingerprint($this->formatted, $hashAlg, false);
        $chunk = trim(chunk_split($str, 2, ':'), ':');
        return strtoupper($chunk);
    }

    /**
     *
     * @return array
     */
    public function fingerprints()
    {
        return [
            'SHA-512' => $this->fingerprint('sha512'),
            'SHA-384' => $this->fingerprint('sha384'),
            'SHA-256' => $this->fingerprint('sha256'),
            'SHA-1' => $this->fingerprint('sha1'),
            'MD5' => $this->fingerprint('md5')
        ];
    }

    public function commonName()
    {
        return $this->parsed['subject']['CN'];
    }

    public function altNames()
    {
        $names = [];

        $entries = explode(',', $this->parsed['extensions']['subjectAltName']);
        foreach ($entries as $entry) {
            $keyVal  = explode(':', $entry);
            if (trim($keyVal[0]) === 'DNS')
                $names[] = trim($keyVal[1]);
        }

        return $names;
    }

    /**
     *
     * @return \DateTime
     */
    public function validFrom()
    {
        return new \DateTime('@'.$this->parsed['validFrom_time_t']);
    }

    /**
     *
     * @return \DateTime
     */
    public function validTo()
    {
        return new \DateTime('@'.$this->parsed['validTo_time_t']);
    }

    public function trusted(array $intermediate)
    {
        $itermediateFormatted = array_map(function (Cert $obj) {
            return $obj->formatted();
        }, $intermediate);
        $strInterm = implode(PHP_EOL, $itermediateFormatted);
        $resInterm = tmpfile();
        $pthInterm = stream_get_meta_data($resInterm)['uri'];
        fwrite($resInterm, $strInterm);

        $resCert = tmpfile();
        $pthCert = stream_get_meta_data($resCert)['uri'];
        fwrite($resCert, $this->formatted());

        $status = null;
        $stdout = '';
        exec('/usr/bin/openssl verify -untrusted "'.$pthInterm.'" "'.$pthCert.'"',
             $stdout, $status);

        return $status === 0;
    }

    public function authorityKeyIdentifier()
    {
        $str = $this->parsed['extensions']['authorityKeyIdentifier'];

        // extract from keyid:KEY,... format
        $csv = explode(',', $str);
        $key = explode(':', $csv[0], 2);

        return trim($key[1]);
    }

    public function subjectKeyIdentifier()
    {
        return $this->parsed['extensions']['subjectKeyIdentifier'];
    }

    public function raw()
    {
        return $this->raw;
    }

    public function formatted()
    {
        return $this->formatted;
    }

    public function suggestChain(Db $db)
    {
        $ident = $this->authorityKeyIdentifier();

        $chain = [];
        while ($next  = $db->intermediateCertSelect($ident)->fetch()) {
            $chain[] = new Cert($next['x509_certificate']);
            $ident   = $next['authority_key_identifier'];
        }

        return $chain;
    }
}
