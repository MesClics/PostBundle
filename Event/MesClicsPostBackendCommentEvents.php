<?php

namespace MesClics\PostBundle\Event;

final class MesClicsPostBackendCommentEvents{
    const CREATION = "mesclics_post_backend_comment_creation";
    const REMOVAL = "mesclics_post_backend_comment_removal";
    const UPDATE = "mesclics_post_backend_comment_update";
    const PIN = "mesclics_post_backend_comment_pin";
}