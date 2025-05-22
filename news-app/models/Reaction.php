<?php
class Reaction {
    private $id;
    private $user_id;
    private $article_id;
    private $comment_id;
    private $type;
    private $reaction_date;

    public function __construct($user_id, $article_id, $comment_id, $type) {
        $this->user_id = $user_id;
        $this->article_id = $article_id;
        $this->comment_id = $comment_id;
        $this->type = $type;
        $this->reaction_date = date('Y-m-d H:i:s');
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getArticleId() {
        return $this->article_id;
    }

    public function getCommentId() {
        return $this->comment_id;
    }

    public function getType() {
        return $this->type;
    }

    public function getReactionDate() {
        return $this->reaction_date;
    }
}