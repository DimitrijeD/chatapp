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

export function getGroupIndexById(groups, id)
{
    for(let grI in groups){
        if(groups[grI].id == id){
            return grI
        }
    }

    return undefined
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

export function getParticipantIndexInGroupByGroupId(participants, user_id)
{
    for(let prI in participants){
        if(participants[prI].id == user_id){
            return prI
        }
    }

    return undefined
}

export function getNumOfUnseenGroups(groups)
{
    let num = 0
    
    if(!groups) return num

    for(let grI in groups){
        if(groups[grI].hasUnseenState) num++
    }

    return num
}

export function getGroupsFrom_openedGroupsIds(groups, openedGroupsIds)
{
    if(!groups) return []
    var openedGroups = []

    for(let i in openedGroupsIds){
        for(let j in groups){
            if(groups[j].id == openedGroupsIds[i]){
                openedGroups.push( groups[j] )
                break
            }
        }
    }

    return openedGroups
}

export function getGroupsFrom_filteredGroupsIds(groups, filteredGroupsIds)
{
    if(!groups) return []
    var filteredGroups = []

    for(let i in filteredGroupsIds){
        for(let j in groups){
            if(groups[j].id == filteredGroupsIds[i]){
                filteredGroups.push( groups[j] )
            }
        }
    }

    return filteredGroups
}

export function getGroupIndexByIdIn_openedGroupsIds(openedGroupsIds, id)
{
    return openedGroupsIds.findIndex((group_id, index) => {
        if(group_id == id) return true;
    })
}

export function nowISO()
{
    return (new Date(Date.now())).toISOString();  
}

