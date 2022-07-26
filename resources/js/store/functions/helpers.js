export function getById(collection, id) 
{
    if(!collection) return null

    for(let index in collection){
        if(collection[index].id == id) return collection[index]
    }

    return null
}

export function getBySearchString(groups, str)
{   
    let groupsIdsMatchSearch = []
    if(!groups) return []
    
    if(!str){
        for (let grI in groups){
            groupsIdsMatchSearch.push( groups[grI].id )
        }
        return groupsIdsMatchSearch
    }

    for (let grI in groups){
        let group = groups[grI]

        for (let usI in groups[grI].participants){
            let participant = group.participants[usI]

            // search criteria
            const text = [
                participant.first_name, 
                participant.last_name, 
                participant.email, 
                group.name
            ].join(' ') 

            if(regExpressionMatch(str, text)){
                groupsIdsMatchSearch.push( groups[grI].id )
                // do not push same group multiple times, break
                break
            }
        }
    }
    return groupsIdsMatchSearch
}

export function regExpressionMatch(find, text)
{
    let regex = new RegExp(find, 'i');
    return text.match(regex)
}

export function getLastOwnedMsgId(messages)
{
    if(Object.prototype.toString.call(messages).indexOf("Object")>-1){
        let last_id = 0
        for(let id in messages){
            if(parseInt(id) > last_id) last_id = parseInt(id)
        }

        return last_id
    }
}

export function getModelsFromIds(collection, arrIds)
{
    if(!collection) return []
    var filteredModels = []

    for(let i in arrIds){
        for(let j in collection){
            if(collection[j].id == arrIds[i]) filteredModels.push( collection[j] )
        }
    }

    return filteredModels
}

export function nowISO()
{
    return (new Date(Date.now())).toISOString();  
}

export function getMinObjKey(x)
{
    x = Object.keys(x)
    return Math.min(...x.filter(x => typeof x === 'string'))
}

export function getAllIds(collection)
{
    var ids = []

    for(let i in collection){
        ids.push(collection[i].id)
    }

    return ids
}

export function createDict(collection, propertyName)
{
    let dict = {};

    for (let i in collection){
        if (collection.hasOwnProperty(i) && collection[i]?.[propertyName]) {
            const model = collection[i];
            dict[model[propertyName]] = model;
        }
    }

    return dict;
}
  
export function prepareParticipantsForStoreRequest(data)
{
    let request = {
        group_id: data.group_id,
        usersToAdd: []
    }

    for(let i in data.addedUsersIds){
        request.usersToAdd.push({
            user_id: data.addedUsersIds[i],
            target_role: data.massAssignRolesTo
        })
    }

    return request
}

export function evalSeenState(self, last_msg)
{
    // if group doesnt have a single message
    if(!last_msg) return true

    // if self is owner of last message
    if(self.id == last_msg.user_id) return true

    // has user already acknowledged last message 
    return self.pivot.last_message_seen_id == last_msg.id 
}

export function sortNewest(collection)
{
    return collection.sort(function(a,b){
        // Turn your strings into dates, and then subtract them
        // to get a value that is either negative, positive, or zero.
        return new Date(b.updated_at) - new Date(a.updated_at);
    })
    // .reverse();
}

export function getByStr(users, str)
{
    let usersIdsMatchSearch = []

    for(let i in users){
        let user = users[i] 

        const text = [
            user.first_name, 
            user.last_name, 
            user.email, 
        ].join(' ') 

        if( text.match(new RegExp(str, 'i')) ) usersIdsMatchSearch.push( user.id )
    }

    return usersIdsMatchSearch
}

/**
 * From array of @param ids, return array which doesnt contain @param excludeIds
 * 
 * @param array ids 
 * @param array excludeIds 
 * @returns 
 */
export function excludeByIds(ids, excludeIds)
{
    return ids.filter((el) => !excludeIds.includes(el))
}

export function prepareUsersSearchRequest(data, possesedIds)
{
    data.i_have_ids = possesedIds

    if('exclude' in data && data.exclude.length){
        data.i_have_ids = possesedIds.concat(data.exclude.filter((item) => possesedIds.indexOf(item) < 0)); // merge Ids without duplicates
    }

    return data
}