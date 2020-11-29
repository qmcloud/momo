<?php

namespace App\Models\Movie;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class InTheater extends Model
{
    public function paginate()
    {
        $perPage = Request::get('per_page', 10);

        $page = Request::get('page', 1);

        $start = ($page-1)*$perPage;

        $data = file_get_contents("https://api.douban.com/v2/movie/in_theaters?city=上海&start=$start&count=$perPage");

        $data = json_decode($data, true);

        extract($data);

        $movies = static::hydrate($subjects);

        $paginator = new LengthAwarePaginator($movies, $total, $perPage);

        $paginator->setPath(url()->current());

        return $paginator;
    }

    public static function with($relations)
    {
        return new static;
    }

    public function findOrFail($id)
    {
        $data = file_get_contents("http://api.douban.com/v2/movie/subject/$id");

        $data = json_decode($data, true);

        return static::newFromBuilder($data);
    }

    public function save(array $options = [])
    {
        dd($this->getAttributes());
    }
}