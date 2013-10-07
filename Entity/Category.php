<?php

namespace Anh\Bundle\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use Anh\Bundle\ContentBundle\Entity\Document;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Category
 *
 * @ORM\Table(indexes={
 *      @ORM\Index(name="idx_title", columns={ "title" }),
 *      @ORM\Index(name="idx_slug", columns={ "slug" }),
 *      @ORM\Index(name="idx_createdAt", columns={ "createdAt" }),
 *      @ORM\Index(name="idx_updatedAt", columns={ "updatedAt" })
 * })
 * @ORM\Entity(repositoryClass="Anh\Bundle\ContentBundle\Entity\CategoryRepository")
 */
class Category
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="Document", mappedBy="category", cascade={"remove"})
     */
    protected $documents;

    /**
     * Constructor
     */
    // public function __construct()
    // {
    //     $this->documents = new ArrayCollection();
    // }

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
     * Set title
     *
     * @param  string   $title
     * @return Category
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
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Category
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
     * @return Category
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

    // /**
    //  * Add documents
    //  *
    //  * @param  Document $documents
    //  * @return Category
    //  */
    // public function addDocument(Document $documents)
    // {
    //     $this->documents[] = $documents;

    //     return $this;
    // }

    // /**
    //  * Remove documents
    //  *
    //  * @param Document $documents
    //  */
    // public function removeDocument(Document $documents)
    // {
    //     $this->documents->removeElement($documents);
    // }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDocuments()
    {
        return $this->documents = $this->documents ?: new ArrayCollection();
    }
}
