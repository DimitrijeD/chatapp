export function prepareUsersSearchRequest(data, possesedIds)
{
    data.i_have_ids = possesedIds

    if('exclude' in data && data.exclude.length)
        data.i_have_ids = possesedIds.concat(data.exclude.filter((item) => possesedIds.indexOf(item) < 0)); // merge Ids without duplicates

    return data
}