<?php

namespace Laravel\Remote2Model;

use Illuminate\Database\Connection;

class RemoteConnection extends Connection
{

    /**
     * Get a new query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        return new RemoteQueryBuilder(
            $this, $this->getQueryGrammar(), $this->getPostProcessor()
        );
    }
    
}
