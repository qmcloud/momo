<?php

namespace App\Admin\Extensions\Form;

use Encore\Admin\Form\Field;

class MarkdownEditor extends Field
{
    protected $view = 'admin::form.editor';

    protected static $css = [
        '/packages/bootstrap-markdown-editor/dist/css/bootstrap-markdown-editor.css',
    ];

    protected static $js = [
        '//cdnjs.cloudflare.com/ajax/libs/ace/1.1.3/ace.js',
        '//cdnjs.cloudflare.com/ajax/libs/marked/0.3.2/marked.min.js',
        '/packages/bootstrap-markdown-editor/dist/js/bootstrap-markdown-editor.js',
    ];

    public function render()
    {
        $this->script = <<<EOT

$('#$this->id').markdownEditor({
    preview: true,
    onPreview: function (content, callback) {
        callback( marked(content) );
    }
});

EOT;

        return parent::render();
    }
}
