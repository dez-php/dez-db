<?php

    namespace Dez\Config;

    use Dez\Config\Adapter\Ini as IniAdapter;
    use Dez\Config\Adapter\Json as JsonAdapter;
    use Dez\Config\Adapter\NativeArray as ArrayAdapter;

    /**
     * Class Config
     * @package Dez\Config
     */
    class Config implements ConfigInterface {

        /**
         * @var array
         */
        protected $config = [];

        /**
         * @param array $configArray
         */
        public function __construct( array $configArray = [] ) {
            foreach( $configArray as $key => $value )
                $this->offsetSet( $key, $value );
        }

        /**
         * @param $name
         * @return null
         */
        public function __get( $name ) {
            return $this->get( $name );
        }


        /**
         * @param string $filePath
         * @return IniAdapter|JsonAdapter|ArrayAdapter
         * @throws Exception
         */
        static public function fatory( $filePath = '' ) {

            list( $fileExtention )    = array_reverse( explode( '.', $filePath ) );

            switch ( $fileExtention ) {
                case 'json': {
                    return new JsonAdapter( $filePath );
                    break;
                }
                case 'php': {
                    return new ArrayAdapter( $filePath );
                    break;
                }
                case 'ini': {
                    return new IniAdapter( $filePath );
                    break;
                }
                default: {
                    throw new Exception( 'Unknown config type' );
                }
            }

        }

        /**
         * @param null $name
         * @param null $default
         * @return null|static
         */
        public function get( $name = null, $default = null ) {
            return $this->has( $name ) ? $this->config[$name] : $default;
        }

        /**
         * @param ConfigInterface $config
         * @return Config
         */
        public function merge( ConfigInterface $config ) {
            return $this->_merge( $config, $this );
        }

        /**
         * @param ConfigInterface $config
         * @param ConfigInterface $instance
         * @return ConfigInterface
         */
        protected function _merge( ConfigInterface $config, ConfigInterface $instance ) {

            foreach( $config as $key => $value ) {
                if( isset( $instance[ $key ] ) && is_object( $instance[ $key ] ) && is_object( $value ) ) {
                    $this->_merge( $value, $instance[ $key ] );
                } else {
                    $instance[ $key ]   = $value;
                }
            }

            return $instance;

        }

        /**
         * @return \stdClass
         */
        public function toObject() {

            $configObject   = new \stdClass();

            foreach( $this as $property => $value ) {
                $configObject->{$property}  = ( is_object( $value ) && $value instanceof ConfigInterface )
                    ? $value->toObject() : $value;
            }

            return $configObject;
        }

        /**
         * @return array
         */
        public function toArray() {
            return json_decode( json_encode( $this->toObject() ), true );
        }

        /**
         * @return string
         */
        public function toJSON() {
            return json_encode( $this->toObject(), JSON_PRETTY_PRINT );
        }

        /**
         * @param $name
         * @return bool
         */
        public function has( $name ) {
            return isset( $this->config[$name] );
        }

        /**
         * @return array
         */
        public function keys() {
            return array_keys( $this->config );
        }

        /**
         * @return int
         */
        public function count() {
            return count( $this->config );
        }

        /**
         * @param mixed $index
         * @return bool
         */
        public function offsetExists( $index ) {
            return $this->has( $index );
        }

        /**
         * @param mixed $index
         * @return null
         */
        public function offsetUnset( $index ) {
            unset( $this->config[$index] );
            return null;
        }

        /**
         * @param mixed $index
         * @return null
         */
        public function offsetGet( $index ) {
            return $this->get( $index );
        }

        /**
         * @param mixed $name
         * @param mixed $value
         * @return $this
         */
        public function offsetSet( $name, $value ) {
            $this->config[$name] = is_array( $value ) ? new self( $value ) : $value;
            return $this;
        }

        /**
         * @return \ArrayIterator
         */
        public function getIterator() {
            return new \ArrayIterator( $this->config );
        }

    }