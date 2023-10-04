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

use hemio\edentata\sql;
use hemio\edentata\exception;

/**
 * Description of Db
 *
 * @author Sophie Herold <sophie@hemio.de>
 */
class Db extends \hemio\edentata\ModuleDb
{

    public function aliasCreate(array $params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'web.ins_alias'
        , $params
        ))->execute();
    }

    public function aliasDelete(array $params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'web.del_alias'
        , $params
        ))->execute();
    }

    public function aliasSelect($site, $sitePort)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo
            , 'web.sel_alias'
        );

        $stmt->options('WHERE site = :site AND site_port = :site_port');

        return $stmt->execute(['site' => $site, 'site_port' => $sitePort]);
    }

    public function userSelect()
    {
        return
            (new sql\QuerySelectFunction(
            $this->pdo
            , 'server_access.sel_user'
            ))->execute();
    }

    public function siteSelect()
    {
        return
            (new sql\QuerySelectFunction(
            $this->pdo
            , 'web.sel_site'
            ))->execute();
    }

    public function siteSelectSingle($domain, $port)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo
            , 'web.sel_site'
        );

        $stmt->options('WHERE domain = :domain AND port = :port');

        return $stmt->execute(['domain' => $domain, 'port' => $port]);
    }

    public function siteCreate(array $params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'web.ins_site'
        , $params
        ))->execute();
    }

    public function siteDelete(array $params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'web.del_site'
        , $params
        ))->execute();
    }

    public function availableDomainsWeb($port)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo, 'dns.sel_available_service'
        );
        $stmt->selectAs('t');
        $stmt->options('WHERE service = \'web\' AND '
            .'NOT EXISTS (SELECT TRUE FROM web.sel_site() AS s WHERE t.domain = s.domain AND s.port = :port) AND '
            .'NOT EXISTS (SELECT TRUE FROM web.sel_alias() AS s WHERE t.domain = s.domain AND s.site_port = :port)');


        return $stmt->execute(['port' => $port]);
    }

    public function availableDomainsNewSite()
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo, 'dns.sel_available_service'
        );
        $stmt->selectAs('t');
        $stmt->options('WHERE service = \'web\' AND '
            .'2 <> COALESCE((SELECT COUNT(*) FROM web.sel_site() AS s WHERE t.domain = s.domain AND s.port IN (80,443) GROUP BY s.domain),0) + '
            .'COALESCE((SELECT COUNT(*) FROM web.sel_alias() AS s WHERE t.domain = s.domain AND s.site_port IN (80,443) GROUP BY s.domain),0)');


        return $stmt->execute();
    }

    public function availableDomains(array $services)
    {
        if (count($services) > 1)
            throw new exception\Error('Multiple services not implemented.');

        $stmt = new sql\QuerySelectFunction(
            $this->pdo, 'dns.sel_available_service'
        );
        $stmt->selectAs('t');
        $stmt->options('WHERE service = :service AND '
            .'NOT EXISTS (SELECT TRUE FROM web.sel_site() AS s WHERE t.domain = s.domain) AND '
            .'NOT EXISTS (SELECT TRUE FROM web.sel_alias() AS s WHERE t.domain = s.domain)');


        return $stmt->execute(['service' => $services[0]]);
    }
}
