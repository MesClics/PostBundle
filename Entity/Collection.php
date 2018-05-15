<?php

namespace MesClics\PostBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Collection
 *
 * @ORM\Table(name="mesclics_collection")
 * @ORM\Entity(repositoryClass="MesClics\PostBundle\Repository\CollectionRepository")
 */
class Collection
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(name="entity", type="string", length=255)
     */
    private $entity;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;


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
     * Set name.
     *
     * @param string $name
     *
     * @return Collection
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set entity.
     *
     * @param string $entity
     *
     * @return Collection
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity.
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Collection
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getFormLabel(){
        return $this->name . ' (' . $this->description . ')';
    }

    public function __construct($entity = null){
        if($entity){
            $this->entity = $entity;
        }
    }
}
