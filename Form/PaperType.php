<?php

namespace Anh\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        $section = $options['section'];

        $builder->add('section', 'hidden', array(
            'data' => $section,
        ));

        $config = $this->sections[$section];

        if ($config['category'] && !in_array('category', $options['hidden_fields'])) {
            $builder
                ->add('category', 'entity', array(
                    'class' => $this->categoryClass,
                    'property' => 'title',
                    'query_builder' => function(EntityRepository $repository) use ($section) {
                        return $repository->prepareQueryBuilder(
                            [ 'section' => $section ], // criteria
                            [ 'title' => 'asc' ] // sorting
                        );
                    }
                ))
            ;
        }

        $builder
            ->add('title', 'text')
        ;

        $builder
            ->add('slug', 'text', array('required' => false))
        ;

        if ($config['publishedSince'] && !in_array('publishedSince', $options['hidden_fields'])) {
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
        ;

        if(!in_array('isDraft', $options['hidden_fields'])) {
            $builder
                ->add('isDraft', 'checkbox', array('required' => false))
            ;
        }

        if ($config['tags']) {
            $builder
                ->add('tags', 'tags', array('required' => false))
            ;
        }

        if ($config['meta']) {
            if (!in_array('metaAuthor', $options['hidden_fields'])) {
                $builder
                    ->add('metaAuthor', 'text', array('required' => false))
                ;
            }

            if (!in_array('metaDescription', $options['hidden_fields'])) {
                $builder
                    ->add('metaDescription', 'text', array('required' => false))
                ;
            }

            if (!in_array('metaKeywords', $options['hidden_fields'])) {
                $builder
                    ->add('metaKeywords', 'text', array('required' => false))
                ;
            }
        }

        $builder
            ->add('save', 'submit')
            ->add('save_and_preview', 'submit')
        ;

        $builder
            ->add('image', 'hidden', array(
                'attr' => array(
                    'class' => 'anh_content_editor_image',
                ),
            ))
        ;

        $builder->add(
            $builder->create('assets', 'hidden', array(
                    'attr' => array(
                        'class' => 'anh_content_editor_assets',
                    ),
                ))
                ->addModelTransformer(new ArrayToJsonTransformer())
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->paperClass,
            'hidden_fields' => array()
        ));

        $resolver->setRequired(array(
            'section',
        ));

        $resolver->setAllowedValues(array(
            'section' => array_keys($this->sections),
        ));
    }

    public function getName()
    {
        return 'anh_content_form_type_paper';
    }
}
