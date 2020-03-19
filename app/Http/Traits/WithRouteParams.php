<?php

namespace App\Http\Traits;

trait WithRouteParams
{
    /**
     * @param array $all
     * @return array
     */
    public function sanitize(array $all)
    {
        $this->replace(
            $all = array_replace_recursive($all, $this->route()->parameters())
        );

        return $all;
    }
}
