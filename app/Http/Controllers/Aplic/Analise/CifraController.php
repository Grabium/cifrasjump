<?php

namespace App\Http\Controllers\Aplic\Analise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CifraController extends Controller
{
  
  public string $acordeConfirmado;
  public string $tonalidade;
  public string $tipagem;
  public int    $sizeAcordeConfirmado;
  public array  $enarmonia             = ['se' => false, 'natureza' => null];//sus ou bem
  public bool   $tercaMenor            = false;
  public array  $inversao              = ['se' => false, 'tom' => null, 'natureza' => null, 'indexInversao' => 0];// [V/F, tom, nat/sus/bem]
  public bool   $dissonancia           = false;
  public array  $marcador              = ['se' => false, 'marcador' => '', 'indexMarcador' => 0];

  public function setDissonancia(bool $get = false)
  {
    static $cont = 0;
    $this->dissonancia = true;
    $cont++;
    if($get == true){
      if($cont == 1){
        $dissonanciaPermanente = true;
      }else{
        $dissonanciaPermanente = false;
      }
      $cont = 0;
    return $dissonanciaPermanente;
    }
  }

  public function getDissonancia()
  {
    if($this->setDissonancia(true)){
      $this->dissonancia = false;
    }
  }
}
