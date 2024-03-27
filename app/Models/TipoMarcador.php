<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMarcador extends Model
{
    use HasFactory;
    protected $table = 'tipos_marcadores';
    protected $fillable = ['tipo'];

    public function marcador()
    {
        return $this->hasMany(Marcador::class, 'id_tipos_marcadores', 'id');
    }
}
