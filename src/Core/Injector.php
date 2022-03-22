<?php

namespace App\Core;

trait Injector
{
    public function inject(string $className, ...$args){
        $classes = explode("\\", $className);
        $propName = lcfirst(end($classes));

        if($args) {
            $this->{$propName} = new $className(...$args);
        } else {
            $this->{$propName} = new $className();
        }
    }
}