<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessConcatPerson extends Model
{
    //
    const CREATED_AT = null;
    const UPDATED_AT = null;
    protected $table = 'business_concat_persons';

    protected $guarded = ['id', 'created_at', 'updated_at'];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
