<?php

namespace App\Repositories;

use App\Album;
use App\Contracts\AlbumRepositoryInterface;

class AlbumRepository extends CrudRepository implements AlbumRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = Album::class;

    /**
     * @var Album
     */
    protected $album;

    public function __construct(Album $album)
    {
        $this->album = $album;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->album;
    }
}
