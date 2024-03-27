<?php

namespace App\Http\Controllers\Aplic\Concatenacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Aplic\Analise\CifraController;
use Illuminate\Support\Collection;


class ConcatenacaoController extends Controller
{

  private array $arrayAcordes   = [];
  private array $arrayLinhas    = [];
  private array $arrayNegat     = [];
  private array $caracteres     = [];
  private array $marcadores     = [];
  private array $newAcordes     = [];
  private array $chaveDeAcordes = [];
  private CifraController $cifra;
  private LinhaProntaController $linha;
  
  
  public function faseConcatenacao(array $arraysPrincipais, array $marcadores):array
  {
    $this->arrayAcordes = $arraysPrincipais['arrayAcordes'];
    $this->arrayLinhas  = $arraysPrincipais['arrayLinhas'];
    $this->arrayNegat   = $arraysPrincipais['arrayNegat'];
    $this->marcadores   = $marcadores[0];
    $this->caracteres   = $marcadores[1];
    unset($arraysPrincipais, $marcadores);
    
    $this->changeMarcadoresCifras();
    $this->arrayLinhas = $this->changeMarcadoresLinhasENegat($this->arrayLinhas);
    $this->arrayNegat  = $this->changeMarcadoresLinhasENegat($this->arrayNegat);
    unset($this->caracteres, $this->marcadores);
    
    $linhasECifras = $this->setLInhasECifras();
    $collectionChords = $this->setOnlyChords();
    
    return ['lines' => $linhasECifras, 'cifers' => $collectionChords];
  }

  private function changeMarcadoresCifras()
  {
    collect($this->arrayAcordes)->map(function (CifraController $cifra){
      $this->cifra = $cifra;
      if($this->cifra->marcador['se'] == true){
        $r = array_search($this->cifra->marcador['marcador'], $this->marcadores);
        $this->cifra->acordeConfirmado = substr_replace(
          $this->cifra->acordeConfirmado,
          $this->caracteres[$r],
          $this->cifra->marcador['indexMarcador'],
          6);
        $this->cifra->tipagem = ($this->cifra->enarmonia['se'] == true)
          ?substr($this->cifra->acordeConfirmado, 2)
          :substr($this->cifra->acordeConfirmado, 1);
      }

      unset($this->cifra->marcador['indexMarcador'], $this->cifra->inversao['indexInversao']);
    });
  }

  private function changeMarcadoresLinhasENegat(array $arr):array
  {
    
    return collect($arr)->map(function ($linha, $key){
      return str_replace($this->marcadores, $this->caracteres, $linha);
    })->all();
    
  }

  private function setLInhasECifras()
  { 
    collect($this->arrayAcordes)->map(function (CifraController $cifra, string $key){
      $this->cifra = $cifra;
      $this->newAcordes[$key] = $this->cifra->acordeConfirmado;
      array_push($this->chaveDeAcordes, $key); 
    });
    
    $newMasterArray = array_merge($this->arrayLinhas, $this->arrayNegat, $this->newAcordes);
    $newMasterArray = $this->ordenar($newMasterArray);
    $imploded = $this->implodir($newMasterArray);
    $exploded = $this->explodir($imploded);
    //dd($exploded);
    return $exploded;
  }

  private function ordenar(array $newMasterArray):array
  {
    $v = [];
    $l = count($newMasterArray);
    for($i=0; $i<$l; $i++){
      $c = $i;
      settype($c, 'string');
      $c = '0'.$c;
      $this->linha = new LinhaProntaController($newMasterArray[$c]);
      if(in_array($c, $this->chaveDeAcordes)){
        $this->linha->tipo = 'cifra';
      }
      array_push($v, $this->linha);
    }
    return $v;
  }

  private function implodir(array $newMasterArray):array
  {
    $imploded = '';
    $ondeCifra = [];
    foreach($newMasterArray as $linha){
      $imploded = $imploded.$linha->conteudo;
      if($linha->tipo == 'cifra'){
        array_push($ondeCifra, (strlen($imploded)-1));
      }
    }
    return [$imploded, $ondeCifra];
  }

  private function explodir($imploded):array
  {
    $linhaString  = $imploded[0];
    $ondeCifra    = $imploded[1];
    $oc = 0;
    $v = [];
    $li = '';
    $cif = false;
    
    $l = strlen($linhaString);
    for($i=1; $i<$l; $i++){
      $c = $linhaString[$i];
      $li = $li.$c;
      if(($ondeCifra != [])&&($ondeCifra[$oc] == $i)){
        $cif = true;
        $oc = (($oc+1)<count($ondeCifra)) ? $oc+1: 0 ;
      }
      if($c == '%'){
        $li = substr($li, 1);
        $li = ($cif == true) ? substr($li, 0, -3) : substr($li, 0, -2) ;
        $v[] = ['content'=>$li, 'cifer'=>$cif];
        $li = '';
        $cif = false;
      }
    }
    
    return $v;
  }

  private function setOnlyChords():array
  {
    $r = collect($this->arrayAcordes)->map(function (CifraController $chord){
      return substr($chord->acordeConfirmado, 0, -1);
    })->all();

    return array_unique($r);
  }
}
