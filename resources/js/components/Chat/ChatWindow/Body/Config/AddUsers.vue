<template>
    <div class="select-none">
        <div>
            <search-input 
                :actions="input.actions"
                :exclude="excludeUsersFromSearch"
                :placeholder="input.placeholder"
            />
        </div>

        <div class="flex gap-2 my-2 add-users-height">
            <div class="flex-1 p-1 bg-blue-100 border-2 border-blue-200">
                <vue-scroll :ops="ops">
                    <ul> 
                        <li v-for="(id, index) in listUsers" :key="index">
                            <small-user 
                                :user="getUser(id)"
                                @click.native="add(id)"
                                :userNameCls="'text-blue-600 space-x-2'"
                            /> 
                        </li>
                    </ul>
                </vue-scroll>
            </div>

            <div class="flex-1 p-1 bg-blue-100 border-2 border-blue-200">
                <vue-scroll :ops="ops">
                    <div v-if="addedUsersIds.length == 0">
                        <p class="text-center italic font-extralight text-gray-500">
                            Selected users will appear in this list
                        </p>
                    </div>
                    <ul> 
                        <li v-for="(id, index) in addedUsersIds" :key="index">
                            <small-user 
                                :user="getUser(id)"
                                @click.native="remove(id)"
                                class="bg-green-100 hover:bg-red-300"
                                :userNameCls="'text-sky-600 space-x-2'"
                            />
                        </li>
                    </ul>
                </vue-scroll>
            </div>
        </div>

        <div>
            <button     
                class="py-3 w-full font-light text-gray-500"
                :class="{
                    'text-white bg-green-400 hover:bg-green-500 ': anySelected, 
                    'disabled text-gray-600 bg-gray-300': !anySelected,
                }"
                @click="addParticipants"
            >{{ btnTxt }}</button>
        </div>

    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import SearchInput from '../../../reuseables/SearchInput.vue'
import SmallUser from '../../../reuseables/SmallUser.vue'
import * as ns from '../../../../../store/module_namespaces.js'

export default {
    props: [
        'group', 'permissions'
    ],

    components: {
        'search-input': SearchInput,
        'small-user': SmallUser,
    },

    data() {
        return {
            addedUsersIds: [],
            input: {
                actions: {
                    api: ns.users() + '/searchForAddUsersInApi',
                    store: ns.users() + '/searchForAddUsersInStore'
                },
                placeholder: "Find users to add",
            },

            ops: {
                vuescroll: {},
                scrollPanel: {
                    scrollingX: false
                },
                rail: {},
                bar: {
                    keepShow: true
                }
            },

            gm_ns: ns.groupModule(this.group.id),
        }
    },

    computed: {
        ...mapGetters({ 
            user: "user",
        }),

        listUsers(){ return this.$store.getters[ns.users() + '/getFilterForAddUsers'] },

        anySelected() {return this.addedUsersIds.length ? true : false}, 

        btnTxt(){
            let numSelected = this.addedUsersIds.length
            if(numSelected == 0)                    return 'Select at least one user'
            if(numSelected == 1)                    return 'Add selected user'
            if(numSelected > 1 && numSelected <= 4) return 'Add selected users'
            if(numSelected > 4)                     return `Add all ${numSelected} selected users`
        },

        excludeUsersFromSearch(){ return Object.keys(this.$store.getters[this.gm_ns + '/participants']) },
    },

    methods: 
    {
        getUser(id){
            return this.$store.getters[ns.users() + '/getById'](id)
        },
        
        add(id){
            if(!this.addedUsersIds.includes(id)) this.addedUsersIds.push(id)
        },

        remove(id){
            if(this.addedUsersIds.includes(id)) this.addedUsersIds.splice(this.addedUsersIds.indexOf(id), 1)
        },

        addParticipants(){
            if(!this.addedUsersIds.length) return

            this.$store.dispatch(this.gm_ns + '/addParticipants', {
                addedUsersIds: this.addedUsersIds,
                massAssignRolesTo: this.group.model_type == "PUBLIC_CLOSED" ? "LISTENER" : "PARTICIPANT"
            }).then(() =>{
                this.addedUsersIds = []
            })
        },
    },
}
</script>

<style>
.add-users-height{
    height: 405px;
}

.disabled:hover {
    cursor:not-allowed
}
</style>