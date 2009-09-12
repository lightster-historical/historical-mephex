<?php


class MXT_TableIndex
{
    const TYPE_PRIMARY = 1;
    const TYPE_UNIQUE = 2;
    const TYPE_INDEX = 3;


    protected $name;
    protected $fields;
    protected $type;

    protected $permutatedFields;


    public function __construct($keyname, $type)
    {
        $this->keyname = $keyname;
        $this->fields = array();
        $this->type = $type;

        $this->permutatedFields = null;
    }


    public function addField($fieldName)
    {
        $this->fields[] = $fieldName;
    }


    public function getKeyname()
    {
        return $this->keyname;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getType()
    {
        return $this->type;
    }

    public function isUnique()
    {
        return $this->unique;
    }


    protected function initPermutatedFields(array $fields)
    {
        if(count($fields) > 0)
        {
            $permutations = array();
            foreach($fields as $field)
            {
                $rest = $fields;
                $key = array_search($field, $rest);
                unset($rest[$key]);

                $permutations[$field] = $this->initPermutatedFields($rest);
            }

            return $permutations;
        }
        else
        {
            return array('.' => $this);
        }
    }

    public function getPermutatedFields()
    {
        if(is_null($this->permutatedFields))
            $this->permutatedFields = $this->initPermutatedFields($this->getFields());

        return $this->permutatedFields;
    }
}



?>
