<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyPackage extends Model
{
    use HasFactory;

    public function company(){
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function package(){
        return $this->hasOne(Package::class, 'id', 'package_id');
    }
}
