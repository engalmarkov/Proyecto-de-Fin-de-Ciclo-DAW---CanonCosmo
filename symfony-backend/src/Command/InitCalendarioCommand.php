<?php

namespace App\Command;

use App\Entity\EventoCalendario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-calendario',
    description: 'Inicializa los 7 días de la semana en la tabla de EventoCalendario',
)]
class InitCalendarioCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 1. Verificamos si ya hay datos para no duplicar
        $repo = $this->em->getRepository(EventoCalendario::class);
        if (count($repo->findAll()) > 0) {
            $io->warning('El calendario ya tiene datos. No se ha insertado nada para evitar duplicados.');
            return Command::SUCCESS;
        }

        // 2. Definimos los días iniciales
        $diasIniciales = [
            ['dia' => 'Lunes', 'juegos' => 'Descanso', 'festivo' => true],
            ['dia' => 'Martes', 'juegos' => 'Digimon TCG', 'festivo' => false],
            ['dia' => 'Miércoles', 'juegos' => 'Gundam TCG', 'festivo' => false],
            ['dia' => 'Jueves', 'juegos' => 'Beyblade', 'festivo' => false],
            ['dia' => 'Viernes', 'juegos' => 'Riftbound', 'festivo' => false],
            ['dia' => 'Sábado', 'juegos' => 'Pokémon TCG (mañana) - One Piece TCG (tarde)', 'festivo' => false],
            ['dia' => 'Domingo', 'juegos' => 'Final Fantasy TCG', 'festivo' => false],
        ];

        // 3. Los creamos y los preparamos para guardar
        foreach ($diasIniciales as $data) {
            $evento = new EventoCalendario();
            $evento->setDia($data['dia']);
            $evento->setJuegos($data['juegos']);
            $evento->setEsFestivo($data['festivo']);

            $this->em->persist($evento);
        }

        // 4. Guardamos todo en la base de datos
        $this->em->flush();

        $io->success('¡Misión cumplida! Los 7 días del calendario se han insertado correctamente en la base de datos.');

        return Command::SUCCESS;
    }
}