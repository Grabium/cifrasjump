<?php

namespace App\Http\Controllers\Aplic\Principal;

use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;
use App\Http\Requests\FatorRequest;
use App\Http\Controllers\Aplic\Conversao\ConversorController;
use App\Http\Controllers\Aplic\Concatenacao\ConcatenacaoController;
use App\Http\Controllers\Aplic\Leitura\LeituraController;
use App\Http\Controllers\Aplic\Analise\AnaliseController;

class PrincipalController extends Controller
{

  private LeituraController      $leitura;
  private AnaliseController      $analise;
  private ConversorController    $conversao;
  private ConcatenacaoController $concatenacao;
  

  public function __construct(FatorRequest $request)
  {
    
    $this->leitura = new LeituraController((string)$request['texto']);
    $this->analise = new AnaliseController();
    $this->conversao = new ConversorController($request['fator']);
    $this->concatenacao = new ConcatenacaoController();
  }
  
  public function master()
  {
    $resposta = $this->passosBasicos();
    return response()->json($resposta);
  }

  private function passosBasicos()
  {
    $texto_e_marcadores = $this->leitura->faseLeitura();//texto(objeto), marcadores[marc, carac].
    $linhas_e_Acordes = $this->analise->faseAnalise($texto_e_marcadores);//arrayAcordes, arrayLinhas, arrayNegat.
    $linhas_e_Acordes['arrayAcordes'] = $this->conversao->faseConversao($linhas_e_Acordes["arrayAcordes"]);
    $resposta = $this->concatenacao->faseConcatenacao($linhas_e_Acordes, $texto_e_marcadores[1]);
    return $resposta;
  }
}
