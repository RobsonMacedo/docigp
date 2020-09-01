<?php

namespace App\Data\Traits;

use App\Data\Models\EntryDocument;

trait ModelActionable
{
    public function verify()
    {
        $this->update([
            'verified_at' => now(),
            'verified_by_id' => auth()->user()->id
        ]);
    }

    public function unverify()
    {
        $this->verified_at = null;
        $this->verified_by_id = auth()->user()->id;

        $this->unanalyse(false);

        $this->save();
    }

    public function isAnalysable()
    {
        return (!blank($this->verified_at) || !blank($this->closed_at)) &&
            blank($this->published_at);
    }

    public function analyse()
    {
        $this->update([
            'analysed_at' => now(),
            'analysed_by_id' => auth()->user()->id
        ]);
    }

    public function unanalyse($save = true)
    {
        $this->analysed_at = null;
        $this->analysed_by_id = auth()->user()->id;

        if (!$this instanceof EntryDocument) {
            $this->unpublish(false);
        }

        if ($save) {
            $this->save();
        }
    }

    public function isPublishable()
    {
        return !blank($this->analysed_at) &&
            (!blank($this->verified_at) || !blank($this->closed_at));
    }

    public function publish()
    {
        $this->update([
            'published_at' => now(),
            'published_by_id' => auth()->user()->id
        ]);

        return $this;
    }

    public function unpublish($save = true)
    {
        $this->published_at = null;
        $this->published_by_id = auth()->user()->id;

        if ($save) {
            $this->save();
        }
    }

    public function close()
    {
        $this->update([
            'closed_at' => now(),
            'closed_by_id' => auth()->user()->id
        ]);
    }

    public function reopen()
    {
        $this->update([
            'closed_at' => null,
            'closed_by_id' => auth()->user()->id
        ]);
    }
}
