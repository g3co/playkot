<?php

namespace Playkot\PhpTestTask\Storage;

use MongoDB\Driver\Cursor;
use MongoDB\Driver\Manager as MongoClient;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\WriteResult;
use MongoDB\Driver\Query;

class AdapterMongo implements IStorageAdapter
{
    protected $connection = null;
    protected $dbName = null;
    protected $collection = null;

    public function __construct(array $config)
    {
        if (empty($config['host'])) {
            $config['host'] = '127.0.0.1';
        }

        if (empty($config['port'])) {
            $config['port'] = '27017';
        }

        $this->connection = new MongoClient('mongodb://' . $config['host'] . ':' . $config['port']);

        $this->dbName = !empty($config['dbName']) ? $config['dbName'] : 'local';
        $this->collection = !empty($config['collection']) ? $config['collection'] : 'demo';
    }

    /**
     * @param string $id
     * @param array $payment
     * @param bool $isUpdate
     * @return bool
     */
    public function save(string $id, array $payment, bool $isUpdate): bool
    {
        if (!empty($payment)) {

            $bulk = new BulkWrite();

            if ($isUpdate) {
                $bulk->update(['paymentId' => $id], ['$set' => $payment]);
            } else {
                $bulk->insert($payment);
            }

            return (bool)count($this->execWrite($bulk)->getWriteErrors());
        }

        return true;
    }

    /**
     * @param string $paymentId
     * @return bool
     */
    public function has(string $paymentId): bool
    {
        $cursorArray = $this->exec(new Query(['paymentId' => $paymentId], []))->toArray();
        return isset($cursorArray[0]);
    }

    /**
     * @param string $paymentId
     * @return array
     */
    public function get(string $paymentId): array
    {
        $cursor = $this->exec(new Query(['paymentId' => $paymentId], []));
        $cursorArray = (array)current($cursor->toArray());

        if ($cursorArray == ['']) {
            $cursorArray = [];
        }

        return $cursorArray;
    }

    /**
     * @param string $paymentId
     * @return bool
     */
    public function remove(string $paymentId): bool
    {
        $bulk = new BulkWrite;
        $bulk->delete(['paymentId' => $paymentId], ['limit' => 0]);
        return (bool)count($this->execWrite($bulk)->getWriteErrors());
    }

    /**
     * @param Query $query
     * @return Cursor
     */
    protected function exec(Query $query):Cursor
    {
        return $this->connection->executeQuery($this->dbName . '.' . $this->collection, $query);
    }

    /**
     * @param BulkWrite $bulk
     * @return WriteResult
     */
    protected function execWrite(BulkWrite $bulk):WriteResult
    {
        return $this->connection->executeBulkWrite($this->dbName . '.' . $this->collection, $bulk);
    }
}