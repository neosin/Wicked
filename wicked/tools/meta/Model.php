<?php

namespace wicked\tools\meta;

use wicked\tools\meta\Annotation;

trait Model
{

    protected $__rel = [];

    /**
     * Find relation
     * @param $entity
     * @param array $where
     * @return bool|\PDOStatement
     * @throws \InvalidArgumentException
     */
    public function __call($entity, $where = [])
    {
        // lazy mapping
        if(!$this->__rel) {

            // get annotations
            $annotations = Annotation::object($this);

            // parse
            foreach($annotations as $key => $annotation) {
                if($key == 'has') {
                    @list($type, $name, $link) = explode(' ', $annotation);
                    $link = $link ? trim($link, '()') : strtolower($name);
                    $this->__rel[strtolower($name)] = (object)['type' => $type, 'link' => $link];
                }
            }

        }

        // clean
        $entity = strtolower($entity);

        // find relation
        if(isset($this->__rel[$entity]) and $rel = $this->__rel[$entity]) {

            // many
            if($rel->type == 'many') {
                return syn()->{$entity}->find([$rel->link => $this->id]);
            }
            // one
            elseif($rel->type == 'one') {
                return syn()->{$entity}->find($rel->link);
            }

        }
        else
            throw new \InvalidArgumentException('Method ' . $entity . ' does not exists in ' . get_called_class());
    }

}