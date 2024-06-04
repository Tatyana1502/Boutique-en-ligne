<?php

namespace App\Form\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserRoleType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('roles', ChoiceType::class, [
            'label' => 'Choisi role',
            'choices' => $this->getUserRole(),
            'multiple' => true, // Assuming roles are multiple selection
            'attr' => [
                'class' => 'form-select',
            ],
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    private function getUserRole()
    {
        $query = $this->entityManager->createQuery('SELECT DISTINCT user.roles FROM App\Entity\User user');
        $users = $query->getResult();
        $roles = [];
        foreach ($users as $user) 
        {
            foreach ($user['roles'] as $role) 
            {
                $roles[$role] = $role; // Use the role string as both the key and value
            }
        }
        return $roles;
    }
}
        
