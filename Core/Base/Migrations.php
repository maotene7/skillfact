<?php
/**
 * This file is part of FacturaScripts
 * Copyright (C) 2020-2021 Carlos Garcia Gomez <carlos@facturascripts.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Core\Base;

use FacturaScripts\Core\App\AppSettings;
use FacturaScripts\Dinamic\Model\FormatoDocumento;

/**
 * Description of Migrations
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
class Migrations
{

    public static function run()
    {
        static::fixCodagente();
        new FormatoDocumento();

        // sets IBAN validation to TRUE, if not defined
        $settings = new AppSettings();
        $settings->get('default', 'validate_iban', true);
        $settings->save();
    }

    private static function fixCodagente()
    {
        $dataBase = new DataBase();
        $tables = ['albaranescli', 'facturascli', 'pedidoscli', 'presupuestoscli'];
        foreach ($tables as $table) {
            if (false === $dataBase->tableExists($table)) {
                continue;
            }

            $sql = 'UPDATE ' . $table . ' SET codagente = NULL WHERE codagente IS NOT NULL'
                . ' AND codagente NOT IN (SELECT codagente FROM agentes);';
            $dataBase->exec($sql);
        }
    }
}
