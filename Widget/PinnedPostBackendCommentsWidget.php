<?php

namespace MesClics\PostBundle\Widget;

use MesClics\PostBundle\Entity\Post;
use MesClics\UtilsBundle\Widget\Widget;
use MesClics\CommentBundle\Entity\Comment;
use MesClics\PostBundle\PostRetriever\PostRetriever;

class PinnedPostBackendCommentsWidget extends Widget{
    protected $posts;
    
    public function __construct(PostRetriever $post_retriever){
        // retrieve all posts that concern the author
        $this->posts = $post_retriever->getPosts();
    }
    
    public function getName(){
        return 'pinned_post_backend_comments';
    }

    public function getTemplate(){
        return 'MesClicsPostBundle:Widgets:pinned-post-backend-comments.html.twig';
    }

    public function getVariables(){
        return array(
            'posts' => $this->posts
        );
    }

    public function getPosts(){
        return $this->posts;
    }

    public function getPinnedComments(Post $post){
        $results = array();
        foreach($post->getBackendComments()->toArray() as $comment){
            if($comment->isPinned()){
                $results[] = $comment;
            }

            $this->searchForPinnedChildren($comment, $results);
        }

        return $results;
    }

    protected function searchForPinnedChildren(Comment $comment, array &$results){
        foreach($comment->getChildren()->toArray() as $child){
            if($child->isPinned()){
                //check if parent is in results
                if(!in_array($child->getParent(), $results)){
                    $results[] = $child;
                }
            }

            $this->searchForPinnedChildren($child, $results);
        }
    }
}