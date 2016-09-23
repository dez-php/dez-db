<?php

namespace Dez\Db;

use Dez\Config\Config;

/**
 * Class Connection
 * @package Dez\Db
 */
class Connection extends \PDO implements ConnectionInterface
{

  /**
   * @var int
   */
  protected $affectedRows = 0;

  /**
   * @var null
   */
  static protected $config = null;

  /**
   * @param Config $config
   * @throws DbException
   */
  public function __construct(Config $config)
  {

    $this->setConfig($config);

    try {
      @ parent::__construct(
        $config->get('dsn'),
        $config->get('user'),
        $config->get('password'),
        [
          Connection::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ]
      );
    } catch (\Exception $e) {
      throw new DbException('Connection error: ":error"', [
        'error' => $e->getMessage(),
      ]);
    }

    $this->setAttribute(Connection::ATTR_ERRMODE, Connection::ERRMODE_EXCEPTION);
    $this->setAttribute(Connection::ATTR_CURSOR, Connection::CURSOR_SCROLL);
    $this->setAttribute(Connection::ATTR_STATEMENT_CLASS, [__NAMESPACE__ . '\Stmt', [$this]]);

  }

  /**
   * @param Config $config
   * @return $this
   */
  public function setConfig(Config $config)
  {
    static::$config = $config;
    return $this;
  }

  /**
   * @return Config
   */
  public function getConfig()
  {
    return static::$config;
  }

  /**
   * @param null $query
   * @param array $params
   * @return StmtInterface
   */
  public function prepareQuery($query = null, array $params = [])
  {
    /** @var StmtInterface $stmt */
    $stmt = parent::prepare($query);
    if (count($params) > 0) {
      $stmt->bindParams($params);
    }
    return $stmt;
  }

  /**
   * @param null $query
   * @return ConnectionInterface
   * @throws DbException
   */
  public function execute($query = null)
  {
    try {
      $this->affectedRows = parent::exec($query);
    } catch (\Exception $exception) {
      throw new DbException('Executing query has error: [:code] ":error"', [
        'code' => $exception->getCode(),
        'error' => $exception->getMessage(),
      ]);
    }

    return $this;
  }

  /**
   * @param null $query
   * @return StmtInterface
   * @throws DbException
   */
  public function query($query = null)
  {
    /** @var StmtInterface $result */
    try {
      $result = parent::query($query);
    } catch (\Exception $exception) {
      throw new DbException('Executing query has error: [:code] ":error"', [
        'code' => $exception->getCode(),
        'error' => $exception->getMessage(),
      ]);
    }
    return $result;
  }

  /**
   * @return int
   */
  public function affectedRows()
  {
    return $this->affectedRows;
  }

  /**
   * @param null $name
   * @return string
   */
  public function lastInsertId($name = null)
  {
    return parent::lastInsertId($name);
  }

  /**
   * @return $this
   */
  public function start()
  {
    parent::beginTransaction();
    return $this;
  }

  /**
   * @return $this
   */
  public function commit()
  {
    parent::commit();
    return $this;
  }

  /**
   * @return $this
   */
  public function rollback()
  {
    parent::rollBack();
    return $this;
  }

}