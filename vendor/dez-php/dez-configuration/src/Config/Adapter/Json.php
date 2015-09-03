<?php

    namespace Dez\Config\Adapter;

    use Dez\Config\Config;
    use Dez\Config\Exception;

    /**
     * Class Json
     * @package Dez\Config\Adapter
     */
    class Json extends Config {

        /**
         * @param string $filePath
         * @throws Exception
         */
        public function __construct( $filePath = '' ) {
            if( ! file_exists( $filePath ) ) {
                throw new Exception( "Config file dont exists {$filePath}" );
            }
            parent::__construct( json_decode( file_get_contents( $filePath ), true ) );
        }

    }