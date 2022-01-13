<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $primaryKey = 'id_com';

    protected $fillable = [
        'id_reg', 'description', 'status',
    ];

    public $timestamps = false;

    public function region()
    {
        return $this->belongsTo(Region::class, 'id_reg', 'id_reg');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'id_com', 'id_com');
    }
}
