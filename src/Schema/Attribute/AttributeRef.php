<?php
namespace Goetas\XML\XSDReader\Schema\Attribute;

use Goetas\XML\XSDReader\Schema\Item;

class AttributeRef extends Item implements AttributeSingle
{

    protected $qualified = true;

    protected $nil = false;

    protected $use = self::USE_OPTIONAL;

    /**
     *
     * @var Attribute
     */
    protected $wrapped;

    public function __construct(AttributeDef $att)
    {
        parent::__construct($att->getSchema(), $att->getName());
        $this->wrapped = $att;
    }

    public function isAnonymousType()
    {
        return $this->wrapped->isAnonymousType();
    }

    public function getType()
    {
        return $this->wrapped->getType();
    }

    public function isQualified()
    {
        return $this->qualified;
    }

    public function setQualified($qualified)
    {
        $this->qualified = $qualified;
        return $this;
    }

    public function isNil()
    {
        return $this->nil;
    }

    public function setNil($nil)
    {
        $this->nil = $nil;
        return $this;
    }

    public function getUse()
    {
        return $this->use;
    }

    public function setUse($use)
    {
        $this->use = $use;
        return $this;
    }
}
