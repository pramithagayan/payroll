<?php

namespace App\Repositories;

use A17\Twill\Models\Behaviors\Sortable;
use A17\Twill\Repositories\ModuleRepository;
use App\Models\PayrollUpload;

class PayrollUploadRepository extends ModuleRepository
{
    

    public function __construct(PayrollUpload $model)
    {
        $this->model = $model;
    }

    

    /**
     * @param array $with
     * @param array $scopes
     * @param array $orders
     * @param int $perPage
     * @param bool $forcePagination
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($with = [], $scopes = [], $orders = [], $perPage = 20, $forcePagination = false)
    {
        if(in_array(\Auth::guard('twill_users')->user()->role, ['SUPERADMIN', 'ADMIN'])) {
            $query = $this->model->with($with);
        } else {
            $query = $this->model->where('user_id', \Auth::guard('twill_users')->user()->id)->with($with);
        }

        $query = $this->filter($query, $scopes);
        $query = $this->order($query, $orders);

        if (!$forcePagination && $this->model instanceof Sortable) {
            return $query->ordered()->get();
        }

        if ($perPage == -1) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }
}
