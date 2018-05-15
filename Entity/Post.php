<?php

namespace MesClics\PostBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Post
 *
 * @ORM\Table(name="mesclics_post")
 * @ORM\Entity(repositoryClass="MesClics\PostBundle\Repository\PostRepository")
 */
class Post
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_publication", type="datetime", nullable=true)
     */
    private $datePublication;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_peremption", type="datetime", nullable=true)
     */
    private $datePeremption;

    /**
     * @var string
     *
     * @ORM\Column(name="visibilite", type="string", length=10)
     */
    private $visibilite;

    /**
     * @ORM\ManyToMany(targetEntity="MesClics\UserBundle\Entity\User", cascade={ "persist" })
     * @ORM\JoinTable(name="mesclics_post_user")
     */
    private $authors;

    /**
     * @ORM\ManyToMany(targetEntity = "MesClics\PostBundle\Entity\Collection", cascade={ "persist" })
     * @ORM\JoinTable(name="mesclics_post_collection")
     */
    private $collections;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Post
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content.
     *
     * @param string|null $content
     *
     * @return Post
     */
    public function setContent($content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dateCreation.
     *
     * @param \DateTime $dateCreation
     *
     * @return Post
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation.
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set datePublication.
     *
     * @param \DateTime|null $datePublication
     *
     * @return Post
     */
    public function setDatePublication($datePublication = null)
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    /**
     * Get datePublication.
     *
     * @return \DateTime|null
     */
    public function getDatePublication()
    {
        return $this->datePublication;
    }

    /**
     * Set datePeremption.
     *
     * @param \DateTime|null $datePeremption
     *
     * @return Post
     */
    public function setDatePeremption($datePeremption = null)
    {
        $this->datePeremption = $datePeremption;

        return $this;
    }

    /**
     * Get datePeremption.
     *
     * @return \DateTime|null
     */
    public function getDatePeremption()
    {
        return $this->datePeremption;
    }

    /**
     * Set visibilite.
     *
     * @param string $visibilite
     *
     * @return Post
     */
    public function setVisibilite($visibilite)
    {
        $this->visibilite = $visibilite;

        return $this;
    }

    /**
     * Get visibilite.
     *
     * @return string
     */
    public function getVisibilite()
    {
        return $this->visibilite;
    }

    /**
     * Add author.
     *
     * @param \MesClics\UserBundle\Entity\User $author
     *
     * @return Post
     */
    public function addAuthor(\MesClics\UserBundle\Entity\User $author)
    {
        $this->authors[] = $author;
        return $this;
    }

    /**
     * Remove author.
     *
     * @param \MesClics\UserBundle\Entity\User $author
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeAuthor(\MesClics\UserBundle\Entity\User $author)
    {
        return $this->authors->removeElement($author);
    }

    /**
     * Get authors.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Add collection.
     *
     * @param \MesClics\PostBundle\Entity\Collection $collection
     *
     * @return Post
     */
    public function addCollection(\MesClics\PostBundle\Entity\Collection $collection)
    {
        $collection->setEntity('post');
        $this->collections[] = $collection;
        return $this;
    }

    /**
     * Remove collection.
     *
     * @param \MesClics\PostBundle\Entity\Collection $collection
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCollection(\MesClics\PostBundle\Entity\Collection $collection)
    {
        return $this->collections->removeElement($collection);
    }

    /**
     * Get collections.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollections()
    {
        return $this->collections;
    }

    public function __construct(){
        $this->authors = new ArrayCollection();
        $this->collections = new ArrayCollection();
        foreach($this->getCollections() as $collection){
            $collection->setEntity('post');
        }
        //on rend automatique la date de création
        $this->setDateCreation(new \DateTime());        
    }

    public function isOnline(){
        $now = new \DateTime();
        if($this->datePublication && $this->datePublication <= $now && ($this->datePeremption > $now OR $this->datePeremption == null)){
            return true;
        } else{
            false;
        }
    }

    public function willBePublished(){
        $now = new \DateTime();
        if($this->datePublication && $this->datePublication > $now){
            return true;
        } else{
            return false;
        }
    }

    public function willBeUnpublished(){
        $now = new \DateTime();
        if($this->datePeremption && $this->datePeremption > $now){
            return true;
        } else{
            return false;
        }
    }

    public function hasBeenPublished(){
        $now = new \DateTime();
        if($this->datePublication && $this->datePublication < $now && $this->datePeremption < $now){
            return true;
        } else{
            return false;
        }
    }

    public function isDraft(){
        $now = new \DateTime();
        return (!$this->datePublication ? true : false);
    }
}
