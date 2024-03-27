<?php

namespace App\Http\Controllers\Aplic\Leitura;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Marcador;
use Illuminate\Support\Facades\DB;

class MarcadorController extends Controller
{
  //   < entrada_do_cliente > => < novo_valor >
  protected $agenteCli = [ "\r\n" => ' % ', "\n"=>' % '];
  
  
  public function getLista($coluna)
  {
    $compac = $this->busca($coluna);
    $lista  = $this->descompacta($compac, $coluna);
    $lista_concatenada = $this->concatena($lista, $coluna);
    return $lista_concatenada;
  }

  protected function busca($coluna)
  {
    $compac = Array(DB::table('marcadores')->select($coluna)->get());
    return $compac;
  }

  protected function descompacta($compac, $coluna)
  {
    $lista = [];
    foreach($compac as $unid_compac){
      if(($unid_compac != null)||($unid_compac != false)||($unid_compac != "")){
        foreach($unid_compac as $obj_descompac){
          if(($obj_descompac != null)||($obj_descompac != false)||($obj_descompac != "")){
            array_push($lista, $obj_descompac->$coluna);
          }
        }
      }
    }
    return $lista;
  }

  protected function concatena($lista, $coluna)
  {
    if($coluna == 'caractere'){
      $lista_concatenada = array_merge(array_keys($this->agenteCli), $lista);
    }elseif($coluna == 'marcador'){
      $lista_concatenada = array_merge($this->agenteCli, $lista);
    }
    return $lista_concatenada;
  }
}
