<?php

namespace App\Http\Controllers\Aplic\Leitura;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Aplic\Principal\NaturalController;

 /******
 *  Altera estado do objeto $texto para futura análise
 *  manipulando suas arrays.
 /******/

class LeituraController extends InputMarcadorController
{

  private TextoController $texto;
  private string          $ordem     = 'fechada';
  private int             $complChor = 12;
  private string          $line      = '';
  private string          $chor      = '';
  private string          $car       = '';
  private int             $i;
  private int             $lastIndex  = 0;
  private array           $marcadores = [];
  
  public function __construct(string $textoRecebido)
  {
    $arrayData   =  $this->inserirMarcadores($textoRecebido);
    $this->marcadores[] = $arrayData[1];
    $this->marcadores[] = $arrayData[2];
    $this->texto = new TextoController($arrayData[0]);
  }

  public function faseLeitura()
  {
    $l = strlen($this->texto->textoMarcado);
    for($this->i=0; $this->i<$l; $this->i++){
      
      $this->car = $this->texto->textoMarcado[$this->i];

      if($this->car == ' '){
        $this->ordem = 'aberta';
        $this->setLine();
        
        continue;
      }

      if($this->ordem == 'aberta'){
        if(in_array($this->car, (new NaturalController)->naturais)){
          $this->indicarParaAnalise($this->car);//arryas int e EAs...indicações.
          $this->chor = $this->separarChor();
          $this->InputInArray('arrayTextLines', 'line');
          $this->InputInArray('arrayChor', 'chor');
          $this->saltarLeitura();
        }else{
          $this->setLine();
        }
      }else{
        $this->setLine();
      }

      $this->ordem = 'fechada';
    }//for()
    if($this->line != ''){
      $this->InputInArray('arrayTextLines', 'line');
    }
    $this->texto->textoMarcado = '';
    $this->texto->indicados = [];
    $this->marcadoresToSend();
    return [$this->texto, $this->marcadores];
  }//faseLeitura()

  private function InputInArray($arrayCL, $itemCL)
  {
    
    $stringIndex = $this->lastIndex;
    settype($stringIndex, "string");
    $newKey = '0'.$stringIndex;
    $this->texto->$arrayCL[$newKey] = $this->$itemCL;
    $this->lastIndex++ ;
    if($itemCL == 'line'){
      $this->line = '';
    }
  }

  private function separarChor()
  {
    $chor = substr($this->texto->textoMarcado, $this->i, ($this->complChor+1)); 
    $chor = $chor . " ";
    return substr($chor, 0, (strpos($chor, " ")+1)); 
  }

  private function indicarParaAnalise()
  {
    array_push($this->texto->indicados, $this->i);
    
    if(($this->car == "E")||($this->car == "A")){
      array_push($this->texto->locaisEA, $this->i);
      array_push(   $this->texto->preEA, $this->texto->textoMarcado[$this->i-2]);
      array_push(   $this->texto->posEA, $this->texto->textoMarcado[$this->i+2]);
      array_push( $this->texto->posEmAm, $this->texto->textoMarcado[$this->i+3]);
    }
  }

  private function setLine()
  {
    $this->line = $this->line.$this->car;
  }

  private function saltarLeitura()
  {
    $saltoLeitura = ($saltoLeitura = strlen(end($this->texto->arrayChor))) ?: 0;
    $this->i += ($saltoLeitura-2);
  }

  private function marcadoresToSend()
  {
    collect($this->marcadores[0])->map(function ($marcador){
      
      array_push($this->marcadores[0], $marcador);
      array_shift($this->marcadores[0]);
    });

    array_shift($this->marcadores[0]);
    array_shift($this->marcadores[0]);
    array_shift($this->marcadores[1]);
    array_shift($this->marcadores[1]);
    
  }

}//class
