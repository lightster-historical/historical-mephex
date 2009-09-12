<?php



class MXT_Ranker
{
    protected $currRank;
    protected $currValue;
    protected $skip;



    public function __construct()
    {
        $this->currRank = 0;
        $this->currValue = null;
        $this->skip = 0;
    }


    public function getRank($value)
    {
        if(!is_null($this->currValue) && $value == $this->currValue)
            $this->skip++;
        else
        {
            $this->rank += $this->skip + 1;
            $this->currValue = $value;
            $this->skip = 0;
        }

        return $this->rank;
    }
}



?>
