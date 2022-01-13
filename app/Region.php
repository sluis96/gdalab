<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $primaryKey = 'id_reg';

    protected $fillable = [
        'description', 'status',
    ];

    public $timestamps = false;

    public function communes()
    {
        return $this->hasMany(Commune::class, 'id_reg', 'id_reg');
    }
}
