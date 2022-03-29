/**
 * Input: {
 *      3: {id: 11, text: 'oldest message'},
 *      1: {id: 20, text: '20'},
 *      0: {id: 23, text: 'Newst message'},
 *      2: {id: 16, text: '16'},
 *      21231232: {ids: 11236, text: 'This message has invalid property "ids" '},   // obj must have prop "id" to pass hash function
 * }
 * 
 * Return: {
 *      11: {id: 11, text: 'oldest message'},
 *      16: {id: 16, text: '16'},
 *      20: {id: 20, text: '20'},
 *      23: {id: 23, text: 'Newst message'},
 * }
 */

export function createHashMap_OneToOne(listedCollection, propertyName)
{
    let hashCollection = {};

    for (let index in listedCollection){
        if (listedCollection.hasOwnProperty(index) && listedCollection[index]?.[propertyName]) {
            const model = listedCollection[index];
            hashCollection[model[propertyName]] = model;
        }
    }

    return hashCollection;
}