<?php

    namespace Dez\Config;

    /**
     * Interface ConfigInterface
     * @package Dez\Config
     */
    interface ConfigInterface extends \ArrayAccess, \IteratorAggregate, \Countable {

        /**
         * @param $name
         * @return mixed
         */
        public function get( $name );

        /**
         * @param ConfigInterface $config
         * @return $config ConfigInterface
         */
        public function merge( ConfigInterface $config );

    }