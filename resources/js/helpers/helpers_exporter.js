import { createHashMap_OneToOne } from "./OneToOneHashMap/createHashMap_OneToOne.js";
import { createHashMap_OneToMany } from "./OneToManyHashMap/createHashMap_OneToMany.js";
import { updateHashMap_OneToMany } from "./OneToManyHashMap/updateHashMap_OneToMany.js";
import { cleanup_oneToOne } from "./OneToOneHashMap/cleanup_oneToOne.js";

export { 
    createHashMap_OneToMany,
    updateHashMap_OneToMany, 

    createHashMap_OneToOne,
    cleanup_oneToOne 
}