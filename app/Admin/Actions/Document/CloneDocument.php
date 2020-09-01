<?php

namespace App\Admin\Actions\Document;

use Encore\Admin\Actions\RowAction;
use App\Models\Document;

class CloneDocument extends RowAction
{
    public $name = '复制';

    public function handle(Document $document)
    {
        $document->replicate()->save();
        
        return $this->response()->success('复制成功')->refresh();
    }

    public function dialog()
    {
        $this->confirm('确认复制?');
    }
}