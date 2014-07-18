<?php

namespace Anh\ContentBundle;

use Symfony\Component\HttpFoundation\ParameterBag;
use Anh\FeedBundle\AbstractDataProvider;
use Anh\ContentBundle\Entity\PaperRepository;
use Anh\ContentBundle\Entity\CategoryRepository;
use Anh\ContentBundle\UrlGenerator as ContentUrlGenerator;
use Anh\FeedBundle\UrlGenerator as FeedUrlGenerator;
use DateTime;

class FeedDataProvider extends AbstractDataProvider
{
    protected $conditions;

    protected $options;

    protected $paperRepository;
    protected $categoryRepository;

    protected $contentUrlGenerator;
    protected $feedUrlGenerator;

    public function __construct(
        PaperRepository $paperRepository,
        CategoryRepository $categoryRepository,
        ContentUrlGenerator $contentUrlGenerator,
        FeedUrlGenerator $feedUrlGenerator
    ) {
        $this->paperRepository = $paperRepository;
        $this->categoryRepository = $categoryRepository;

        $this->contentUrlGenerator = $contentUrlGenerator;
        $this->feedUrlGenerator = $feedUrlGenerator;

        $this->conditions = new ParameterBag();
        $this->options = new ParameterBag();
    }

    public function getType()
    {
        return 'atom';
    }

    public function getUpdatedAt(array $parameters = array())
    {
        $updatedAt = null;

        $criteria = array(
            'section' => $this->conditions->get('section'),
            '[isPublished]',
        );

        if (isset($parameters['category'])) {
            $criteria['category'] = $this->findCategory(
                $criteria['section'],
                $parameters['category']
            );
        }

        $updatedAt = $this->paperRepository
            ->findLatestUpdateDate($criteria)
        ;

        return new DateTime($updatedAt);
    }

    public function getData($feed, array $parameters)
    {
        $link = $this->feedUrlGenerator->generate(
            $feed,
            $parameters
        );

        return array_merge(
            $this->generateFeed($link, $parameters),
            $this->generateEntries($parameters)
        );
    }

    public function setOptions(array $options)
    {
        $this->options->replace($options);
    }

    public function setConditions(array $conditions)
    {
        $this->conditions->replace($conditions);
    }

    protected function generateFeed($link, $parameters)
    {
        $data = array(
            'link0' => array(
                'rel' => 'self',
                'type' => 'application/atom+xml',
                'href' => $link
            ),
            'id' => $link,
            'updated' => $this->getUpdatedAt($parameters),
        );

        if ($this->options->has('author')) {
            $data['author'] = $this->options->get('author');
        }

        if ($this->options->has('title')) {
            $data['title'] = $this->options->get('title');
        }

        if ($this->options->has('link')) {
            $data['link1'] = array(
                'type' => 'text/html',
                'rel' => 'alternate',
                $this->options->get('link')
            );
        }

        if ($this->options->has('copyright')) {
            $data['rights'] = $this->options->get('copyright');
        }

        if ($this->options->has('icon')) {
            $data['icon'] = $this->options->get('icon');
        }

        if ($this->options->has('logo')) {
            $data['logo'] = $this->options->get('logo');
        }

        return $data;
    }

    protected function generateEntries(array $parameters)
    {
        $data = array();

        $papers = $this->getPapers($parameters);

        foreach ($papers as $key => $paper) {
            $key = sprintf('entry%d', $key);
            $link = $this->contentUrlGenerator->resolveAndGenerate($paper, true);

            $data[$key] = array(
                'title' => $paper->getTitle(),
                'id' => $link,
                'link' => array(
                    'rel' => 'alternate',
                    $link,
                ),
                'published' => $paper->getPublishedSince(),
                'updated' => $paper->getUpdatedAt(),
            );

            if ($this->options->get('summary', false)) {
                $data[$key]['summary'] = array(
                    'type' => 'html',
                    $paper->getPreview()
                );
            } else {
                $data[$key]['content'] = array(
                    'type' => 'html',
                    $paper->getContent()
                );
            }

            if ($paper->getCategory()) {
                $category = $paper->getCategory();

                $data[$key]['category'] = array(
                    'term' => $category->getTitle(),
                    'scheme' => $this->contentUrlGenerator->resolveAndGenerate(
                        $category,
                        true
                    )
                );
            }
        }

        return $data;
    }

    protected function getPapers(array $parameters)
    {
        $sorting = array('publishedSince' => 'DESC');

        $modifiedSince = isset($parameters['modifiedSince']) ? $parameters['modifiedSince'] : null;
        if (is_string($modifiedSince)) {
            $modifiedSince = new DateTime($modifiedSince);
        }

        $criteria = array(
            'section' => $this->conditions->get('section'),
            '[isPublished]',
        );

        if (isset($parameters['category'])) {
            $criteria['category'] = $this->findCategory(
                $criteria['section'],
                $parameters['category']
            );
        }

        if ($modifiedSince) {
            $criteria['%updatedAt'] = array('>' => $modifiedSince);
        }

        return $this->paperRepository->fetch($criteria, $sorting);
    }

    protected function findCategory($section, $slug)
    {
        $category = $this->categoryRepository->findOneBy(array(
            'section' => $section,
            'slug' => $slug,
        ));

        return $category;
    }
}
