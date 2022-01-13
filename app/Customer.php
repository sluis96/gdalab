<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'dni';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'dni', 'id_reg', 'id_com', 'email', 'name', 'last_name', 'address', 'date_reg', 'status',
    ];

    public function commune()
    {
        return $this->belongsTo(Commune::class, 'id_com', 'id_com');
    }
}
