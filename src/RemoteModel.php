<?php

namespace Laravel\Remote2Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class RemoteModel extends Model
{
    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $queryBuilder = null;

    protected $WheresArr = [];

    protected $connection = 'remote';

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        $query->setModel($this);
        return new EloquentBuilder($query);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        return $connection->query();
    }

    /**
     * set queryBuiler property.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return static
     */
    public function setQueryBuilder(\Illuminate\Database\Query\Builder $query)
    {
        $this->queryBuilder = $query;
        return $this;
    }

    /**
     * Overwrite query get. 
     *
     * @return array
     */
    public function getHandle()
    {
        $condition = RemoteHelper::queryToCondition($this->queryBuilder);
        $query = RemoteHelper::conditionToQuery($condition);
        
        $list = $query->get();
        return $list;
    }

    /**
     * Overwrite query update. 
     *
     * @return int
     */
    public function updateHandle($data = null)
    {
        $condition = RemoteHelper::queryToCondition($this->queryBuilder);
        $query = RemoteHelper::conditionToQuery($condition);
        $rowsNum = $query->update($data);

        return $rowsNum;
    }

    /**
     * Overwrite query insert. 
     *
     * @return int
     */
    public function insertGetId($data = null) {
        $condition = RemoteHelper::queryToCondition($this->queryBuilder);
        $query = RemoteHelper::conditionToQuery($condition);

        $id = $query->insertGetId($data);

        return $id;
    }

    /**
     * Overwrite query delete. 
     *
     * @return int
     */
    public function deleteHandle() {
        $condition = RemoteHelper::queryToCondition($this->queryBuilder);
        $query = RemoteHelper::conditionToQuery($condition);
        $rowsNum = $query->delete();

        return $rowsNum;
    }
    

}
