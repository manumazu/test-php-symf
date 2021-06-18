<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;

class ImportCsvCommand extends Command
{
    protected static $defaultName = 'app:import-csv';
    protected static $defaultDescription = 'Add a short description for your command';

    public function __construct($projectDir, EntityManagerInterface $entityManager) {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::REQUIRED, 'Give the file name')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //parse CSV
        $fileName = $input->getArgument('filename');
        $productsArray = $this->setCsvArray($fileName);

        $productRepo = $this->entityManager->getRepository(Product::class);
        
        $cptUpdated = 0;
        $cptAdded = 0;
        foreach($productsArray as $product) {
            //dd($product); 
            //update datas in db
            if($existingProduct = $productRepo->findOneBy(['isbn13' => $product['isbn']])) {
                $existingProduct->setTitle($product['title']);
                $existingProduct->setAuthor($product['author']);
                $this->entityManager->persist($existingProduct);
                //dd($existingProduct); 
                $cptUpdated++;
                continue;               
            }

            $newProduct = new Product();
            $newProduct->setIsbn13($product['isbn']);
            $newProduct->setTitle($product['title']);
            $newProduct->setAuthor($product['author']);
            $this->entityManager->persist($newProduct);
            $cptAdded++;

        }

        $this->entityManager->flush();

        $io = new SymfonyStyle($input, $output);
        $io->success('Importation ok : ' . $cptUpdated . ' updated and ' . $cptAdded . ' added');

        return Command::SUCCESS;
    }

    /**
     * encode csv rows in readable array
     **/
    private function setCsvArray($filename) 
    {
        $inputFile = $this->projectDir . '/public/' . $filename;
        $decoder = new Serializer([],[new CsvEncoder()]);
        return $decoder->decode(file_get_contents($inputFile), 'csv');
    }
}
