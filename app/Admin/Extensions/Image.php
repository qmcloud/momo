<?php
namespace App\Admin\Extensions;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Encore\Admin\Form\Field\ImageField;
use Encore\Admin\Form\Field\File;
class Image extends File
{
    use ImageField;
    /**
     * {@inheritdoc}
     */
    protected $view = 'admin::form.file';
    /**
     *  Validation rules.
     *
     * @var string
     */
    protected $rules = 'image';
    /**
     * @param array|UploadedFile $image
     *
     * @return string
     */
    public function prepare($image)
    {
        if (request()->has(static::FILE_DELETE_FLAG)) {
            return $this->destroy();
        }
        $size = getimagesize($image);
        $a = $size[0] . "_" . $size[1] . "_";
        $this->name = $a . $this->getStoreName($image);
        $this->callInterventionMethods($image->getRealPath());
        return $this->uploadAndDeleteOriginal($image);
    }
}
