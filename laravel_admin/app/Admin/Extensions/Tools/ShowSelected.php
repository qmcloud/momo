<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class ShowSelected extends BatchAction
{
    public function script()
    {
        return <<<EOT
        
$('{$this->getElementClass()}').on('click', function() {

    console.log(selectedRows());
    
    alert(selectedRows().join());
    
});

EOT;

    }
}
