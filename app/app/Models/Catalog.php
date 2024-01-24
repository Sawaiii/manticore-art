<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Catalog extends Model
{
    use HasFactory;


    protected $table = "prt_catalog";


    public function razdel():BelongsTo
    {
        return $this->belongsTo(Razdel::class , 'Razdel', 'ID');
    }
}
