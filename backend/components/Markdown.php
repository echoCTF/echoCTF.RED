<?php

namespace app\components;

use cebe\markdown\GithubMarkdown;

class Markdown extends GithubMarkdown
{

    /**
     * Add bootstrap classes to tables.
     * @inheritdoc
     */
    public function renderTable($block)
    {
        return str_replace('<table>', '<table class="table table-bordered table-striped">', parent::renderTable($block));
    }
}