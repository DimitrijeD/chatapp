<template>
    <div 
        v-if="showConfig"
        class="z-40 fixed bg-gray-50 border border-gray-300 box-border"
    >
        <!-- Create Nav bar for config -->
        <ul class="grid text-lg font-light text-gray-600 border-b border-gray-300 mb-3"
            :class="'grid-cols-' + Object.keys(settings).length"
        >
            <li 
                v-for="(setting, key) in settings"
                v-bind:key="key"
                @click="openSetting(key)"
                class="text-center cursor-pointer select-none p-1.5"
                v-bind:class="{
                    'bg-blue-100 border-b border-blue-400': setting.opened,
                    'bg-gray-100 hover:bg-blue-100': !setting.opened,
                }"
            >
                {{ setting.name }}
            </li>
        </ul>
        <!-- End Nav Bar -->

        <!-- Create Config component if opened, close otherwise -->
        <div class="text-base font-light m-2 flex-1">
            <add-users 
                v-if="settings.add_users.opened" 
                :group="group"  
                :role="role"  
            />

            <info 
                v-if="settings.info.opened" 
                :group="group" 
                :role="role"
            />

            <participants 
                v-if="settings.participants.opened" 
                :group="group" 
                :role="role"
            />

            <options 
                v-if="settings.options.opened"
                :group="group" 
                :role="role"
            />
        </div>
        <!-- End Create Config component -->
    </div>    
</template>

<script>
import Info         from './Config/Info.vue'
import Participants from './Config/Participants.vue'
import Options      from './Config/Options.vue'
import AddUsers     from './Config/AddUsers.vue'

export default {
    props: [
        'showConfig', 'group'
    ],

    components: {
        'add-users': AddUsers,
        'participants': Participants,
        'info': Info,
        'options': Options,
    },

    data(){
        return {
            user: this.$store.state.auth.user,

            settings: {
                add_users: {
                    name: 'Add Users',
                    opened: true, 
                },

                info: {
                    name: 'Info',
                    opened: false,
                },

                participants: {
                    name: 'Participants',
                    opened: false, 
                },

                options: {
                    name: 'Options',
                    opened: false, 
                },
            },

        }
    },

    computed: {
        role(){
            return this.$store.getters['groups/getUserRole']({ group_id: this.group.id, user_id: this.user.id })
        },
    },

    created(){
        console.log() 
    },

    mounted(){
  
    },

    methods: 
    {
        openSetting(key)
        {
            for(let type in this.settings){
                this.settings[type].opened = false
            }

            this.settings[ key ].opened = true
        },

    },
}

</script>

<style scoped>

</style>