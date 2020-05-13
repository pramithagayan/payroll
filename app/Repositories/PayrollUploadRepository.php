<?php

namespace App\Repositories;


use A17\Twill\Repositories\ModuleRepository;
use App\Models\PayrollUpload;

class PayrollUploadRepository extends ModuleRepository
{
    

    public function __construct(PayrollUpload $model)
    {
        $this->model = $model;
    }
}
