<?php

namespace App\Repositories;

use App\Genre;
use App\Contracts\GenreRepositoryInterface;

class GenreRepository extends CrudRepository implements GenreRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = Genre::class;

    /**
     * @var \App\Genre $genre
     */
    protected $genre;

    public function __construct(Genre $genre)
    {
        $this->genre = $genre;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->genre;
    }
}
