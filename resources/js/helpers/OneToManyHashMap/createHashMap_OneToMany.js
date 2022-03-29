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
export function createHashMap_OneToMany(listedCollection, firstLevel, secondLevel)
{
    let hashCollection = {};

    for (let index in listedCollection){
        if (listedCollection.hasOwnProperty(index) && listedCollection[index]?.[firstLevel]) {
            const model = listedCollection[index];

            // If for example ID already exists in obj, then value must be array of models
            if(!hashCollection.hasOwnProperty(model[firstLevel])){
                hashCollection[model[firstLevel]] = {};
            } 
            hashCollection[model[firstLevel]] [model[secondLevel]] = model;
        }
    }

    return hashCollection;
}