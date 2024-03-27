<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TipoMarcador;

class MaintenanceTipoMarcadorController extends Controller
{
  public function index()
  {
    $tipos_marcadores = TipoMarcador::all();
    return response()->json($tipos_marcadores);
  }

  public function store(Request $request)
  {
    $data = $request->all(); //para retorno

    try{
      
      $new_type = new TipoMarcador();
      $new_type->tipo = $request['type_name'];
      $new_type->save();
    
    } catch (\Exception $e) {
      return response()->json([
        'data' => [
          'msg' => 'Tipo: '.$data['type_name'].'...NÃO CADASTRADO!'
        ]
      ], 401);
    }

    return response()->json([
      'data' => [
        'msg' => 'Tipo: '.$data['type_name'].' cadastrado com sucesso!'
      ]
    ], 200);

    
  }

  public function show($id)
  {
    try{
      
      $type = TipoMarcador::findOrFail($id);
    
    } catch (\Exception $e) {
      return response()->json([
        'data' => [
          'msg' => 'Tipo n°: '.$id.'...NÃO ENCONTRADO!'
        ]
      ], 401);
    }

    return response()->json([
      'data' => $type
    ], 200);
  }

  public function update(Request $request, $id)
  {
    $new_type = $request->all();

    try{
      
      $old_type = TipoMarcador::findOrFail($id);
      $old_type->update($new_type);

    } catch (\Exception $e) {
      return response()->json([
        'data' => [
          'msg' => 'Tipo n°: '.$id.'...NÃO ATUALIZADO!'
        ]
      ], 401);
    }

    return response()->json([
      'data' => [
        'msg' => 'Tipo atualizado com sucesso!'
      ]
    ], 200);

    
  }


  public function destroy($id)
  {
    try{
      
      $to_destroy = TipoMarcador::findOrFail($id);
      $data = $to_destroy['tipo'];//para resposta.
      $to_destroy->delete();

    } catch (\Exception $e) {
      return response()->json([
        'data' => [
            'msg' => 'Tipo n°: '.$id.'...NÃO DELETADO!'
        ]
      ], 401);
    }
    
    return response()->json([
      'data' => [
        'msg' => 'Tipo: '.$data.' deletado com sucesso!'
      ]
    ], 200);
  }
}
