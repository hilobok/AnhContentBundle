<?php

namespace Anh\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Anh\MarkupBundle\Mapping\Annotation\Parsable as ParseMarkup;
use Anh\MarkupBundle\Mapping\Annotation\Countable as CountMarkup;
use Symfony\Component\Validator\Constraints as Assert;
use Anh\ContentBundle\Validator\Constraints as PaperAssert;
use Anh\MarkupBundle\Validator\Constraints as MarkupAssert;
use Anh\Taggable\AbstractTaggable;
use Anh\Taggable\TaggableInterface;

/**
 * Paper
 *
 * @ORM\Table(indexes={
 *      @ORM\Index(name="idx_title", columns={ "title" }),
 *      @ORM\Index(name="idx_section", columns={ "section" }),
 *      @ORM\Index(name="idx_slug", columns={ "slug" }),
 *      @ORM\Index(name="idx_createdAt", columns={ "createdAt" }),
 *      @ORM\Index(name="idx_updatedAt", columns={ "updatedAt" }),
 *      @ORM\Index(name="idx_publishedSince", columns={ "publishedSince" }),
 *      @ORM\Index(name="idx_isDraft", columns={ "isDraft" }),
 *      @ORM\Index(name="idx_image", columns={ "image" }),
 *      @ORM\Index(name="idx_externalLinksCount", columns={ "externalLinksCount" })
 * })
 * @ORM\Entity(repositoryClass="Anh\ContentBundle\Entity\PaperRepository")
 */
class Paper extends AbstractTaggable implements TaggableInterface
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
     * @Assert\NotBlank
     * @PaperAssert\SectionIsConfigured
     */
    protected $section;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="papers", fetch="EAGER")
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
     * @Assert\NotBlank
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
     * @MarkupAssert\MarkupIsValid(type="bbcode")
     */
    protected $markup;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     * @ParseMarkup(type="bbcode", field="markup")
     */
    protected $content;

    /**
     * @var string
     *
     * @ORM\Column(name="preview", type="text", nullable=true)
     * @ParseMarkup(type="bbcode", field="markup", options={"previewOnly"=true})
     */
    protected $preview;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isDraft", type="boolean")
     */
    protected $isDraft = false;

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

    /**
     * @var string
     *
     * @ORM\Column(name="metaAuthor", type="string", nullable=true)
     */
    protected $metaAuthor;

    /**
     * @var string
     *
     * @ORM\Column(name="metaDescription", type="string", nullable=true)
     */
    protected $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="metaKeywords", type="string", nullable=true)
     */
    protected $metaKeywords;

    /**
     * @var integer
     *
     * @ORM\Column(name="externalLinksCount", type="integer")
     */
    protected $externalLinksCount = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="charsCount", type="integer")
     * @CountMarkup(type="bbcode", field="markup")
     */
    protected $charsCount = 0;

    public function afterMarkupParsed()
    {
        $this->countExternalLinks();
    }

    protected function countExternalLinks()
    {
        $this->externalLinksCount = preg_match_all(
            '/<a\s+.*?href="https?\:\/\/.+?".*?>/i',
            $this->content
        );
    }

    public function getCharsCount()
    {
        return $this->charsCount;
    }

    public function setCharsCount($count)
    {
        $this->charsCount = $count;
    }

    public function getExternalLinksCount()
    {
        return $this->externalLinksCount;
    }

    public function setExternalLinksCount($count)
    {
        $this->externalLinksCount = $count;
    }

    /**
     * Get metaAuthor
     *
     * @return string
     */
    public function getMetaAuthor()
    {
        return $this->metaAuthor;
    }

    /**
     * Set metaAuthor
     *
     * @param  string $metaAuthor
     * @return Paper
     */
    public function setMetaAuthor($metaAuthor)
    {
        $this->metaAuthor = $metaAuthor;

        return $this;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set metaDescription
     *
     * @param  string $metaDescription
     * @return Paper
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set metaKeywords
     *
     * @param  string $metaKeywords
     * @return Paper
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param  string $image
     * @return Paper
     */
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
     * @param  array $assets
     * @return Paper
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
    public function getMarkupData()
    {
        return $this->getUrlParameters();
    }

    /**
     * Returns parameters for url generation
     *
     * @return array
     */
    public function getUrlParameters()
    {
        return array(
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
     * @param  string $section
     * @return Paper
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
     * @return Paper
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
     * @return Paper
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
     * @return Paper
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
     * @param  string $title
     * @return Paper
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
     * @param  string $markup
     * @return Paper
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
     * @param  string $content
     * @return Paper
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
     * @param  string $preview
     * @return Paper
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
     * @param  boolean $isDraft
     * @return Paper
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
     * @return Paper
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

    /**
     * {@inheritdoc}
     */
    public function getTaggableType()
    {
        return $this->section;
    }
}
