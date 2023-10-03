<?php
//
//namespace App\Tests\Repository;
//
//use App\Repository\ImageRepository;
//use App\Repository\ImageRepositoryInterface;
//use Doctrine\DBAL\Connection;
//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//
//class ImageRepositoryTest extends WebTestCase
//{
//    /**
//     * @before
//     */
//    public function start()
//    {
//        $this::getContainer()->get('doctrine.dbal.default_connection')->beginTransaction();
//    }
//
//    public function testGetImagesForFossils(): void
//    {
//        $sql = file_get_contents(__DIR__ . '/_fixtures/images.sql');
//        static::assertIsString($sql);
//
//        $this::getContainer()->get(Connection::class)->executeQuery($sql);
//
//        $result = $this->createImageRepository()->getImagesForFossils([1, 2, 3, 4, 5]);
//
//        static::assertCount(15, $result);
//    }
//
//    private function createImageRepository(): ImageRepositoryInterface
//    {
//        return new ImageRepository($this::getContainer()->get('doctrine.dbal.default_connection'));
//    }
//}