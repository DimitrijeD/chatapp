/**
 * Example for 'group_participants' response.
 * 
 * --- Params ---
 * listedCollection = [
 *      { chat_group_id: 1, last_message_seen_id: 108,  user_id: 2 ...}
 *      { chat_group_id: 1, last_message_seen_id: 108,  user_id: 3 ...}
 *      { chat_group_id: 1, last_message_seen_id: null, user_id: 1 ...} // note that null value wont be set as object key! User seen no messages in this chat.
 * ]
 * 
 * firstLevel = 'last_message_seen_id'; 
 * secondLevel =  'user_id';
 * 
 * --- Return ---
 * hashCollection = {
 *      108: {
 *          2: { chat_group_id: 1, last_message_seen_id: 108,  user_id: 2 ...}
 *          3: { chat_group_id: 1, last_message_seen_id: 108,  user_id: 3 ...}
 *      }
 * }
 *  
 * hashCollection.108  ->  is object of all ('group_participants' table row)s which saw 108 message last 
 * hashCollection.108.2  ->  is object of single ('group_participants' table row)
 * 
 */
 export function updateHashMap_OneToMany(hashMap, newData, firstLevel, secondLevel, uniqueIdentifier)
 {
    // remove instance of secondLevel data ex: user exists somewhere in this map, find it and delete it:
    for(let temp_firstLevel in hashMap){
        for(let temp_secondLevel in hashMap[temp_firstLevel]){
            if(temp_secondLevel == newData[uniqueIdentifier]){
                delete hashMap[temp_firstLevel][temp_secondLevel];
                break; 
            }
        }
    }
    
    // set newData
    if(hashMap?.[firstLevel]){
        hashMap[firstLevel][newData[uniqueIdentifier]] = newData;
    } else {
        hashMap[firstLevel] = {};
        hashMap[firstLevel][newData[uniqueIdentifier]] = newData;
    }

    // delete property with empty value
    for(let temp_firstLevel in hashMap){
        if(hashMap[temp_firstLevel]){
            console.log('evaluated as  true', hashMap[temp_firstLevel]);
        } else {
            console.log('evaluated as  false', hashMap[temp_firstLevel]);
        }
        // console.log(hashMap[temp_firstLevel]);
        // if(!hashMap[temp_firstLevel].length){
        //     delete hashMap[temp_firstLevel];
        // }
    }
    // console.log(hashMap);
    return hashMap;
 }