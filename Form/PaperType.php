<?php

namespace Anh\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;

use Anh\Taggable\TaggableManager;

class PaperType extends AbstractType
{
    /**
     * Paper entity class
     * @var string
     */
    protected $paperClass;

    /**
     * Category entity class
     * @var string
     */
    protected $categoryClass;

    /**
     * Sections config
     * @var array
     */
    protected $sections;

    /**
     * TaggableManager
     * @var \Anh\Taggable\TaggableManager
     */

    public function __construct($paperClass, $categoryClass, $sections, TaggableManager $taggableManager)
    {
        $this->paperClass = $paperClass;
        $this->categoryClass = $categoryClass;
        $this->sections = $sections;
        $this->taggableManager = $taggableManager;
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
                ->add('slug', 'text', array('required' => false))
            ;
        }

        if ($config['publishedSince']) {
            $builder
                ->add('publishedSince', 'datetime', array(
                    'picker' => true,
                    'format' => 'dd.MM.yyyy HH:mm:ss',
                    // 'format' => 'yyyy-MM-dd HH:mm:ss',
                    'separator' => ' ',
                    'required' => false
                ))
            ;
        }

        $builder
            ->add('markup', 'textarea', array(
                'attr' => array(
                    'class' => 'bbcode'
                ),
                'required' => false
            ))
            ->add('isDraft', 'checkbox', array('required' => false))
        ;

        if ($config['tags']) {
            $builder
                ->add('tags', 'tags', array('required' => false))
            ;
        }

        if ($config['meta']) {
            $builder
                ->add('metaAuthor', 'text')
                ->add('metaDescription', 'text')
                ->add('metaKeywords', 'text')
            ;
        }

        $builder
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
            'data_class' => $this->paperClass
        ));
    }

    public function getName()
    {
        return 'anh_content_form_type_paper';
    }
}
