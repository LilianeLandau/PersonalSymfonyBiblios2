<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Enum\BookStatus;
use App\Enum\BookStatusEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


//dans AbstractType on implémente déjà l'interface
//use Symfony\Component\Form\FormTypeInterface;
//en étendant la classe AbstractType, on a accès à la méthode buildForm() qui permet de définir les champs du formulaire
//et à la méthode configureOptions() qui permet de définir les options du formulaire
//on définit les champs du formulaire avec la méthode add() de l'objet $builder
//on définit les options du formulaire avec la méthode setDefaults() de l'objet $resolver
class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('isbn', type: TextType::class)
            ->add('cover', UrlType::class)
            ->add('editedAt', DateType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
            ])
            ->add('plot', TextareaType::class)
            ->add('pageNumber', NumberType::class)
            ->add('status', EnumType::class, [
                'class' => BookStatus::class,
            ])

            //un BOOK peut avoir plusieurs AUTHOR et inveresement
            //by_reference: false : veut dire que Symfony va appeler 
            //les méthodes addAuthor() et removeAuthor() de l'entité Book
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'name',
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('editor', EntityType::class, [
                'class' => Editor::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
