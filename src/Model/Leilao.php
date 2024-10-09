<?php

namespace Alura\Leilao\Model;

class Leilao
{
    private $lances;
    private $descricao;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
    }

    public function recebeLance(Lance $lance)
    {
        $this->lances[] = $lance;
    }

    public function getLances(): array
    {
        return $this->lances;
    }
}
