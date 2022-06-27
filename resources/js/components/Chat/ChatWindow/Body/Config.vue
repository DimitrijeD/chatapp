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
                    'bg-gray-100 hover:bg-blue-100':       !setting.opened,
                }"
            >
                {{ setting.name }}
            </li>
        </ul>
        <!-- End Nav Bar -->

        <!-- Create Config component if opened, close otherwise -->
        <div class="text-base font-light m-2 flex-1">
            <add-users 
                v-if="settings.hasOwnProperty('add_users') && settings.add_users.opened" 
                :group="group"  
                :role="userRole"
                :permissions="permissions"  
            />

            <info 
                v-if="settings.hasOwnProperty('info') && settings.info.opened" 
                :group="group" 
                :role="userRole"
                :permissions="permissions"
            />

            <participants 
                v-if="settings.hasOwnProperty('participants') && settings.participants.opened" 
                :group="group" 
                :role="userRole"
                :permissions="permissions"
            />

            <options 
                v-if="settings.hasOwnProperty('options') && settings.options.opened"
                :group="group" 
                :role="userRole"
                :permissions="permissions"
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
        'showConfig', 'group', 'permissions', 'userRole'
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
                    opened: false, 
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

    created(){
        this.createPermissibleSettings()
        this.setFirstOpenedConfig()
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

            this.settings[key].opened = true
        },

        /**
         * Exclue settings from showing in Config nav if their role is not allowing certain action
         */
        createPermissibleSettings()
        {
            if(!this.permissions.add.length)
                delete this.settings['add_users']

            if(this.group.model_type == "PRIVATE")
                delete this.settings['participants']
        },

        setFirstOpenedConfig()
        {
            let oneWillBeOpened = false

            for(let settingIndex in this.settings){
                if(this.settings[settingIndex].opened){
                    oneWillBeOpened = true
                    break
                }
            }

            // if no settings are opened, then select first one in object as default opened setting after user clicks config cog
            if(!oneWillBeOpened){
                let firstOpened = Object.keys(this.settings)[0]
                this.settings[firstOpened].opened = true
            }
        },

    },
}

</script>

<style>

</style>