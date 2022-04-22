export function sort_filteredGroups_by_updated_at(groups)
{
    return groups.sort(function(a,b){
        // Turn your strings into dates, and then subtract them
        // to get a value that is either negative, positive, or zero.
        return new Date(b.updated_at) - new Date(a.updated_at);
      }).reverse();
}