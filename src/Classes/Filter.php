<?php
namespace App\Classes;

use App\Entity\Tag;

class Filter{

    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var Tag[]
     */
    public $tags = [];

    /**
     * @var null|integer
     */
    public $max;

    /**
     * @var null|integer
     */
    public $min;

}