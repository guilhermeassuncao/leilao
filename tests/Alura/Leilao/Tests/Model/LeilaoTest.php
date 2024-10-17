<?php

namespace Alura\Leilao\Tests\Model;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Lance;

class LeilaoTest extends TestCase
{

    public function testLeilaoNaoDeveReceberLancesRepetidos()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor 2 lances consecutivos');

        $leilao = new Leilao('Cobalt');
        $ian = new Usuario('Ian');

        $leilao->recebeLance(new Lance($ian, 1000));
        $leilao->recebeLance(new Lance($ian, 1500));

        static::assertCount(1, $leilao->getLances());
        static::assertEquals(1000, $leilao->getLances()[0]->getValor());
    }

    public function testeLeilaoNaoPodeAceitarMaisDe5LancesPorUsuario (){
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor mais de 5 lances por leilão');
        
        $leilao = new Leilao('Ford Fiesta');
        $ian = new Usuario('Ian');
        $cecilia = new Usuario('Cecilia');

        $leilao->recebeLance(new Lance($ian, 1000));
        $leilao->recebeLance(new Lance($cecilia, 1500));

        $leilao->recebeLance(new Lance($ian, 2000));
        $leilao->recebeLance(new Lance($cecilia, 2500));

        $leilao->recebeLance(new Lance($ian, 3000));
        $leilao->recebeLance(new Lance($cecilia, 3500));

        $leilao->recebeLance(new Lance($ian, 4000));
        $leilao->recebeLance(new Lance($cecilia, 4500));

        $leilao->recebeLance(new Lance($ian, 5000));
        $leilao->recebeLance(new Lance($cecilia, 5500));

        $leilao->recebeLance(new Lance($ian, 6000));

        static::assertCount(10, $leilao->getLances());
        static::assertEquals(5500, $leilao->getLances()[array_key_last($leilao->getLances())]->getValor());
    }

    /**
     * @dataProvider gerarLances
     */
    public function testLeilaoDeveReceberLances(int $quantidadeLances, Leilao $leilao, array $valores)
    {
        static::assertCount($quantidadeLances, $leilao->getLances());

        foreach ($valores as $i => $valorEsperado) {
            static::assertEquals($valorEsperado, $leilao->getLances()[$i]->getValor());
        }
    }

    public static function gerarLances()
    {
        $ian = new Usuario('Ian');
        $cecilia = new Usuario('Cecilia');

        $leilaoCom1Lance = new Leilao('Fiat 147');
        $leilaoCom1Lance->recebeLance(new Lance($ian, 1000));


        $leilao2Lances = new Leilao('Cobalt');
        $leilao2Lances->recebeLance(new Lance($ian, 1000));
        $leilao2Lances->recebeLance(new Lance($cecilia, 1500));

        return [
            '1-lance' => [1, $leilaoCom1Lance, [1000]],
            '2-lances' => [2, $leilao2Lances, [1000, 1500]]
        ];
    }
}