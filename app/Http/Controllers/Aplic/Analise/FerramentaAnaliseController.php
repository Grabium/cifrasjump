<?php

namespace App\Http\Controllers\Aplic\Analise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Aplic\Leitura\TextoController;
use App\Http\Controllers\Aplic\Principal\NaturalController;
use App\Http\Controllers\Aplic\Leitura\MarcadorController;

class FerramentaAnaliseController extends Controller
{
  protected TextoController $texto;
  protected CifraController $cifra;
  protected array $marcadores; 
  protected array $naturais; 
  protected int $changeChor = -1;//itera os chor reservados em TextoController::arrayChor[]
  protected int $s = 0; //índice do $chor a ser analizado
  protected string $ac; //caractere a analisar
  protected string $chor;
  protected bool $possivelInversao = false;
  protected bool $parentesis = false;
  protected array $arrayChorKeys = [];

  public function __construct()
  {
    $this->naturais = (new NaturalController)->naturais;
  }

  protected function preMap(TextoController $texto)
  {
    $this->texto = $texto;
    unset($texto);
    $this->arrayLinhas = $this->texto->arrayTextLines;
    $this->arrayChorKeys = array_keys($this->texto->arrayChor);
    $this->arrayTextLinesKeys = array_keys($this->texto->arrayTextLines);
  }

  protected function preparaEApAnalise()
  {
    $this->texto->localEA_menosDois  = array_shift(  $this->texto->preEA);
    $this->texto->localEA_maisDois   = array_shift(  $this->texto->posEA);
    $this->texto->localEmAm_maisDois = array_shift($this->texto->posEmAm);
  }
  
  protected function seEouA()
  {
    if((($this->texto->localEA_menosDois == "%")||($this->texto->localEA_menosDois == '.'))//se início de frase
      &&(!in_array($this->texto->localEA_maisDois, $this->naturais))//e não há um possível acorde o seguindo.
      &&($this->texto->localEA_maisDois != "%")&&($this->texto->localEA_maisDois != " ")){//e não é fim de linha de acordes.
      //echo 'negativo 1 <br />';
      return 'negativo'; //AnaliseController->incrChor();
      
    }elseif(($this->chor[1] == 'm')
        &&(!in_array($this->texto->localEmAm_maisDois, $this->naturais))//e não há um possível acorde o seguindo.
        &&($this->texto->localEA_menosDois == "%")
        &&($this->texto->localEmAm_maisDois != "%")
        &&($this->texto->localEmAm_maisDois != " ")){
      //echo 'negativo 2 .'.$this->texto->localEmAm_maisDois.' - '.$this->texto->localEA_menosDois.' - '.$this->chor[1].'.<br />';
      return 'negativo';
    }else{
      return 'positivo'; // encaminha para AnaliseController->positivo();
    }
  }

  protected function InputInArray(string $posNegArr, string $posNegItem)
  {
    $this->cifra->getDissonancia();//zerar quando posit. ou neg.
    $key = array_shift($this->arrayChorKeys);
    $this->$posNegArr[$key] = $this->$posNegItem;
  }

  protected function processaEnarmoniaDeAcordOuDissonan()
  {
    if($this->s == 1){
      $this->cifra->enarmonia['se'] = true;
      if($this->ac == '#'){
        $this->cifra->enarmonia['natureza'] = 'sustenido';
      }elseif($this->ac == 'b'){
        $this->cifra->enarmonia['natureza'] = 'bemol';
      }
    } 
    //dissonancia nao classifica a cifra. Serve apenas para abrir/fechar análise.
    $this->cifra->dissonancia = false;
  }

  protected function processaMenor()
  {
    $this->cifra->tercaMenor = true;
  }

  protected function processaCaracMaisOuMenos()
  {
    if(($this->ac == '-')&&($this->cifra->dissonancia == false)){
      $this->processaMenor();
    }
    $this->cifra->dissonancia = false;
  }

  protected function processaBarra()
  {
    //$this->cifra->dissonancia = false;
    $this->sAc();
    //echo '- .'.$this->ac.'. - .'.$this->chor.' dentro barr - parent'.$this->parentesis.'<br>';
    if(in_array($this->ac, $this->naturais)){
      return $this->seInversao();
    }elseif(($this->ac == '(')&&($this->parentesis == false)){
      //echo $this->chor. ' abre<br>';
      return $this->processaAbreParentesis();
    }else{//se não naturais
      return $this->seNum();//testar numeros
    }
  }

  private function sAc($incremento = 1)
  {
    $this->s = ($this->s + $incremento);
    $this->ac = $this->chor[$this->s];
  }
  
  protected function seNum()
  {
    //echo $this->ac.' - diss: .'.$this->cifra->dissonancia.'. <br>';
    if($this->cifra->dissonancia == false){
      $numAte9 = ['2', '3', '4', '5', '6', '7', '9'];
      if(in_array($this->ac, $numAte9)){
        return $this->numOk();
      }elseif($this->ac == '1'){
        $numAte14 = ['0', '1', '2', '3', '4'];
        $this->sAc();
        if(in_array($this->ac, $numAte14)){
          return $this->numOk();
        }
      }
    }
    return 'analisar';//caso == barra fecha-parentesis ou negativo
  }

  private function numOk()
  {
    $this->cifra->setDissonancia();
    return 'incrChor';
  }

  protected function processaAbreParentesis()
  {
    $this->parentesis = true;
    return $this->processaBarra();//mesmos passos.
  }

  private function seInversao()
  {
    $this->possivelInversao = true;
    $indexInversao = $this->s;
    $this->sAc();
    if(($this->ac == ' ')||(($this->parentesis == true)&&($this->ac == ')'))){
      if(($this->parentesis = true)&&($this->ac == ')')){$this->processaFechaParentesis();}
      $this->cifra->inversao = ['se'=>true, 
        'tom'=>$this->chor[$this->s-1], 
        'natureza'=>'naturalInv',
        'indexInversao' => $indexInversao];
    }elseif(($this->ac == '#')||($this->ac == 'b')){
      if($this->ac == '#'){
        $this->cifra->inversao['natureza'] = "sustenidoInv";
      }elseif($this->ac == 'b'){
        $this->cifra->inversao['natureza'] = "bemolInv";
      }
      $this->sAc();
      if(($this->ac == ' ')||(($this->parentesis == true)&&($this->ac == ')'))){
        if(($this->parentesis = true)&&($this->ac == ')')){$this->processaFechaParentesis();}
        $this->cifra->inversao['se'] = true;
        $this->cifra->inversao['tom'] = $this->chor[$this->s-2].$this->chor[$this->s-1];//string($s - 2, 2);
        $this->cifra->inversao['indexInversao'] = $indexInversao;
      }
    }
    return 'analisar';
  }

  protected function processaFechaParentesis()
  {
    $this->parentesis = false;
    $this->cifra->dissonancia = false;
    return 'incrChor';
  }

  protected function processaNumero()
  {
    if($this->cifra->dissonancia == false){echo 'Dissonancia indevida!';}//gerar exception
    $this->cifra->dissonancia = false;
  }

  protected function seMarcador():bool
  {//se positivo será processado aqui mesmo.
    $this->cifra->marcador['indexMarcador'] = $this->s;
    $this->sAc();
    if($this->ac == '_'){
      if(($this->cifra->enarmonia['se'] == true)&&($this->s == 3)){
        $start = 2;
      }elseif(($this->cifra->enarmonia['se'] == false)&&($this->s == 2)){
        $start = 1;
      }
      
      $boolean = $this->compararMarcador($start);
      if($boolean){
        $this->sAc(4);
      }
      return $boolean;
    }
    return false;
  }

  private function compararMarcador($start):bool
  {
    $parcialChor = substr($this->chor, $start, 6);
    if(in_array($parcialChor, $this->marcadores)){
      $this->cifra->marcador['se'] = true;
      $this->cifra->marcador['marcador'] = $parcialChor;
      return true;
    }else{
      return false;
    }
  }
    
  
  
}
