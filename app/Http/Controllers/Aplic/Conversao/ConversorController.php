<?php

namespace App\Http\Controllers\Aplic\Conversao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Aplic\Analise\CifraController;

class ConversorController extends Controller
{
  private   CifraController $cifra;
  private   array   $tonalidadeSustenido = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
  private   array   $tonalidadeBemol     = ['C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab', 'A', 'Bb', 'B'];
  protected int     $fator;
  protected array   $novoArrayAcordes = [];
  protected string  $novoTom;
  protected string  $novoTomInv;
  
  public function __construct(int $fator)
  {
    $this->fator = $fator;
  }
  
  public function faseConversao(array $arrayAcordes):array
  {
    collect($arrayAcordes)->map(function (CifraController $cifra, string $key) {
      $this->cifra = $cifra;
      $this->converter();
      $this->novoArrayAcordes[$key] = $this->cifra;
    });
    
    
    return $this->novoArrayAcordes;
  }

  private function converter()
  {
    $this->transpor();
    $this->aterarEstadoCifra();
  }

  private function transpor()
  {
    $key = $this->setKeyFundamental();
    $this->novoTom = $this->buscarNovoTom($key);

    if($this->cifra->inversao['se'] == true){
      $key = $this->setKeyInversao();//criar funcao
      $this->novoTomInv = $this->buscarNovoTom($key);
    }
  }

  private function setKeyFundamental()
  {
    if(($this->cifra->enarmonia['se'] == false)||($this->cifra->enarmonia['natureza'] == 'sustenido')){
      return array_search($this->cifra->tonalidade, $this->tonalidadeSustenido);//retorna o número da fundamental.
    }elseif($this->cifra->enarmonia['natureza'] == 'bemol'){
      return array_search($this->cifra->tonalidade, $this->tonalidadeBemol);
    }
  }

  private function setKeyInversao()
  {
    if(($this->cifra->inversao['natureza'] == 'naturalInv')||($this->cifra->inversao['natureza'] == 'sustenidoInv')){
      return array_search($this->cifra->inversao['tom'], $this->tonalidadeSustenido);//retorna o número da fundamental.
    }elseif($this->cifra->inversao['natureza'] == 'bemolInv'){
      return array_search($this->cifra->inversao['tom'], $this->tonalidadeBemol);
    }
  }

  private function buscarNovoTom($key)
  {
    $resto = (($this->fator + $key)%12);
    if($resto < 0){
      return $this->tonalidadeSustenido[12 + $resto];
    }else{
      return $this->tonalidadeSustenido[$resto];
    }
  }

  private function aterarEstadoCifra()
  {
    $this->alterarInversao();
    $this->alterartonalidade();
    $this->alterarTipagem();
  }

  private function alterarInversao()
  {
    if($this->cifra->inversao['se'] == true){
      $this->substrRelaceAcordeConfirmado(
        $this->novoTomInv, 
        $this->cifra->inversao['indexInversao'], 
        strlen($this->cifra->inversao['tom']));
      $this->cifra->inversao['tom'] = $this->novoTomInv;
      if(strlen($this->cifra->inversao['tom']) == 1){
        $this->cifra->inversao['natureza'] = 'naturalInv';
      }else{
        $this->cifra->inversao['natureza'] = ($this->cifra->inversao['tom'][1] == '#') ? 'sustenidoInv' : 'bemolInv';
      }
    }
  }

  private function alterartonalidade()
  {
    $this->substrRelaceAcordeConfirmado($this->novoTom, 0, strlen($this->cifra->tonalidade));
    $tl = strlen($this->cifra->tonalidade);
    $this->cifra->tonalidade = $this->novoTom;
    if(strlen($this->cifra->tonalidade) == 1){
      $this->cifra->enarmonia = ['se' => false, 'natureza' => null];
    }elseif(strlen($this->cifra->tonalidade) == 2){
      $r = ($this->cifra->tonalidade[1] == '#') ? 'sustenido' : 'bemol';
      $this->cifra->enarmonia = ['se' => true, 'natureza' => $r];
    }
    $this->cifra->sizeAcordeConfirmado = strlen($this->cifra->acordeConfirmado);

    $f = (strlen($this->cifra->tonalidade) - $tl);//novo - velho
    $this->cifra->inversao['indexInversao'] = ($this->cifra->inversao['indexInversao'] + ($f));
    
  }

  private function alterarTipagem()
  {
    if($this->cifra->enarmonia['se'] == true){
      $r = 2;
    }elseif($this->cifra->enarmonia['se'] == false){
      $r = 1;
    }
    $this->cifra->tipagem = substr($this->cifra->acordeConfirmado, $r);
  }

  private function substrRelaceAcordeConfirmado(string $replace, int $index, int $length)
  {
    $this->cifra->acordeConfirmado =  substr_replace($this->cifra->acordeConfirmado,$replace,$index,$length);
  }

}
