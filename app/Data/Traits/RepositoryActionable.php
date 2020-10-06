<?php

namespace App\Data\Traits;

trait RepositoryActionable
{
    public function publish($modelId)
    {
        return $this->transformSingleRow($this->findById($modelId)->publish());
    }

    public function unpublish($modelId)
    {
        return $this->transformSingleRow($this->findById($modelId)->unpublish());
    }

    public function close($modelId)
    {
        return $this->transformSingleRow($this->findById($modelId)->close());
    }

    public function reopen($modelId)
    {
        return $this->transformSingleRow($this->findById($modelId)->reopen());
    }

    public function analyse($modelId)
    {	    
        return $this->transformSingleRow($this->findById($modelId)->analyse());
    }	   

    public function unanalyse($modelId)
    {
        return $this->transformSingleRow($this->findById($modelId)->unanalyse());
    }

    public function verify($entryId)
    {
        return $this->transformSingleRow($this->findById($entryId)->verify());
    }

    public function unverify($entryId)
    {
        return $this->transformSingleRow($this->findById($entryId)->unverify());
    }

    public function delete($entryId)
    {
        return $this->findById($entryId)->delete();
    }
}
