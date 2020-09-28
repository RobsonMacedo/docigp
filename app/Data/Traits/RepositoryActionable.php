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
        return $this->findById($modelId)->unpublish();
    }

    public function close($modelId)
    {
        return $this->findById($modelId)->close();
    }

    public function reopen($modelId)
    {
        return $this->findById($modelId)->reopen();
    }

    public function analyse($modelId)
    {
        return $this->transformSingleRow($this->findById($modelId)->analyse());
    }

    public function unanalyse($modelId)
    {
        return $this->transformSingleRow(
            $this->findById($modelId)->unanalyse()
        );
    }

    public function verify($entryId)
    {
        return $this->findById($entryId)->verify();
    }

    public function unverify($entryId)
    {
        return $this->findById($entryId)->unverify();
    }

    public function delete($entryId)
    {
        $this->findById($entryId)->delete();
    }
}
