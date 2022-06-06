<div style="overflow-x: scroll; white-space: nowrap;">
<?php

class Strings{
    const BR = "<br>";
    const START_PROP = '* @OA\Property(';  
    const END_PROP = '* ),';
}

class ResponseObject{
    public $o;

    public function set($response){
        $this->o = $response;
    }

    public function getAll(){
        return $this->o;
    }

    public function getValueByKey($key){
        if(gettype($this->o) == "object")
            return $this->o->{$key};

        if(gettype($this->o) == "array"){
            return $this->o[$key];
        }
    }
}

function makePropString($name, $value){
    $output = "";

    $output .= Strings::BR . Strings::START_PROP . Strings::BR;

    // type
    $output .= " * " . ' type="string",' . Strings::BR;

    // property
    $output .= " * " . ' property="' . $name . '",' . Strings::BR;

    // example
    $output .= " * " . ' example="' . htmlentities($value) . '",';

    $output .= Strings::BR . Strings::END_PROP;

    return $output;
}

function makePropNull($name){
    $output = "";

    $output .= Strings::BR . Strings::START_PROP . Strings::BR;

    // type
    $output .= ' * type="unknown",' . Strings::BR;

    // property
    $output .= ' * property="' . $name . '",' . Strings::BR;

    // example
    $output .= ' * example=null,';

    $output .= Strings::BR . Strings::END_PROP;

    return $output;
}

function makePropInt($name, $value){
    $output = "";

    $output .= Strings::BR . Strings::START_PROP . Strings::BR;

    // type
    $output .= ' * type="integer",' . Strings::BR;

    // property
    $output .= ' * property="' . $name . '",' . Strings::BR;

    // example
    $output .= ' * example=' . $value . ',';

    $output .= Strings::BR . Strings::END_PROP;

    return $output;
}

function makePropIterables($name, $value, $asObj)
{

    //JSON_UNESCAPED_SLASHES
    $value = json_encode($asObj->getValueByKey($name), JSON_UNESCAPED_UNICODE);

    $output = "";

    $output .= Strings::BR . Strings::START_PROP . Strings::BR;

    // type
    $output .= ' * type="object",' . Strings::BR;

    // property
    $output .= ' * property="' . $name . '",' . Strings::BR;

    // example
    $output .= ' * example=' . $value . ',';

    $output .= Strings::BR . Strings::END_PROP;

    return $output;
}

/**
 * Swagger cannot accept array as regular square brackets [ ] 
 * 
 * eg JSON:
 * [{ id: 1,name: 'Asd' }]
 * 
 * But must be in format:
 * {{ id: 1,name: 'Asd' }}
 * 
 * this method seaches for this case in JSON 
 * 
 */ 

function makeSwaggerReadableArray($value)
{
    $value = str_replace('[', '{', $value);
    $value = str_replace(']', '}', $value);
    return $value;
}

//  ----------------------  RUN -------------------------------//
$output = "";


$asObj = new ResponseObject;
$asObj->set($response);

$propsNotIncluded = [];

foreach($response as $name => $value){
    switch(gettype($value)){
        case "string":
            $output .= makePropString($name, $value);
            break;

        case "integer":
            $output .= makePropInt($name, $value);
            break;

        case is_null($value):
            $output .= makePropNull($name, $value);
            break;

        case is_bool($value):
            $output .= makePropNull($name, $value);
            break;

        case "array":
            $output .= makePropIterables($name, $value, $asObj);
            break;

        default:
            // Properties which have unknown type will be listed here //
            $propsNotIncluded[$name] = $value;
            break;
    }
}

$output = makeSwaggerReadableArray($output);

if($propsNotIncluded){
    echo "<p>Following properties are not included in docs below because of unknown type format.</p>";

    foreach($propsNotIncluded as $key => $value){
        echo $key . ' : ';

        if(is_countable($value)){
            print_r($value);
        }else{
            echo $value;
        }

        echo "<br>";
    }
} 

// TODO \" remove this fucking shit from all instances in string. .fUCK

echo '<div style="background-color: hsl(89, 83%, 81%);">' . htmlspecialchars_decode($output) . '</div>';

echo "<br><br><br><br><br>";



?>
</div>
