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
                @click="addUsers"
            >{{ btnTxt }}</button>
        </div>

    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import SearchInput from '../../../reuseables/SearchInput.vue'
import SmallUser from '../../../reuseables/SmallUser.vue'

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
                    api: 'searchForAddUsersInApi',
                    store: 'searchForAddUsersInStore'
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

        }
    },

    computed: {
        ...mapGetters({ 
            user: "StateUser",
        }),

        listUsers(){ return this.$store.getters['users/getFilterForAddUsers'] },

        anySelected() {return this.addedUsersIds.length ? true : false}, 

        btnTxt(){
            let numSelected = this.addedUsersIds.length
            if(numSelected == 0)  return 'Select at least one user'
            if(numSelected == 1) return 'Add selected user'
            if(numSelected > 1 && numSelected <= 4) return 'Add selected users'
            if(numSelected > 4) return `Add all ${numSelected} selected users`
        },

        excludeUsersFromSearch(){
            let participants = this.$store.getters['groups/getMyParticipants'](this.group.id)
            let arrayOfIds = []

            for(let i in participants){
                arrayOfIds.push(participants[i].id)
            }

            return arrayOfIds
        }
    },

    methods: 
    {
        getUser(id)
        {
            return this.$store.getters['users/getById'](id)
        },
        
        add(id)
        {
            if(!this.addedUsersIds.includes(id)) this.addedUsersIds.push(id)
        },

        remove(id)
        {
            if(this.addedUsersIds.includes(id))
                this.addedUsersIds.splice(this.addedUsersIds.indexOf(id), 1)
        },

        addUsers()
        {
            if(!this.addedUsersIds.length) return

            this.$store.dispatch('groups/addParticipants', {
                addedUsersIds: this.addedUsersIds,
                group_id: this.group.id,
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