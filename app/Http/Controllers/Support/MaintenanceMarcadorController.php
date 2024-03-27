<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Marcador;

class MaintenanceMarcadorController extends Controller
{

  public function index()
  {
    $marcadores = Marcador::all();
    return response()->json($marcadores);
  }


  public function store(Request $request)
  {
    $data = $request->all(); //para retorno

    Marcador::create($data);
    
    return response()->json([
      'data' => [
        'msg' => 'Marcador: <strong>'.$data['marcador'].'</strong> cadastrado com sucesso!'
      ]
    ], 200);//return
  }


  
  public function show(string $id)
  {
    $search_tag = new Marcador();
    $tag = $search_tag->findOrFail($id);

    return response()->json([
      'data' => $tag
    ], 200);//return
  }


  public function update(Request $request, string $id)
  {
    try{
      $new_tag = $request->all();
      $old_tag = Marcador::findOrFail($id);
      $old_tag->update($new_tag);
    }catch(Exception $e){
      return response()->json([
        'data' => [
          'msg' => 'Marcador: NÃO ATUALIZADO!'
        ]
      ], 200);
    }//try-catch

    return response()->json([
      'data' => [
        'msg' => 'Marcador: n°'.$id.' atualizado para: [ '.$new_tag['caractere'].' => '.$new_tag['marcador'].' ].....com sucesso!'
      ]
    ], 200);//return
  }


  public function destroy($id)
  {
    try{
      $tag = Marcador::findOrFail($id);
      $tag->delete();
    }catch(Exception $e){
        return response()->json([
          'data' => [
            'msg' => 'Marcador: NÃO EXCLUÍDO!'
          ]
        ], 200);
    }//try-catch

    return response()->json([
      'data' => [
        'msg' => 'Marcador: n°'.$id.' EXCLUÍDO.....com sucesso!'
      ]
    ], 200);//return
    
  }
}
