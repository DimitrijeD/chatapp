import * as helpers from "../../../helpers/helpers_exporter.js"

export function attachUnseenStateBool(groups, user_id)
{
    for(let grI in groups){
        const participants = groups[grI].participants
        
        for(let prI in participants){
            if(participants[prI].id == user_id){
                const pivot = participants[prI].pivot
                groups[grI].hasUnseenState =  pivot.last_message_seen_id < groups[grI].last_msg_id ? true : false
                break // this participants pivot is found and his unseen state is set, break since no more action is needed 
            }
        }
    }

    return groups
}

export function propInit(groups)
{
    for(let grI in groups){
        groups[grI].lastAcknowledgedMessageId = null
        groups[grI].hasUnseenState
        groups[grI].messages = {} 
        groups[grI].eariestMessageId = null
        groups[grI].reachedEarliestMsgId = false
    }

    return groups
}

export function prepareManyGroups(groups, mapIdentifier, user_id)
{
    groups = helpers.createHashMap_OneToOne(groups, mapIdentifier)
    groups = attachUnseenStateBool(groups, user_id)
    return propInit(groups)
}

export function prepareOneGroup(groups, mapIdentifier, user_id)
{
    groups = helpers.createHashMap_OneToOne(groups, mapIdentifier)
    groups = propInit(groups)
    groups = attachUnseenStateBool(groups, user_id)
    return groups[Object.keys(groups)[0]]
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

