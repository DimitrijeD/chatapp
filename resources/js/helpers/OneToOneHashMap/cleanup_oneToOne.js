/**
 * Receives object of properties and deletes prop which has no value 
 */

export function cleanup_oneToOne(hashCollection)
{
    for(let id in hashCollection){
        if(! Object.keys(hashCollection[id]).length){
            delete hashCollection[id];
        }
    }
}