<?php

namespace Concise\Matcher;

class HasPropertyWithExactValue extends HasProperty
{
    public function supportedSyntaxes()
    {
        return array(
            '?:object has property ?:string with exact value ?' => 'Assert that an object has a property with a specific exact value.',
        );
    }

    public function match($syntax, array $data = array())
    {
        return parent::match(null, $data) && ($data[0]->{$data[1]} === $data[2]);
    }

    public function getTags()
    {
        return array(Tag::OBJECTS);
    }
}
