<?php

namespace Anh\ContentBundle;

use Symfony\Component\HttpFoundation\ParameterBag;
use Anh\FeedBundle\AbstractDataProvider;
use Anh\ContentBundle\Entity\PaperManager;
use Anh\ContentBundle\Entity\CategoryManager;
use Anh\ContentBundle\UrlGenerator as ContentUrlGenerator;
use Anh\FeedBundle\UrlGenerator as FeedUrlGenerator;
use DateTime;

class FeedDataProvider extends AbstractDataProvider
{
    protected $conditions;

    protected $options;

    protected $feedName;

    protected $paperManager;

    protected $contentUrlGenerator;

    protected $feedUrlGenerator;

    public function __construct(PaperManager $paperManager, CategoryManager $categoryManager, ContentUrlGenerator $contentUrlGenerator, FeedUrlGenerator $feedUrlGenerator)
    {
        $this->paperManager = $paperManager;
        $this->categoryManager = $categoryManager;

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

        if (isset($parameters['category'])) {
            $category = $this->findCategory(
                $this->conditions->get('section'),
                $parameters['category']
            );

            $updatedAt = $this->paperManager
                ->findMaxPublishedUpdatedAtInSectionAndCategory(
                    $this->conditions->get('section'),
                    $category
                )
            ;
        } else {
            $updatedAt = $this->paperManager
                ->findMaxPublishedUpdatedAtInSection(
                    $this->conditions->get('section')
                )
            ;
        }

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
            'link' => array(
                'rel' => 'self',
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
                'link' => $link,
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
        $section = $this->conditions->get('section');
        $modifiedSince = isset($parameters['modifiedSince']) ? $parameters['modifiedSince'] : null;

        if (is_string($modifiedSince)) {
            $modifiedSince = new DateTime($modifiedSince);
        }

        if (isset($parameters['category'])) {
            $category = $this->findCategory(
                $section,
                $parameters['category']
            );

            return $this->paperManager->findPublishedInSectionAndCategory(
                $section,
                $category,
                $modifiedSince
            );
        }

        return $this->paperManager->findPublishedInSection(
            $this->conditions->get('section'),
            $modifiedSince
        );
    }

    protected function findCategory($section, $slug)
    {
        $category = $this->categoryManager->findInSectionBySlug(
            $section,
            $slug
        );

        return $category;
    }
}