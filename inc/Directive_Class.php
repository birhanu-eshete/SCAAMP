<?php
/**
 * This is a class for a generic directive
 *
 * @author birhanum
 */
class Directive {

    public $_directiveName;
    public $_possibleValues = array();
    public $_currentValue;
    public $_recommendedValue;
    public $_description;
    public $_remark;
    public $_safetyScore;

public function setDirectiveName ($name)
{
    $this->_directiveName=$name;
}

public function setPossibleValues($values)
{
$numElements= count($values);
for ($i=0;$i<$numElements;$i++)
    {
        $this->_possibleValues[$i]=$values[$i];
    }
}

public function setCurrentValue($cvalue)
{
    $this->_currentValue=$cvalue;
}

public function setRecommendedValue($rvalue)
{
    $this->_recommendedValue=$rvalue;

}

public function setDescription ($desc)
{
    $this->_description=$desc;
}

public function setRemark($rem)
{
    $this->_remark= $rem;
}

public function setSafetyScore($sscore)
{
    $this->_safetyScore=$sscore;
}

public function getDirectiveName()
{
    return $this->_directiveName;
}

public function getPossibleValues()
{
    return $this->_possibleValues;
}

public function getCurrentValue($name,$path)
{
   return  $this->_currentValue; 
}

public function getRecomendedValue()
{
    return $this->_recommendedValue;
}

public function getDescription()
{
    return $this->_description;
}

public function getRemark()
{
    return $this->_remark;
}


public function  computeSafetyScore($recomended, $current) {
    //unsafe by default unless checked
    $safetyScore =0;

    if (strcmp($recomended,$current)==0)
    {$safetyScore =1;}
    elseif(strcmp($recomended,"On")==0 && strcmp($current,"1")==0)
    {$safetyScore=1;}
    elseif(strcmp($recomended,"Off")==0 && strcmp($current,"0")==0)
    {   $safetyScore=1;}
    else
    {$safetyScore=0;}

        return $safetyScore;
    }

} //Directive_Class
?>
