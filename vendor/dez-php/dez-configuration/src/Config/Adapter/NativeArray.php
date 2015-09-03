<?php

    namespace Dez\Config\Adapter;

    use Dez\Config\Config;
    use Dez\Config\Exception;

    /**
     * Class NativeArray
     * @package Dez\Config\Adapter
     */
    class NativeArray extends Config {

        /**
         * @param string $filePath
         * @throws Exception
         */
        public function __construct( $filePath = '' ) {
            if( ! file_exists( $filePath ) ) {
                throw new Exception("Config file dont exists {$filePath}");
            }
            parent::__construct( require( $filePath ) );
        }

    }