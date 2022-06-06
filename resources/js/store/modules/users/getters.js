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

export function excludeByIds(usersIds, excludeIds)
{
    return usersIds.filter((el) => !excludeIds.includes(el))
}
