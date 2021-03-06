<?php
namespace MesClics\PostBundle\PostRetriever;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PostRetriever{
    private $em;
    private $token_storage;
    private $repository;
    private $order_params;
    private $order_by;
    private $order;
    private $limit;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $token_storage, $adminListItemsNumber){
        $this->em = $em;
        $this->token_storage = $token_storage;
        $this->repository = $this->em->getRepository('MesClicsPostBundle:Post');
        $this->limit = $adminListItemsNumber; //get the %admin.list_items_number% parameter (bound in the service config file).
        $this->order_by = 'date-creation'; //par défaut on trie les posts par date de création.
        if(preg_match('/^date-/m', $this->order_by)){//par défaut le critère de tri @order est ascendant sauf lorsque le critère de tri commence par date_
            $this->order = 'DESC';
        } else{
            $this->order = 'ASC'; 
        }
    }

    public function addOrderParams(Array $array){
        $this->order_params = $array;
        return $this;
    }

    public function getOrderBy(){
        return $this->order_by;
    }

    public function setOrderBy($order_by){
        if(!$this->order_params[$order_by]){
            throw new InvalidArgumentException('critère de tri @order_by (' . $order_by . ') non reconnu. Le critère doit être l\'un des suivants : ' . implode(', ', array_keys($this->order_params)));
        }

        // $this->order_by = $this->order_params[$order_by];
        $this->order_by = $order_by;
        return $this;
    }

    public function getOrder(){
        return $this->order;
    }

    public function setOrder($order){
        if(!$order == 'ASC' || !$order == 'DESC'){
            throw new InvalidArgumentException("le critère de tri @order (" . $order . ") ne peut être que l'un des deux suivants : ASC ou DESC");
        }
        $this->order = $order;
        return $this;
    }

    public function setLimit(int $limit){
        $this->limit = $limit;
        return $this;
    }

    public function getLimit(){
        return $this->limit;
    }

    public function getPosts(){
        return $this->repository->getPosts($this->token_storage->getToken()->getUser(), $this->order_params[$this->order_by], $this->limit, $this->order);
    }
}