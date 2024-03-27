<?php

namespace App\Http\Controllers\Aplic\Leitura;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TextoController extends Controller
{
  public string $textoMarcado;
  public  array $indicados         = []; //indicia os caracteres do texto marcado que fazem parte dos naturais 
  public  array $arrayChor         = []; //reserva os chor
  public  array $locaisEA          = []; //inteiro
  public  array $preEA             = []; //string 
  public  array $posEA             = []; //string
  public  array $posEmAm           = []; //string
  public string $localEA_menosDois; //string
  public string $localEA_maisDois; //string
  public string $localEmAm_maisDois; //string
  public  array $arrayTextLines  = []; //string
 
  public function __construct(string $textoMarcado)
  {
    $this->textoMarcado = $textoMarcado;
  }

 
}
