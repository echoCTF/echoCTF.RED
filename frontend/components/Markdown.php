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

    /**
     * Mark code blocks without a language as plaintext so highlight.js does not guess one.
     * @inheritdoc
     */
    public function renderCode($block)
    {
        if (empty($block['language'])) {
            $block['language'] = 'plaintext';
        }
        return parent::renderCode($block);
    }
}