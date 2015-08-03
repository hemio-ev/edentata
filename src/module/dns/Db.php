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

namespace hemio\edentata\module\dns;

use hemio\edentata\sql;

/**
 * Description of Db
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Db extends \hemio\edentata\ModuleDb
{

    public function adminC(array $params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'domain_reseller.upd_registered'
        , $params)
        )->execute();
    }

    public function customCreate(array $params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'dns.ins_custom'
        , $params)
        )->execute();
    }

    public function customDelete($recordId)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'dns.del_custom'
        , ['p_id' => $recordId])
        )->execute();
    }

    public function customSelect($registered)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo
            , 'dns.sel_custom');

        $stmt->options('WHERE registered = :registered');

        return $stmt->execute(['registered' => $registered]);
    }

    public function customUpdate($params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'dns.upd_custom'
        , $params)
        )->execute();
    }

    public function customSelectSingle($recordId)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo
            , 'dns.sel_custom');

        $stmt->options('WHERE id = :id');

        return $stmt->execute(['id' => $recordId]);
    }

    public function handleCreate($params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'domain_reseller.ins_handle'
        , $params)
        )->execute();
    }

    public function handleDelete($alias)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'domain_reseller.del_handle'
        , ['p_alias' => $alias])
        )->execute();
    }

    public function handleUpdate($params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'domain_reseller.upd_handle'
        , $params)
        )->execute();
    }

    public function handleSelect()
    {
        return (new sql\QuerySelectFunction(
            $this->pdo
            , 'domain_reseller.sel_handle')
            )->execute();
    }

    public function handleSelectSingle($alias)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo, 'domain_reseller.sel_handle');

        $stmt->options('WHERE alias = :alias');

        return $stmt->execute(['alias' => $alias]);
    }

    public function registeredCreate(array $params)
    {
        return (new sql\QuerySelectFunction(
            $this->pdo
            , 'dns.ins_registered'
            , $params
            )
            )->execute();
    }

    public function registeredDelete($domain)
    {
        return (new sql\QuerySelectFunction(
            $this->pdo
            , 'dns.del_registered'
            , ['p_domain' => $domain])
            )->execute();
    }

    public function registeredSelect()
    {
        return (new sql\QuerySelectFunction(
            $this->pdo
            , 'dns.sel_registered')
            )->execute();
    }

    public function registeredSelectSingle($domain)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo
            , 'dns.sel_registered'
        );

        $stmt->options('WHERE domain = :domain');

        return $stmt->execute(['domain' => $domain]);
    }

    public function registeredNameserverSelect()
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo
            , 'dns.sel_nameserver'
        );

        return $stmt->execute();
    }

    public function resellerRegisteredCreate(array $params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'domain_reseller.ins_registered'
        , $params
        ))->execute();
    }

    public function resellerRegisteredSelectSingle($domain)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo
            , 'domain_reseller.sel_registered'
        );

        $stmt->options('WHERE domain = :domain');

        return $stmt->execute(['domain' => $domain]);
    }

    public function serviceDomainSelect($registered)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo
            , 'dns.sel_service')
        ;

        $stmt->select(['domain']);
        $stmt->options('WHERE registered = :registered GROUP BY domain');

        return $stmt->execute(['registered' => $registered]);
    }

    public function serviceInsert($params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'dns.ins_service'
        , $params)
        )->execute();
    }

    public function serviceDelete($params)
    {
        (new sql\QuerySelectFunction(
        $this->pdo
        , 'dns.del_service'
        , $params)
        )->execute();
    }

    public function serviceSelect($domain)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo
            , 'dns.sel_service');

        $stmt->options('WHERE domain = :domain');

        return $stmt->execute(['domain' => $domain]);
    }

    public function activatableServiceSelect()
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo
            , 'dns.sel_activatable_service');

        $stmt->select(['service']);
        $stmt->options('GROUP BY service');

        return $stmt->execute();
    }

    public function activatableServiceNameSelect($service)
    {
        $stmt = new sql\QuerySelectFunction(
            $this->pdo
            , 'dns.sel_activatable_service');

        $stmt->select(['service_entity_name']);
        $stmt->options('WHERE service = :service');

        return $stmt->execute(['service' => $service]);
    }
}
