<?php
namespace MC\PostBundle\Twig;

use MC\PostBundle\PostCounter\PostCounter;

class PostCounterExtension extends \Twig_Extension{
    private $post_counter;

    public function __construct(PostCounter $post_counter){
        $this->post_counter = $post_counter;
    }

    public function getPostsCount($counter_type = false){
        return $this->post_counter->count($counter_type);
    }

    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('countPosts', array($this, 'getPostsCount'))
        );
    }

    public function getName(){
        return 'MCPostCounter';
    }
}