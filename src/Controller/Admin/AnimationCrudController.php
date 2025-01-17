<?php

namespace App\Controller\Admin;

use App\Entity\Animation;
use App\Entity\User;
use App\Enum\ScheduleAnimationState;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class AnimationCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return Animation::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        $qb->innerJoin('entity.creators', 'creators')
            ->addSelect('creators')
            ->andWhere('creators IN (:creator)')
            ->setParameter('creator', $this->getUser())
        ;

        return $qb;
    }

    /**
     * @param Animation $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var User $user */
        $user = $this->getUser();
        $entityInstance->addCreator($user);

        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * @param Animation $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var User $user */
        $user = $this->getUser();
        $entityInstance->addCreator($user);

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function createEntity(string $entityFqcn): Animation
    {
        /** @var Animation $entityInstance */
        $entityInstance = parent::createEntity($entityFqcn);

        /** @var User $user */
        $user = $this->getUser();
        $entityInstance->addCreator($user);

        return $entityInstance;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field\TextField::new('name'),
            Field\TextEditorField::new('description')->setRequired(false)->hideOnIndex(),
            Field\AssociationField::new('creators')->setHelp('The users that are allowed to manage this animation. Note: you will always be included in this list, even if you remove yourself from it.'),
        ];
    }
}
