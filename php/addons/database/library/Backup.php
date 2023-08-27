<?php

namespace addons\database\library;

use Exception;
use fast\Random;
use PDO;
use ZipArchive;

class Backup
{

    private $host = '';
    private $user = '';
    private $name = '';
    private $pass = '';
    private $port = '';
    private $tables = ['*'];
    private $ignoreTables = [];
    private $db;
    private $ds = "\n";

    public function __construct($host = null, $user = null, $name = null, $pass = null, $port = 3306)
    {
        if ($host !== null) {
            $this->host = $host;
            $this->name = $name;
            $this->port = $port;
            $this->pass = $pass;
            $this->user = $user;
        }
        $this->db = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->name . '; port=' . $port, $this->user, $this->pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

        $this->db->exec('SET NAMES "utf8"');
    }

    /**
     * 设置备份表
     * @param $table
     * @return $this
     */
    public function setTable($table)
    {
        if ($table) {
            $this->tables = is_array($table) ? $table : explode(',', $table);
        }
        return $this;
    }

    /**
     * 设置忽略备份的表
     * @param $table
     * @return $this
     */
    public function setIgnoreTable($table)
    {
        if ($table) {
            $this->ignoreTables = is_array($table) ? $table : explode(',', preg_replace('/\s+/', '', $table));
        }
        return $this;
    }

    public function backup($backUpdir = 'download/')
    {
        $sql = $this->_init();
        $zip = new ZipArchive();
        $date = date('YmdHis');
        if (!is_dir($backUpdir)) {
            @mkdir($backUpdir, 0755);
        }
        $name = "backup-{$this->name}-{$date}-" . Random::alnum(6);
        $filename = $backUpdir . $name . ".zip";

        if ($zip->open($filename, ZIPARCHIVE::CREATE) !== true) {
            throw new Exception("Could not open <$filename>\n");
        }
        $zip->addFromString($name . ".sql", $sql);
        $zip->close();
    }

    private function _init()
    {
        # COUNT
        $ct = 0;
        # CONTENT
        $sqldump = '';
        # COPYRIGHT & OPTIONS
        $sqldump .= "-- SQL Dump by Erik Edgren\n";
        $sqldump .= "-- version 1.0\n";
        $sqldump .= "--\n";
        $sqldump .= "-- SQL Dump created: " . date('F jS, Y \@ g:i a') . "\n\n";
        $sqldump .= "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";";
        $sqldump .= "\n\n\n\n-- --------------------------------------------------------\n\n\n\n";
        $tables = $this->db->query("SHOW FULL TABLES WHERE Table_Type != 'VIEW'");
        # LOOP: Get the tables
        foreach ($tables AS $table) {
            // 忽略表
            if (in_array($table[0], $this->ignoreTables)) {
                continue;
            }
            # COUNT
            $ct++;
            /** ** ** ** ** **/
            # DATABASE: Count the rows in each tables
            $count_rows = $this->db->prepare("SELECT * FROM `" . $table[0] . "`");
            $count_rows->execute();
            $c_rows = $count_rows->columnCount();
            # DATABASE: Count the columns in each tables
            $count_columns = $this->db->prepare("SELECT COUNT(*) FROM `" . $table[0] . "`");
            $count_columns->execute();
            $c_columns = $count_columns->fetchColumn();
            /** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **/
            # MYSQL DUMP: Remove tables if they exists
            $sqldump .= "--\n";
            $sqldump .= "-- Remove the table if it exists\n";
            $sqldump .= "--\n\n";
            $sqldump .= "DROP TABLE IF EXISTS `" . $table[0] . "`;\n\n\n";
            /** ** ** ** ** **/
            # MYSQL DUMP: Create table if they do not exists
            $sqldump .= "--\n";
            $sqldump .= "-- Create the table if it not exists\n";
            $sqldump .= "--\n\n";
            # LOOP: Get the fields for the table
            foreach ($this->db->query("SHOW CREATE TABLE `" . $table[0] . "`") AS $field) {
                $sqldump .= str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $field['Create Table']);
            }
            # MYSQL DUMP: New rows
            $sqldump .= ";\n\n\n";
            /** ** ** ** ** **/
            # CHECK: There are one or more columns
            if ($c_columns != 0) {
                # MYSQL DUMP: List the data for each table
                $sqldump .= "--\n";
                $sqldump .= "-- List the data for the table\n";
                $sqldump .= "--\n\n";
                # MYSQL DUMP: Insert into each table
                $sqldump .= "INSERT INTO `" . $table[0] . "` (";
                # ARRAY
                $rows = [];
                $numeric = [];
                # LOOP: Get the tables
                foreach ($this->db->query("DESCRIBE `" . $table[0] . "`") AS $row) {
                    $rows[] = "`" . $row[0] . "`";
                    $numeric[] = (bool)preg_match('#^[^(]*(BYTE|COUNTER|SERIAL|INT|LONG$|CURRENCY|REAL|MONEY|FLOAT|DOUBLE|DECIMAL|NUMERIC|NUMBER)#i', $row[1]);
                }
                $sqldump .= implode(', ', $rows);
                $sqldump .= ") VALUES\n";
                # COUNT
                $c = 0;
                # LOOP: Get the tables
                foreach ($this->db->query("SELECT * FROM `" . $table[0] . "`") AS $data) {
                    # COUNT
                    $c++;
                    /** ** ** ** ** **/
                    $sqldump .= "(";
                    # ARRAY
                    $cdata = [];
                    # LOOP
                    for ($i = 0; $i < $c_rows; $i++) {
                        $value = $data[$i];

                        if (is_null($value)) {
                            $cdata[] = "NULL";
                        } elseif ($numeric[$i]) {
                            $cdata[] = $value;
                        } else {
                            $cdata[] = $this->db->quote($value);
                        }
                    }
                    $sqldump .= implode(', ', $cdata);
                    $sqldump .= ")";
                    $sqldump .= ($c % 600 != 0 ? ($c_columns != $c ? ',' : ';') : '');
                    # CHECK
                    if ($c % 600 == 0) {
                        $sqldump .= ";\n\n";
                    } else {
                        $sqldump .= "\n";
                    }
                    # CHECK
                    if ($c % 600 == 0) {
                        $sqldump .= "INSERT INTO `" . $table[0] . "`(";
                        # ARRAY
                        $rows = [];
                        # LOOP: Get the tables
                        foreach ($this->db->query("DESCRIBE `" . $table[0] . "`") AS $row) {
                            $rows[] = "`" . $row[0] . "`";
                        }
                        $sqldump .= implode(', ', $rows);
                        $sqldump .= ") VALUES\n";
                    }
                }
            }
        }

        $sqldump .= "\n\n\n";
        // Backup views
        $tables = $this->db->query("SHOW FULL TABLES WHERE Table_Type = 'VIEW'");
        # LOOP: Get the tables
        foreach ($tables AS $table) {
            // 忽略表
            if (in_array($table[0], $this->ignoreTables)) {
                continue;
            }
            foreach ($this->db->query("SHOW CREATE VIEW `" . $table[0] . "`") AS $field) {
                $sqldump .= "--\n";
                $sqldump .= "-- Remove the view if it exists\n";
                $sqldump .= "--\n\n";
                $sqldump .= "DROP VIEW IF EXISTS `{$field[0]}`;\n\n";
                $sqldump .= "--\n";
                $sqldump .= "-- Create the view if it not exists\n";
                $sqldump .= "--\n\n";
                $sqldump .= "{$field[1]};\n\n";
            }
        }
        return $sqldump;

    }

}
