<?php
 declare(strict_types=1); namespace Tygh\Addons\SdLabels; class Collection implements \Iterator { protected $collection; public function current() { return current($this->collection); } public function key() { return key($this->collection); } public function next() { next($this->collection); } public function rewind() { reset($this->collection); } public function valid(): bool { return null !== key($this->collection); } } 