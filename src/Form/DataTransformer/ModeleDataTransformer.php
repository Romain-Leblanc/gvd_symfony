<?php
namespace App\Form\DataTransformer;


use App\Entity\Modele;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;


class ModeleDataTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)

    {
        $this->entityManager = $entityManager;
    }

    /**
     * Transforms the numeric array (1,2,3,4) to a collection of Categories (Categories[])
     *
     * @param Array|null $categories
     * @return array
     */
    public function transform($categoriesNumber): array
    {
        $result = [];

        if (null === $categoriesNumber) {
            return $result;
        }
        dd($this->entityManager->getRepository(Modele::class)->find($categoriesNumber));

        return $this->entityManager
            ->getRepository(Modele::class)
//            ->findBy(["id" => $categoriesNumber])
            ->find($categoriesNumber)
        ;
    }

    /**
     * In this case, the reverseTransform can be empty.
     *
     * @param type $value
     * @return array
     */
    public function reverseTransform($value): array
    {
        return [];
    }
}