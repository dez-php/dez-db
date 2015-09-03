<?php

    namespace Dez\Db;

    /**
     * Class Schema
     * @package Dez\Db
     */
    class Schema implements SchemaInterface {

        /**
         * @var null
         */
        /**
         * @var null
         */
        /**
         * @var null
         */
        /**
         * @var null
         */
        protected $databaseName   = null;
        protected $charset        = null;
        protected $tables         = [];
        protected $columns        = [];

        /**
         * @param null $schemaFile
         * @throws Exception
         */
        public function __construct( $schemaFile = null ) {
            if( ! file_exists( $schemaFile ) ) {
                throw new Exception( 'Schema file not found ( File: '. $schemaFile .' )' );
            }
            $xmlElement = simplexml_load_file( $schemaFile );
            $this->startLoadSchema( $xmlElement );
        }

        /**
         * @param $tableName
         * @return mixed
         */
        public function getTablePK( $tableName ) {
            static $pks = [];

            if( isset( $pks[$tableName] ) ) {
                return $pks[$tableName];
            }

            $pks[$tableName] = 'id';
            if( $this->tableExist( $tableName ) ) {
                foreach( $this->columns[$tableName] as $pkName => $column ) {
                    if( isset( $column['pk'] ) && $column['pk'] == 1 ) {
                        $pks[$tableName] = $pkName; break;
                    }
                }
            }

            return $pks[$tableName];
        }

        /**
         * @param $tableName
         * @return bool
         */
        public function getColumns ( $tableName ) {
            if( $this->tableExist( $tableName ) ) {
                return $this->columns[$tableName];
            } else {
                return false;
            }
        }

        /**
         * @param $databaseName
         * @return null
         */
        public function getTables ( $databaseName ) {
            return $this->tables;
        }

        /**
         * @param null $tableName
         * @return bool
         */
        public function tableExist ( $tableName = null ) {
            return in_array( $tableName, $this->tables );
        }

        /**
         * @param null $tableName
         * @param null $columnName
         * @return bool
         */
        public function columnExist ( $tableName = null, $columnName = null ) {
            return isset( $this->columns[$tableName], $this->columns[$tableName][$columnName] );
        }

        /**
         * @param \SimpleXMLElement $xml
         * @throws Exception
         */
        private function startLoadSchema( \SimpleXMLElement $xml ) {
            if( isset( $xml->{ 'database' } ) ) {
                $databaseNode = json_decode( json_encode( $xml->database ), true );

                if( isset( $databaseNode['@attributes'] ) ) {
                    $databaseAttrs      = & $databaseNode['@attributes'];
                    $this->databaseName = isset( $databaseAttrs['name'] )       ? $databaseAttrs['name'] : 'default-db';
                    $this->charset      = isset( $databaseAttrs['charset'] )    ? $databaseAttrs['charset'] : 'utf8';
                }

                if( isset( $databaseNode['table'] ) && isset( $databaseNode['table'] ) && count( $databaseNode['table'] ) > 0 ) {
                    $this->collectTables( $databaseNode['table'] );
                } else {
                    throw new Exception( 'Wrong schema node ( Not found node: table )' );
                }
            } else {
                throw new Exception( 'Wrong schema node ( Not found node: database )' );
            }
        }

        /**
         * @param array $tables
         * @throws Exception
         */
        private function collectTables( array $tables = [] ) {
            $tables = isset( $tables['@attributes'] ) ? [ $tables ] : $tables;
            foreach( $tables as $table ) {
                if( isset( $table['@attributes'] ) ) {
                    $this->tables[] = $table['@attributes']['name'];

                    if( isset( $table['column'] ) && isset( $table['column'] ) && ! empty( $table['column'] ) ) {
                        $this->collectColumns( $table['@attributes']['name'], $table['column'] );
                    } else {
                        throw new Exception( 'Not found columns for table: '. $table['@attributes']['name'] );
                    }
                } else {
                    throw new Exception( 'Unknown table name' );
                }
            }
        }

        /**
         * @param null $tableName
         * @param array $columns
         * @throws Exception
         */
        private function collectColumns( $tableName = null, array $columns = [] ) {
            if( $tableName ) {
                $this->columns[$tableName] = [];
                $columns = isset( $columns['@attributes'] ) ? [ $columns ] : $columns;
                foreach( $columns as $column ) {
                    if( isset( $column['@attributes'] ) ) {
                        $attrs                                      = $column['@attributes'];
                        $this->columns[$tableName][$attrs['name']]  = $attrs; unset( $attrs );
                    } else {
                        throw new Exception( 'Not set column properties' );
                    }
                }
            }
        }

    }