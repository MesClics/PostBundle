<?php
namespace MesClics\PostBundle\Event;

class MesClicsPostEvents{
    public const CREATION = "mesclics_post.creation";
    public const UPDATE = "mesclics_post.update";
    public const REMOVAL = "mesclics_post.removal";
    public const PUBLICATION = "mesclics_post.publication";
    public const DEPUBLICATION = "mesclics_post.depublication";
    public const CATEGORIZATION = "mesclics_post.categorization";
}