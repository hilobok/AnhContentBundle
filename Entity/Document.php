<?php

namespace Anh\Bundle\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Anh\Bundle\MarkupBundle\Mapping\Annotation\Parsable as Markup;
use Anh\Bundle\MarkupBundle\Validator\Markup as MarkupValidator;

use Anh\Bundle\ContentBundle\Entity\Category;

/**
 * Document
 *
 * @ORM\Table(indexes={
 *      @ORM\Index(name="idx_title", columns={ "title" }),
 *      @ORM\Index(name="idx_section", columns={ "section" }),
 *      @ORM\Index(name="idx_slug", columns={ "slug" }),
 *      @ORM\Index(name="idx_createdAt", columns={ "createdAt" }),
 *      @ORM\Index(name="idx_updatedAt", columns={ "updatedAt" }),
 *      @ORM\Index(name="idx_publishedSince", columns={ "publishedSince" }),
 *      @ORM\Index(name="idx_isDraft", columns={ "isDraft" })
 * })
 * @ORM\Entity(repositoryClass="Anh\Bundle\ContentBundle\Entity\DocumentRepository")
 */
class Document
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="section", type="string", length=50)
     */
    protected $section;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="documents")
     * @ORM\JoinColumn(name="categoryId", referencedColumnName="id")
     */
    protected $category;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publishedSince", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $publishedSince;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"title"}, updatable=false, unique=true, unique_base="section")
     */
    protected $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="markup", type="text", nullable=true)
     * @MarkupValidator(type="bbcode")
     */
    protected $markup;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     * @Markup(type="bbcode", field="markup")
     */
    protected $content;

    /**
     * @var string
     *
     * @ORM\Column(name="preview", type="text", nullable=true)
     * @Markup(type="bbcode", field="markup", options={"previewOnly"=true})
     */
    protected $preview;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDraft", type="boolean")
     */
    protected $isDraft;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    protected $image;

    /**
     * @var array
     *
     * @ORM\Column(name="assets", type="array", nullable=true)
     */
    protected $assets;

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get assets
     *
     * @return array
     */
    public function getAssets()
    {
        return (array) $this->assets;
    }

    /**
     * Set assets
     *
     * @param  array    $assets
     * @return Document
     */
    public function setAssets($assets)
    {
        $this->assets = $assets;

        return $this;
    }

    /**
     * Returns fields on which markup depends (besides markup field).
     *
     * @return array
     */
    public function getMarkupMasterFields()
    {
        return array(
            'id' => $this->getId(),
            'slug' => $this->getSlug(),
            'section' => $this->getSection()
        );
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set section
     *
     * @param  string   $section
     * @return Document
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return string
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Document
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param  \DateTime $updatedAt
     * @return Document
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set publishedSince
     *
     * @param  \DateTime $publishedSince
     * @return Document
     */
    public function setpublishedSince($publishedSince)
    {
        $this->publishedSince = $publishedSince;

        return $this;
    }

    /**
     * Get publishedSince
     *
     * @return \DateTime
     */
    public function getpublishedSince()
    {
        return $this->publishedSince;
    }

    /**
     * Set title
     *
     * @param  string   $title
     * @return Document
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param  string $slug
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set markup
     *
     * @param  string   $markup
     * @return Document
     */
    public function setMarkup($markup)
    {
        $this->markup = $markup;

        return $this;
    }

    /**
     * Get markup
     *
     * @return string
     */
    public function getMarkup()
    {
        return $this->markup;
    }

    /**
     * Set content
     *
     * @param  string   $content
     * @return Document
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set preview
     *
     * @param  string   $preview
     * @return Document
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * Get preview
     *
     * @return string
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * Set isDraft
     *
     * @param  boolean  $isDraft
     * @return Document
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * Get isDraft
     *
     * @return boolean
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * Set category
     *
     * @param  Category $category
     * @return Document
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}
