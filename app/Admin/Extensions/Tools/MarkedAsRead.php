<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class MarkedAsRead extends BatchAction
{
    public function script()
    {
        return <<<EOT
        
$('{$this->getElementClass()}').on('click', function() {

    console.log(selectedRows());
    
});

EOT;

    }
}
