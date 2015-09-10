<?php

    namespace Dez\Db;

    class Stmt extends \PDOStatement implements StmtInterface {

        protected
            $connection     = null;

        protected function __construct( Connection $connection ) {
            $this->connection   = $connection;
        }

        public function bindParam( $parameter = null, & $value = null, $type = null, $maxLength = null, $driverData = null ) {
            parent::bindParam( $parameter, $value, $type );
            return $this;
        }

        public function bindParams( array $params = [] ) {
            foreach( $params as $parameter => & $value ) {
                $this->bindParam( ( is_numeric( $parameter ) ? $parameter + 1 : $parameter ), $value, Connection::PARAM_STR );
            }
            return $this;
        }

        public function bindValue( $parameter = null, $value = null, $type = null ) {
            parent::bindValue( $parameter, $value, $type );
            return $this;
        }

        public function multiBind( array $params = [] ) {
            foreach( $params as $parameter => $value ) {
                $this->bindValue( ( is_numeric( $parameter ) ? $parameter + 1 : $parameter ), $value, Connection::PARAM_STR );
            }
            return $this;
        }

        public function execute( $params = [] ) {
            if( count( $params ) > 0 ) {
                $this->bindParams( $params );
            }
            parent::execute();
            return $this;
        }

        public function numRows() {
            return parent::rowCount();
        }

        public function loadArray() {
            return $this->_fetch( Connection::FETCH_ASSOC );
        }

        public function loadNum() {
            return $this->_fetch( Connection::FETCH_NUM );
        }

        public function loadObject() {
            return $this->_fetch( Connection::FETCH_OBJ );
        }

        public function loadColumn() {
            return $this->_fetch( Connection::FETCH_COLUMN );
        }

        public function loadIntoObject( $target = '\stdClass' ) {
            if( is_string( $target ) && class_exists( $target ) ) {
                $object = new $target();
            } else if( is_object( $target ) ) {
                $object = $target;
            } else {
                throw new Exception( 'Class not found ('. $target .') for row' );
            }

            $row = $this->loadObject();
            if( ! $row ) return null;

            foreach( $row as $key => $value ) {
                $object->$key = $value;
            }

            return $object;
        }

        public function _fetch( $how = null ) {
            return parent::fetch( $how );
        }

    }