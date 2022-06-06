<div style="overflow-x: scroll; white-space: nowrap;">

<?php

class Strings
{
    const ONE_TAB   = " * \t";
    const TWO_TAB   = " * \t\t";
    const THREE_TAB = " * \t\t\t";
    const FOUR_TAB  = " * \t\t\t\t";
    const FIVE_TAB  = " * \t\t\t\t\t";
    const SIX_TAB   = " * \t\t\t\t\t\t";


    const START_DOC_COMMENT = '/**';
    const POST_DOC_START = ' * @OA\Post(';

    const PATH        = ' * path="/api/test1",';
    const SUMMARY     = ' * summary="",';
    const DESCRIPTION = ' * description="",';
    const TAGS        = ' * tags={""},';

    const FORM_REQUEST_START = ' * @OA\RequestBody(';
    const START_JSON_CONTENT = '@OA\JsonContent(';

    const BR = "<br>";
    const START_PROP = Strings::FOUR_TAB . '@OA\Property(';  
     
    
    const END_PROP = ' * ),';
    const END_DOC_COMMENT = ' */';

}

function startPostDoc(string $str)
{
    $str .= Strings::START_DOC_COMMENT . Strings::BR;
    $str .= Strings::POST_DOC_START . Strings::BR;

    $str .= Strings::PATH . Strings::BR;
    $str .= Strings::SUMMARY . Strings::BR;
    $str .= Strings::DESCRIPTION . Strings::BR;
    $str .= Strings::TAGS . Strings::BR;
    $str .= ' * ' . Strings::BR;

    return $str;
}

function makePropString($name, $value){
    $output = "";

    $output .= Strings::START_PROP . Strings::BR;

    $output .= Strings::FIVE_TAB . 'type="string",' . Strings::BR;

    $output .= Strings::FIVE_TAB . 'property="' . $name . '",' . Strings::BR;

    $output .= Strings::FIVE_TAB . 'example="' . htmlentities($value) . '",' . Strings::BR;

    $output .= Strings::FOUR_TAB . '),' . Strings::BR;

    return $output;
}

function makePropNull($name){
    $output = "";

    $output .= Strings::START_PROP . Strings::BR;

    $output .= Strings::FIVE_TAB . 'type="unknown",' . Strings::BR;

    $output .= Strings::FIVE_TAB . 'property="' . $name . '",' . Strings::BR;

    $output .= Strings::FIVE_TAB . 'example=null,' . Strings::BR;

    $output .= Strings::FOUR_TAB . '),' . Strings::BR;

    return $output;
}

function makePropInt($name, $value){
    $output = "";

    $output .= Strings::START_PROP . Strings::BR;

    $output .= Strings::FIVE_TAB . 'type="integer",' . Strings::BR;

    $output .= Strings::FIVE_TAB . 'property="' . $name . '",' . Strings::BR;

    $output .= Strings::FIVE_TAB . 'example=' . $value . ',' . Strings::BR;

    $output .= Strings::FOUR_TAB . '),' . Strings::BR;

    return $output;
}

function makePropIterables($name, $value)
{
    $output = "";

    $output .= Strings::START_PROP . Strings::BR;

    $output .= Strings::FIVE_TAB . 'type="object",' . Strings::BR;

    $output .= Strings::FIVE_TAB . 'property="' . $name . '",' . Strings::BR;

    $output .= Strings::FIVE_TAB . 'example=' . json_encode($value, JSON_UNESCAPED_UNICODE) . ',' . Strings::BR;

    $output .= Strings::FOUR_TAB . '),' . Strings::BR;

    return $output;
}

function makeParams($payload)
{
    $output = Strings::FORM_REQUEST_START . Strings::BR;
    $output .= Strings::ONE_TAB . 'required=true,' . Strings::BR;
    $output .= Strings::ONE_TAB . 'description="Form data.",' . Strings::BR;
    $output .= ' * ' . Strings::BR;
    $output .= Strings::ONE_TAB . Strings::START_JSON_CONTENT . Strings::BR;
    

    foreach($payload as $key => $value){
        $output .= Strings::TWO_TAB   . '@OA\Property(' . Strings::BR;

        switch(gettype($value)){
            case is_null($value):
                $output .= Strings::THREE_TAB . 'type="null",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'property="'. $key .'",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'example=null,' . Strings::BR;
                break;

            case is_float($value):
                $output .= Strings::THREE_TAB . 'type="float",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'property="'. $key .'",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'example=' . $value . ',' . Strings::BR;
                break;
                
            case 'array':
                $output .= Strings::THREE_TAB . 'type="object",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'property="'. $key .'",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'example=' . json_encode($value, JSON_UNESCAPED_UNICODE) . ',' . Strings::BR;
                break;
                
            case 'string':
                $output .= Strings::THREE_TAB . 'type="string",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'property="'. $key .'",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'example="' . $value . '",' . Strings::BR;
                break;

            case 'integer':
                $output .= Strings::THREE_TAB . 'type="integer",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'property="'. $key .'",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'example=' . $value . ',' . Strings::BR;
                break;

            case 'boolean':
                $output .= Strings::THREE_TAB . 'type="boolean",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'property="'. $key .'",' . Strings::BR;
                $output .= Strings::THREE_TAB . 'example=' . $value . ',' . Strings::BR;
                break;

            default:
                $GLOBALS['failed_params'][$key] = $value;
                break;

        }
        

        $output .= Strings::TWO_TAB . '),' . Strings::BR;
        $output .= " * " . Strings::BR;
    }

    // END JSON CONTENT
    $output .= Strings::ONE_TAB . '),' . Strings::BR;

    // END FORM BODY
    $output .=  ' * ),' . Strings::BR;
    $output .=  ' * ' . Strings::BR;

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
$GLOBALS['failed_params'] = [];

// if(!$response)
//     dd('no response object found');

$propsNotIncluded = [];

$output .= startPostDoc($output);

//make params
$output .= makeParams($payload);

// Make 1 success response -------------------------------------------------------
$output .= ' * @OA\Response(' . Strings::BR;
$output .= Strings::ONE_TAB   . 'response="200",' . Strings::BR;
$output .= Strings::ONE_TAB   . 'description="Successful response.",' . Strings::BR;
$output .= Strings::ONE_TAB   . 'content={' . Strings::BR;
$output .= Strings::TWO_TAB   . '@OA\MediaType(' . Strings::BR;
$output .= Strings::THREE_TAB . 'mediaType="application/json",' . Strings::BR;
$output .= Strings::THREE_TAB . '@OA\Schema(' . Strings::BR;

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
            $output .= makePropIterables($name, $value);
            break;

        default:
            // Properties which have unknown type will be listed here //
            $propsNotIncluded[$name] = $value;
            break;
    }
}

$output = makeSwaggerReadableArray($output);

$output .= Strings::THREE_TAB . '),' . Strings::BR;
$output .= Strings::TWO_TAB   . '),' . Strings::BR;
$output .= Strings::ONE_TAB   . '},' . Strings::BR;
$output .= ' * ),' . Strings::BR;


if($propsNotIncluded){
    echo '<div style="background-color: #ba0223; padding: 15px 5px; margin: 5px 0;">';
    echo "Following properties are not included in docs below because of unknown type format.";

    foreach($propsNotIncluded as $key => $value){
        echo $key . ' : ';

        if(is_countable($value)){
            print_r($value);
        }else{
            echo $value;
        }

    }
    echo '</div>';
} 

if($GLOBALS['failed_params']){
    echo '<div style="background-color: #f55673;  padding: 15px 5px; margin: 5px 0;">';
    echo 'Following parameters are not included in docs below because of unknown type format.<br>';

    foreach($GLOBALS['failed_params'] as $key => $value){
        echo $key . ' : ';

        if(is_countable($value)){
            print_r($value);
        }else{
            echo $value;
        }

        echo Strings::BR;
    }
    echo '</div>';
}

$output .= Strings::END_PROP . Strings::BR;
$output .= Strings::END_DOC_COMMENT . Strings::BR;

echo '<pre>';
echo '<div style="background-color: hsl(89, 83%, 81%);">' . htmlspecialchars_decode($output) . '</div>';
echo '</pre>';
?>
</div>

