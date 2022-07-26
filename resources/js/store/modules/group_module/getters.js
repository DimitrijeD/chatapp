import * as h from '../../functions/helpers.js'

const getters = 
{
    state:        (state) => state, // return everything
    id:           (state) => state.id,
    messages:     (state) => state.messages,
    model_type:   (state) => state.model_type,
    name:         (state) => state.name,
    participants: (state) => state.participants,
    created_at:   (state) => state.created_at,
    updated_at:   (state) => state.updated_at,

    reachedEarliestMsgId: (state) => state.messages_tracker.reachedEarliestMsgId,
    last_message:         (state) => state.messages_tracker.last_message,
    seen:                 (state) => state.messages_tracker.seen,

    getMyParticipants:       (state) =>                     state.participants,
    getUserRole:             (state) => (participant_id) => state.participants[participant_id].pivot.participant_role,
    getParticipant:          (state) => (participant_id) => state.participants[participant_id],
    getParticipantThumbnail: (state) => (participant_id) => state.participants[participant_id].thumbnail,

    getPossesedNumberOfMessagesInGroup: (state) => Object.keys(state.messages).length,
    getLastPossesedMsgId: (state) => h.getMinObjKey(state.messages),

}

export default getters 