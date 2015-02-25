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
 * Description of CertRequest
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class CertRequest
{
    protected $formatted;
    protected $raw;
    protected $parsed;

    const BEGIN = '-----BEGIN CERTIFICATE REQUEST-----';
    const END   = '-----END CERTIFICATE REQUEST-----';

    public function __construct($request)
    {
        if ($request != Cert::clean($request))
            throw new \hemio\edentata\exception\Error('Expecting cleaned string');

        $this->formatted = self::BEGIN.PHP_EOL.chunk_split($request, 64, PHP_EOL).self::END;
        $this->raw       = $request;
        $this->parsed    = openssl_csr_get_subject($this->formatted, true);

        if (!$this->parsed) {
            throw new \hemio\edentata\exception\Error('Invalid Cert');
        }
    }

    public function commonName()
    {
        return $this->parsed['CN'];
    }

    public function formatted()
    {
        return $this->formatted;
    }
}
