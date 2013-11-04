<?php

namespace Anh\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryType extends AbstractType
{
    /**
     * @var string
     *
     * Category entity class
     */
    protected $categoryClass;

    public function __construct($categoryClass, $sections)
    {
        $this->categoryClass = $categoryClass;
        $this->sections = $sections;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->sections as $section => $config) {
            if ($config['category']) {
                $choices[$section] = $section;
            }
        }

        if (empty($choices)) {
            throw new \InvalidArgumentException('There is no sections with categories.');
        }

        $builder
            ->add('section', 'choice', array(
                'choices' => $choices
            ))
            ->add('title', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('slug', 'text')
            ->add('submit', 'submit')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->categoryClass
        ));
    }

    public function getName()
    {
        return 'anh_content_form_type_category';
    }
}
