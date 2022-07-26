export default () => {
    return {
        id: null,
        messages: {},
        model_type: null,
        name: null,
        participants: {},
        created_at: null,
        updated_at: null,
        messages_tracker: {
            gotEarliestMsg: false,
            last_message: {},
            seen: null
        },
        whoSawWhat: {}
    }
}