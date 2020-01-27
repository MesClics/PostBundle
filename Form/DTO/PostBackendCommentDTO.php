<?php
namespace MesClics\PostBundle\Form\DTO;

use MesClicsBundle\Entity\MesClicsUser;
use MesClics\CommentBundle\Entity\Comment;
use MesClics\UtilsBundle\DataTransportObject\Mapper\MappingArrayItem;
use MesClics\UtilsBundle\DataTransportObject\DataTransportObjectToEntity;

class PostBackendCommentDTO extends DataTransportObjectToEntity{
    public $content;
    public $parent;
    public $author;

    public function __construct(MesClicsUser $author){
        // TODO: check if user has comments rights
        parent::__construct();
        $this->author = $author;
    }

    public function getMappingArray(){
        $contentMapping = new MappingArrayItem("content", array("getContent", "setContent"));
        $parentMapping = new MappingArrayItem("parent", array("getParent", "setParent"));
        $authorMapping = new MappingArrayItem("author", array("getAuthor", "setAuthor"));

        return array(
            $contentMapping,
            $parentMapping,
            $authorMapping
        );
    }

    public function setParent(Comment $parent){
        $this->parent = $parent;
    }

    public function getParent(){
        return $this->parent;
    }

    public function setContent(string $content){
        $this->content = $content;
    }

    public function getContent(){
        return $this->content;
    }

    public function setAuthor(MesClicsUser $author){
        $this->author = $author;
    }

    public function getAuthor(){
        return $this->author;
    }
}