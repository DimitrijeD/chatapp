<template>
    <div
        v-if="group"
        class="p-2 rounded-xl cursor-pointer shadow-inner"
        :class="{
            'bg-blue-200 hover:bg-blue-300':    this.seen,
            'bg-green-200 hover:bg-green-300': !this.seen,
        }"
    >
        <p 
            v-if="group.name" 
            class="p-0.5 mb-2 text-gray-700 text-center truncate gr-card-border">
            {{group.name}}
        </p>

        <div class="gr-card-h grid grid-cols-7 gap-2">
            <div class="space-y-2 col-span-3">
                
                <div v-if="atLeastTwoParticpants" class="gr-card-h gr-card-border">
                    <vue-scroll :ops="ops">
                        <div v-for="p in group.participants">
                            <small-user 
                                v-if="p.id !== user.id"
                                class="ml-1"
                                :user="p" 
                                :showOnly="'first_name'"
                                :imgCls="'w-8 h-8'" 
                                :userNameCls="'text-coolGray-600'"
                            />
                        </div>
                    </vue-scroll>
                </div>

                <div v-else class="p-0.5 text-gray-700 truncate gr-card-border">
                    {{ txtOneParticipant }}
                </div>
            </div>

            <div class="col-span-4 gr-card-border">
                <message-card 
                    :message="lastMessage"
                    :heightCls="'gr-card-h'"
                />
            </div>
        </div>
    </div>
</template>

<script>

import { mapGetters } from "vuex"
import SmallUser from "./SmallUser.vue"
import MessageCard from "./MessageCard.vue"
import * as ns from "../../../store/module_namespaces.js"

export default {
    props: [
        'group_id', 
    ],

    components: {
        'small-user': SmallUser,
        'message-card': MessageCard,
    },

    computed:{
        ...mapGetters({ 
            user: "user",
        }),

        group(){ return this.$store.getters[this.gm_ns + '/state'] },

        lastMessage(){ return this.$store.getters[this.gm_ns + '/last_message'] },

        atLeastTwoParticpants(){ return Object.keys(this.group.participants).length >= 2 },

        seen(){ return this.$store.getters[`${this.gm_ns}/seen`] },
    },

    data(){
        return {
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
            txtOneParticipant: "Only you.",
            gm_ns: ns.groupModule(this.group_id),
        }
    },

    created(){

    },

    methods: {
        hasLastMessage(msg){ return msg.hasOwnProperty('id') ? true : false },
        
    },
}
</script>

<style>

.gr-card-h{
    height: 160px;
}

.gr-card-border{
    box-shadow: inset 0px 0px 5px 1px rgb(0 0 0 / 11%);
    @apply border border-white rounded-lg;
}

</style>