<?php

    namespace Dez\Db;

    /**
     * Interface SchemaInterface
     * @package Dez\Db
     */
    interface SchemaInterface {

        /**
         * @param $tableName
         * @return mixed
         */
        public function getColumns ( $tableName );

        /**
         * @param $databaseName
         * @return mixed
         */
        public function getTables ( $databaseName );

        /**
         * @param $tableName
         * @return mixed
         */
        public function tableExist ( $tableName );

        /**
         * @param $tableName
         * @param $columnName
         * @return mixed
         */
        public function columnExist ( $tableName, $columnName );

    }