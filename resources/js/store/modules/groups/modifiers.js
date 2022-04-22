import * as helpers from "../../../helpers/helpers_exporter.js"

export function attachUnseenStateBool(groups, user_id)
{
    for(let grI in groups){
        const participants = groups[grI].participants
        // if(!participants) break

        // if(!groups[grI].messages){
        //     console.log('THERE ARE NO MESSAGES')
        //     groups[grI].hasUnseenState = false
        // }
        
        for(let prI in participants){
            if(participants[prI].id == user_id){
                const pivot = participants[prI].pivot
                if(pivot.last_message_seen_id < groups[grI].last_msg_id){
                    groups[grI].hasUnseenState = true
                } else {
                    groups[grI].hasUnseenState = false
                }

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
