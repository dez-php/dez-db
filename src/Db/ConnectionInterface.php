<?php

    namespace Dez\Db;

    /**
     * Interface ConnectionInterface
     * @package Dez\Db
     */
    interface ConnectionInterface {

        /**
         * @param null $query
         * @param array $params
         * @return mixed
         */
        public function prepareQuery( $query = null, array $params = [] );

        /**
         * @param null $query
         * @return mixed
         */
        public function execute( $query = null );

        /**
         * @return mixed
         */
        public function affectedRows();

        /**
         * @return mixed
         */
        public function transactionStart();

        /**
         * @return mixed
         */
        public function commit();

        /**
         * @return mixed
         */
        public function rollback();

    }
