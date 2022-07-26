export default function defaultState_Users()
{   
    return {
        users: {}, 
        filterForAddUsers:    [], // contains array of users ID-s that match string passed in /ChatWindow/Config/AddUsers input
        // filterForCreateGroup: [], // contains array of users ID-s that match string passed in /CreateChatGroup input
    }
}