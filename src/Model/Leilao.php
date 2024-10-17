<?php

namespace Alura\Leilao\Model;

class Leilao
{
    private $lances;
    private $descricao;
    private $finalizado;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
        $this->finalizado = false;
    }

    private function ehDoUltimoUsuario(Lance $lance): bool
    {
        $ultimoLance = end($this->lances);
        
        return $lance->getUsuario() == $ultimoLance->getUsuario();
    }

    private function quantidadeLancePorUsuario(Lance $lance) {
        return array_reduce(
            $this->lances,
            function (int $totalAcumulado, Lance $lanceAtual) use ($lance) {
                if ($lanceAtual->getUsuario() == $lance->getUsuario()) {
                    return $totalAcumulado + 1;
                }
                return $totalAcumulado;
            },
            0
        );
    }


    public function recebeLance(Lance $lance)
    {
        if (!empty($this->lances) && $this->ehDoUltimoUsuario($lance)) {
            throw new \DomainException('Usuário não pode propor 2 lances consecutivos');
        }

        $totalLancesUsuario = $this->quantidadeLancePorUsuario($lance);

        if($totalLancesUsuario >= 5) {
            throw new \DomainException('Usuário não pode propor mais de 5 lances por leilão');
        }
        
        $this->lances[] = $lance;
    }

    public function getLances(): array
    {
        return $this->lances;
    }

    public function finaliza()
    {
        $this->finalizado = true;
    }

    public function estaFinalizado(): bool
    {
        return $this->finalizado;
    }
}
