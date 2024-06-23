<?php

namespace App\Services;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ApiResultTools
{
    private Builder $query;
    private Request $request;

    public function __construct(Builder $query, Request $request)
    {
        $this->query = $query;
        $this->request = $request;
    }

    public function search(): ApiResultTools
    {
        if ($this->request->has('search') && is_array($this->request->search)) {
            $ar = $this->request->search;
            $this->query = $this->query->where($ar['search_field'], 'like', '%' . $ar['search_string'] . '%');
        }
        return $this;
    }

    public function order(): ApiResultTools
    {
        if ($this->request->has('order') && is_array($this->request->order)) {
            $ar = json_decode($this->request->search, true);
            $this->query = $this->query->orderBy($ar['order_field'], $ar['order_direction']);
        }
        return $this;
    }

    public function paginate(int $pageSize)
    {
        if ($this->request->has('page')) {
            return $this->query->paginate($pageSize);
        }
        return $this->query->get();
    }
}
