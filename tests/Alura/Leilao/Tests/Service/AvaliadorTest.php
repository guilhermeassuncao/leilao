<?php
namespace Alura\Leilao\Tests\Service;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Service\Avaliador;

class AvaliadorTest extends TestCase
{
    private $leiloeiro;

    protected function setUp(): void
    {
        parent::setUp();

        $this->leiloeiro = new Avaliador();
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testMaiorValor(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maiorValor = $this->leiloeiro->getMaiorValor();

        self::assertEquals(2500, $maiorValor); // Corrigido para 2500
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testMenorValor(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $menorValor = $this->leiloeiro->getMenorValor();

        self::assertEquals(1000, $menorValor);
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function test3MaioresLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);

        $maioresLances = $this->leiloeiro->getMaioresLances();

        self::assertCount(3, $maioresLances);
        self::assertEquals(2500, $maioresLances[0]->getValor());
        self::assertEquals(2000, $maioresLances[1]->getValor());
        self::assertEquals(1700, $maioresLances[2]->getValor());
    }

    public function testLeilaoVazioNaoPodeSerAvaliado() {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Não é possível avaliar um leilão vazio');

        $leilao = new Leilao('Fusca');

        $this->leiloeiro->avalia($leilao);
    }

    public function testLeilaoFinalizadoNaoPodeReceberLances() {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Leilão já finalizado');

        $leilao = new Leilao('Fiat 147');

        $leilao->recebeLance(new Lance(new Usuario('Ian'), 1000));
        $leilao->finaliza();

        $this->leiloeiro->avalia($leilao);
    }

    public static function criarLeilaoOrdemCrescente()
    {
        $leilao = new Leilao('Cobalt');

        $ian = new Usuario('Ian');
        $cecilia = new Usuario('Cecilia');
        $vitor = new Usuario('Vitor');
        $snow = new Usuario('Snow');
        $fred = new Usuario('Fred');

        $leilao->recebeLance(new Lance($ian, 1000));
        $leilao->recebeLance(new Lance($cecilia, 1500));
        $leilao->recebeLance(new Lance($snow, 1700));
        $leilao->recebeLance(new Lance($vitor, 2000));
        $leilao->recebeLance(new Lance($fred, 2500));

        return $leilao;
    }

    public static function criarLeilaoOrdemDescrecente()
    {
        $leilao = new Leilao('Cobalt');

        $ian = new Usuario('Ian');
        $cecilia = new Usuario('Cecilia');
        $vitor = new Usuario('Vitor');
        $snow = new Usuario('Snow');
        $fred = new Usuario('Fred');

        $leilao->recebeLance(new Lance($fred, 2500));
        $leilao->recebeLance(new Lance($vitor, 2000));
        $leilao->recebeLance(new Lance($snow, 1700));
        $leilao->recebeLance(new Lance($cecilia, 1500));
        $leilao->recebeLance(new Lance($ian, 1000));

        return $leilao;
    }

    public static function criarLeilaoOrdemAleatoria()
    {
        $leilao = new Leilao('Cobalt');

        $ian = new Usuario('Ian');
        $cecilia = new Usuario('Cecilia');
        $vitor = new Usuario('Vitor');
        $snow = new Usuario('Snow');
        $fred = new Usuario('Fred');

        $leilao->recebeLance(new Lance($snow, 1700));
        $leilao->recebeLance(new Lance($fred, 2500));
        $leilao->recebeLance(new Lance($vitor, 2000));
        $leilao->recebeLance(new Lance($ian, 1000));
        $leilao->recebeLance(new Lance($cecilia, 1500));

        return $leilao;
    }

    public static function entregaLeiloes()
    {
        return [
            'ordem-crescente' => [self::criarLeilaoOrdemCrescente()],
            'ordem-descrecente' => [self::criarLeilaoOrdemDescrecente()],
            'orderm-aleatoria' => [self::criarLeilaoOrdemAleatoria()]
        ];
    }
}