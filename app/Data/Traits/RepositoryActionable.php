<?php

namespace App\Data\Traits;

trait RepositoryActionable
{
    public function publish($modelId)
    {
        return $this->findById($modelId)->publish();
    }

    public function unpublish($modelId)
    {
        $this->findById($modelId)->unpublish();
    }

    public function close($modelId)
    {
        $this->findById($modelId)->close();
    }

    public function reopen($modelId)
    {
        $this->findById($modelId)->reopen();
    }

    public function analyse($modelId)
    {
        $this->findById($modelId)->analyse();
    }

    public function unanalyse($modelId)
    {
        $this->findById($modelId)->unanalyse();
    }

    public function verify($entryId)
    {
        $this->findById($entryId)->verify();
    }

    public function unverify($entryId)
    {
        $this->findById($entryId)->unverify();
    }

    public function delete($entryId)
    {
        $this->findById($entryId)->delete();
    }
}
