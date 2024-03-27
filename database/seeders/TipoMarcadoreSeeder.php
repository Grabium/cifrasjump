<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoMarcador;

class TipoMarcadoreSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  private array $tiposMarcadores = ['dim', 'maj', 'sus', 'add', 'aug'];
  
  public function run()
  {
    foreach($this->tiposMarcadores as $tipo){
      TipoMarcador::create(['tipo' => $tipo]);
    }
  }
}
