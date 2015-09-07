<?php

    namespace Dez\Db;

    use Dez\Config\Config;

    /**
     * Class Connection
     * @package Dez\Db
     */
    class Connection extends \PDO implements ConnectionInterface {

        /**
         * @var int
         */
        protected $affectedRows     = 0;

        /**
         * @var null
         */
        static protected $config    = null;

        /**
         * @var null
         */
        static protected $schema    = null;

        /**
         * @param Config $config
         * @throws Exception
         */
        public function __construct( Config $config ) {

            $this->setConfig( $config );

            try {
                @ parent::__construct (
                    $config->get( 'dsn' ),
                    $config->get( 'user' ),
                    $config->get( 'password' ),
                    [
                        parent::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                    ]
                );
            } catch ( \Exception $e ) {
                throw new Exception( $e->getMessage() );
            }

            $this->setAttribute( static::ATTR_ERRMODE,            static::ERRMODE_EXCEPTION );
            $this->setAttribute( static::ATTR_CURSOR,             static::CURSOR_SCROLL );
            $this->setAttribute( static::ATTR_STATEMENT_CLASS,    [ __NAMESPACE__ .'\Stmt', [ $this ] ] );

            $this->initSchema();
        }

        /**
         * @param Config $config
         * @return $this
         */
        public function setConfig( Config $config ) {
            static::$config     = $config;
            return $this;
        }

        /**
         * @return Config
         */
        public function getConfig() {
            return static::$config;
        }

        /**
         * @return Schema
         */
        public function getSchema() {
            return static::$schema;
        }

        /**
         * @param null $query
         * @param array $params
         * @return Stmt
         */
        public function prepareQuery( $query = null, array $params = [] ) {
            $stmt = parent::prepare( $query );
            if( count( $params ) > 0 ) {
                $stmt->bindParams( $params );
            }
            return $stmt;
        }

        /**
         * @param null $query
         * @return $this
         * @throws Exception
         */
        public function execute( $query = null ) {
            try {
                $this->affectedRows = parent::exec( $query );
            } catch ( \Exception $e ) {
                throw new Exception( 'Error: ('. $e->getMessage() .') Query('. $query .')' );
            }
            return $this;
        }

        /**
         * @param null $query
         * @return Stmt
         * @throws Exception
         */
        public function query( $query = null ) {
            try {
                return parent::query( $query );
            } catch ( \Exception $e ) {
                throw new Exception( 'Error: ('. $e->getMessage() .') Query('. $query .')' );
            }
        }

        /**
         * @return int
         */
        public function affectedRows() {
            return $this->affectedRows;
        }

        /**
         * @param null $name
         * @return string
         */
        public function lastInsertId( $name = null ) {
            return parent::lastInsertId( $name );
        }

        /**
         * @return $this
         */
        public function transactionStart() {
            parent::beginTransaction();
            return $this;
        }

        /**
         * @return $this
         */
        public function commit() {
            parent::commit();
            return $this;
        }

        /**
         * @return $this
         */
        public function rollback() {
            parent::rollBack();
            return $this;
        }

        /**
         * @throws Exception
         */
        private function initSchema() {
            $schemaFile     = realpath( $this->getConfig()->get( 'schema' )->get( 'file' ) );
            try {
                static::$schema   = new Schema( $schemaFile );
            } catch( Exception $e ) {
                throw $e;
            }
        }

    }