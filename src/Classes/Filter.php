<?php
namespace App\Classes;

use App\Entity\Tag;
use App\Entity\Tog;

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
     * @var Tog[]
     */
    public $togs = [];

    /**
     * @var null|integer
     */
    public $max;

    /**
     * @var null|integer
     */
    public $min;

}