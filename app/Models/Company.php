<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Company extends Model
{
    use HasFactory, HasApiTokens;

    public function package(){
        return $this->hasOne(CompanyPackage::class, 'company_id', 'id');
    }
}
