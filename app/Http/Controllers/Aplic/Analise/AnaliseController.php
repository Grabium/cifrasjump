<?php

namespace App\Http\Controllers\Aplic\Analise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Aplic\Leitura\TextoController;
use Illuminate\Support\Collection;

class AnaliseController extends FerramentaAnaliseController
{
  protected array $arrayAcordes = [];
  protected array $arrayNegat   = [];//chor que deverá ser indexado para retorno da fase.
  
  
  
  public function faseAnalise($data): array
  {
    $this->preMap($data[0]);
    $this->marcadores = $data[1][0];
    
    collect($this->texto->arrayChor)->map(function (string $itemChor) {//$itemChor é item de arrayChor
      $this->chor = $itemChor;
      $this->incrArrayChor();
    });
    
    unset($this->texto);
    
    return [
      "arrayAcordes" => $this->arrayAcordes, 
      "arrayLinhas"  => $this->arrayLinhas, 
      "arrayNegat"   => $this->arrayNegat
    ];
  }

  private function incrArrayChor()
  {
    $this->cifra = new CifraController();
    $this->s = 0;
    $this->possivelInversao = false;
    $this->parentesis = false;
    if(($this->chor[0] == 'A')||($this->chor[0] == 'E')){
      $this->preparaEApAnalise();
    }
    //echo '<br /> -> .'.$this->chor.' será analisado:<br /> ';
    $this->incrChor();
  }

  private function incrChor()
  {
    $this->s++;
    $this->ac = $this->chor[$this->s];
    $this->analisar();
  }

  private function analisar()
  {
    require "ExpressoesTestes.php";
    //echo 'ac= .'.$this->ac.'. chor= .'.$this->chor.'. testando.<br>';
    if($espaçoOuInversao){ 
      //echo 'espaço.<br />';
      if($ouMiOuLaMaiorOuMenor){
        //echo 'if EA positivo<br />';
        $funcao = $this->seEouA();
        $this->$funcao(); //positivo() || negativo()
      }else{//positivo porem não EA
        $this->positivo();
      }
    }elseif($menor){
      //echo 'menor.<br />';
      $this->processaMenor();
      $this->incrChor();
    }elseif($enarmoniaDeAcordOuDissonan){
      $this->processaEnarmoniaDeAcordOuDissonan();
      $this->incrChor();
    }elseif($caracMaisOuMenos){
      $this->processaCaracMaisOuMenos();
      $this->incrChor();
    }elseif($barra){
      $funcao = $this->processaBarra();
      $this->$funcao();
    }elseif($abreParentesis){
      $funcao = $this->processaAbreParentesis();
      $this->$funcao(); 
    }elseif($fechaParentesis){//apenas para números
      $funcao = $this->processaFechaParentesis();
      $this->$funcao(); 
    }elseif($numero){
      $this->processaNumero();
      $this->incrChor();
    }elseif($marcador){
      $this->incrChor();
    }else{
      $this->negativo();
    }
  }

  private function positivo()
  {
    //echo $this->chor.' é acorde.<br /> ';
    $this->setTonalidade();
    $this->cifra->acordeConfirmado = $this->chor;
    $this->cifra->sizeAcordeConfirmado = strlen($this->chor);
    $this->InputInArray('arrayAcordes', 'cifra');
  }

  private function setTonalidade()
  {
    if($this->cifra->enarmonia['se'] == false){
      $this->cifra->tonalidade = substr($this->chor, 0, 1);
      $this->cifra->tipagem = substr($this->chor, 1);
      
    }elseif($this->cifra->enarmonia['se'] == true){
      $this->cifra->tonalidade = substr($this->chor, 0, 2);
      $this->cifra->tipagem = substr($this->chor, 2);
    }
  }
  
  private function negativo()
  {
    //echo $this->chor.' não é acorde com .'.$this->ac.'.<br /> ';
    $this->InputInArray('arrayNegat', 'chor');
  }
}