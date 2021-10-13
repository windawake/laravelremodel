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
     * For laravel 5.5 version.
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
        /**
         * @var RemoteTool
         */
        $remoteTool = app('laravelremodel.tool');
        $condition = $remoteTool->queryToCondition($this->queryBuilder);
        $query = $remoteTool->conditionToQuery($condition);
        
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
        /**
         * @var RemoteTool
         */
        $remoteTool = app('laravelremodel.tool');
        $condition = $remoteTool->queryToCondition($this->queryBuilder);
        $query = $remoteTool->conditionToQuery($condition);
        $rowsNum = $query->update($data);

        return $rowsNum;
    }

    /**
     * Overwrite query insert. 
     *
     * @return int
     */
    public function insertGetIdHandle($data = null) {
        /**
         * @var RemoteTool
         */
        $remoteTool = app('laravelremodel.tool');
        $condition = $remoteTool->queryToCondition($this->queryBuilder);
        $query = $remoteTool->conditionToQuery($condition);

        $id = $query->insertGetId($data);

        return $id;
    }

    /**
     * Overwrite query delete. 
     *
     * @return int
     */
    public function deleteHandle() {
        /**
         * @var RemoteTool
         */
        $remoteTool = app('laravelremodel.tool');
        $condition = $remoteTool->queryToCondition($this->queryBuilder);
        $query = $remoteTool->conditionToQuery($condition);
        $rowsNum = $query->delete();

        return $rowsNum;
    }

    /**
     * Overwrite query exists. 
     *
     * @return int
     */
    public function existsHandle() {
        /**
         * @var RemoteTool
         */
        $remoteTool = app('laravelremodel.tool');
        $condition = $remoteTool->queryToCondition($this->queryBuilder);
        $query = $remoteTool->conditionToQuery($condition);

        $boolean = $query->exists();

        return $boolean;
    }
    

}
