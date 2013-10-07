<?php

namespace Anh\Bundle\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;

class DocumentType extends AbstractType
{
    /**
     * @var string
     *
     * Document entity class
     */
    protected $documentClass;

    /**
     * @var string
     *
     * Category entity class
     */
    protected $categoryClass;

    /**
     * @var array
     *
     * Sections
     */
    protected $sections;

    public function __construct($documentClass, $categoryClass, $sections)
    {
        $this->documentClass = $documentClass;
        $this->categoryClass = $categoryClass;
        $this->sections = $sections;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $section = $builder->getData()->getSection();

        if (empty($this->sections[$section])) {
            throw new \InvalidArgumentException("Section '{$section}' not configured.");
        }

        $config = $this->sections[$section];

        if ($config['category']) {
            $builder
                ->add('category', 'entity', array(
                    'class' => $this->categoryClass,
                    'property' => 'title',
                    'query_builder' => function(EntityRepository $er) use ($section) {
                        return $er->createQueryBuilder('c')
                            ->where('c.section = :section')
                            ->setParameter('section', $section)
                            ->orderBy('c.title', 'ASC')
                        ;
                    }
                ))
            ;
        }

        $builder
            ->add('title', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                )
            ))
        ;

        if ($config['slug']) {
            $builder
                ->add('slug', 'text')
            ;
        }

        if ($config['publishedSince']) {
            $builder
                ->add('publishedSince', 'datetime', array(
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd HH:mm:ss',
                    'attr' => array(
                        'class' => 'datetime-picker'
                    )
                ))
            ;
        }

        $builder
            ->add('markup', 'textarea', array(
                'attr' => array(
                    'class' => 'bbcode'
                )
            ))
            ->add('isDraft', 'checkbox')
            ->add('submit', 'submit')
        ;

        if ($config['image']) {
            $builder
                ->add('image', 'hidden')
            ;
        }

        $builder->add(
            $builder->create('assets', 'hidden')
                ->addModelTransformer(new ArrayToJsonTransformer())
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->documentClass
        ));
    }

    public function getName()
    {
        return 'anh_content_form_type_document';
    }
}
