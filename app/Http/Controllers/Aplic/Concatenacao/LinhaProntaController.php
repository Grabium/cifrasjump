<?php

namespace App\Http\Controllers\Aplic\Concatenacao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LinhaProntaController extends Controller
{
  public string $tipo = 'linha'; // linha ou cifra
  public string $conteudo;

  public function __construct(string $linha)
  {
    $this->conteudo = $linha;
  }
}
