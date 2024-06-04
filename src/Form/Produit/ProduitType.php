<?php

namespace App\Form\Produit;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_produit')
            ->add('description_produit')
            ->add('prix_produit')
            ->add('categorie_produit')
            ->add('code_barre_produit')
            ->add('image_produit', FileType::class,  [
                'label' => 'Choose file',
                'mapped' => false, // If not mapped to an entity property
            ])
            ->add('quantity_produit');

           // Применяем преобразователь данных к полю 'image_produit'
        $builder->get('image_produit')->addModelTransformer(new class implements DataTransformerInterface
        {
            public function transform($value): mixed
            {
                // Возвращаем путь к файлу для отображения в форме
                return $value;
            }
            public function reverseTransform($value): mixed
            {
                // Преобразуем строку пути к файлу в объект файла
                return new File($value);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
         // Добавление атрибута enctype для формы
        'attr' => ['enctype' => 'multipart/form-data'],
        ]);
    }

    public function transform($image_produit)
    {
        // Transform the File object to a string (file path)
        if (null === $image_produit) {
            return '';
        }
        return $image_produit->getPathname();
    }

    public function reverseTransform($path)
    {
        // Transform the string (file path) back to a File object
        if (empty($path)) {
            return null;
        }

        return new File($path);
    }
}
