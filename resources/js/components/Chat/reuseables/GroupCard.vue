<template>
    <div
        v-if="group"
        class="p-2 m-2 rounded-xl cursor-pointer shadow-inner"
        :class="{
            'bg-blue-100 hover:bg-blue-200':  !group.seenState,
            'bg-green-200 hover:bg-green-300': group.seenState,
        }"
    >
        <div v-if="group.model_type == 'PRIVATE'" class="grid grid-cols-7 gap-2">
            <div class="space-y-2 col-span-3">
                
                <div v-if="atLeastTwoParticpants" class="">
                    <div v-for="participant in group.participants">
                        <small-user 
                            v-if="participant.id !== user.id"
                            :user="participant" 
                            :showOnly="'first_name'"
                            :imgCls="'w-10 h-10'" 
                            :userNameCls="'text-coolGray-700'"
                        />
                    </div>
                </div>

                <div v-else class="block p-0.5 text-gray-700 truncate border border-white rounded-lg">
                    {{ txtOneParticipant }}
                </div>
            </div>

            <message-card 
                class="col-span-4 border border-white rounded-lg"
                :message="lastMessage"
            />
        </div>

        <div v-else>
            <div class="grid grid-cols-7 gap-2">
                <div class="space-y-2 col-span-3">
                    <p v-if="group.name" class="block p-0.5 text-gray-700 truncate border border-white rounded-lg">{{group.name}}</p>
                    
                    <div v-if="atLeastTwoParticpants" class="block h-36 border border-white rounded-lg">
                        <vue-scroll :ops="ops">
                            <div v-for="participant in group.participants">
                                <small-user 
                                    v-if="participant.id !== user.id"
                                    :user="participant" 
                                    :showOnly="'first_name'"
                                    :imgCls="'w-8 h-8'" 
                                    :userNameCls="'text-coolGray-700'"
                                />
                            </div>
                        </vue-scroll>
                    </div>

                    <div v-else class="block p-0.5 text-gray-700 truncate border border-white rounded-lg">
                        {{ txtOneParticipant }}
                    </div>
                </div>

                <message-card 
                    class="col-span-4 border border-white rounded-lg"
                    :message="lastMessage"
                />
            </div>
        </div>
    </div>
</template>

<script>

import { mapGetters } from "vuex"
import SmallUser from "./SmallUser.vue"
import MessageCard from "./MessageCard.vue"

export default {
    props: [
        'group_id', 
    ],

    components: {
        'small-user': SmallUser,
        'message-card': MessageCard
    },

    computed:{
        ...mapGetters({ 
            user: "StateUser",
        }),

        group(){ return this.$store.getters['groups/getGroupById'](this.group_id) },

        lastMessage(){ return this.$store.getters['groups/getGroupLastMsg'](this.group_id) },

        atLeastTwoParticpants(){ return Object.keys(this.group.participants).length >= 2 },

    },

    data(){
        return {
            txtOneParticipant: "Only you.",

            ops: {
                scrollPanel: {
                    scrollingX: false
                },
                rail: {
                    background: '#BFDBFF',
                    opacity: 0.6,
                    size: '6px',
                },
                bar: {
                    keepShow: true,
                    background: '#88b7f2',
                }
            },
        }
    },

    created(){

    },

    methods: {

    },
}
</script>