export function getById(groups, id) 
{
    if(!groups) return null

    for(let index in groups){
        if(groups[index].id == id){
            return groups[index]
        }
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

function regExpressionMatch(find, text)
{
    let regex = new RegExp(find, 'i');
    return text.match(regex)
}

// expects hash map of messages: messages{ ... msgId: msgObject ... }
export function getLastOwnedMsgId(messages)
{
    if(Object.prototype.toString.call(messages).indexOf("Object")>-1){
        let last_id = 0
        for(let id in messages){
            if(parseInt(id) > last_id) last_id = parseInt(id)
        }

        return last_id
    }

    if(Object.prototype.toString.call(messages).indexOf("Array")>-1){
        if(!messages.length) return 0
        let last_id = 0
        for(let i in messages){
            if(messages[i].id > last_id) last_id = messages[i].id 
        }
        
        return last_id
    }
}

export function getNumOfUnseenGroups(groups)
{
    let num = 0
    
    if(!groups) return num

    for(let grI in groups){
        if(groups[grI].seenState) num++
    }

    return num
}

export function getModelsFromIds(collection, arrIds)
{
    if(!collection) return []
    var filteredModels = []

    for(let i in arrIds){
        for(let j in collection){
            if(collection[j].id == arrIds[i]){
                filteredModels.push( collection[j] )
            }
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

    collection.forEach(model => {
        ids.push(model.id)
    })

    return ids
}
  