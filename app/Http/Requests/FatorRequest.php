<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FatorRequest extends FormRequest
{
  /*********
   * 
   *    Use no cabeçalho (header) na requisição para receber o json:
   *    Accept:application/json
   * 
   ********/
    
  /**
   * Determine if the user is authorized to make this request. 
   * @return bool
   */
  public function authorize():bool
  {
    return true;
  }

  public function rules():array
  {
    return [
      'fator' => ['required', 'numeric', 'integer', 'between:-11,11']
    ];
  }
    
  public function messages(): array
  {
    return [
      'fator.required' => 'O campo :attribute é um campo obrigatório.',
      'fator.numeric' => 'O campo :attribute deve ser preenchido com um número. E evite a vírgula.',
      'fator.integer' => 'Um número decimal torna o campo :attribute inválido. Evite vírgula e ponto.',
      'between' => ['numeric' => 'O campo :attribute deve ter um número entre :min e :max.'],
    ];
  }
}
