<template>
    <div 
        v-if="showConfig"
        class="z-40 fixed bg-gray-100 c-size border-b-2 border-blue-200"
    >
        <ul class="grid grid-cols-3 text-lg font-light text-gray-800 border-b-2 border-blue-200 mb-1">
            <li 
                v-for="(setting, key) in settings"
                v-bind:key="key"
                @click="openSetting(key)"
                class="py-2 text-center cursor-pointer select-none"
                v-bind:class="{
                    'bg-blue-200 border-b border-blue-300': setting.opened,
                    'bg-gray-100 hover:bg-blue-100': !setting.opened,
                }"
            >
                {{ setting.name }}
            </li>
        </ul>

        <div class="overflow-y-auto sub-size text-base font-light">
            <div v-if="settings.info.opened">
                <info 
                    :group="group"
                />
            </div>

            <div v-if="settings.participants.opened">
                <participants 
                    :group="group"
                />
            </div>

            <div v-if="settings.options.opened">
                <options 
                    :group="group"
                />
            </div>
        </div>       
    </div>    
</template>

<script>
import Info from './Config/Info.vue';
import Participants from './Config/Participants.vue';
import Options from './Config/Options.vue';

export default {
    props: [
        'showConfig', 'group'
    ],

    components: {
        'info': Info,
        'participants': Participants,
        'options': Options,

    },

    data(){
        return {
            settings: {
                info: {
                    name: 'Info',
                    opened: true,
                },

                participants: {
                    name: 'Participants',
                    opened: false, 
                },

                options: {
                    name: 'Options',
                    opened: false, 
                }
            },
        }
    },

    created(){

    },

    mounted(){

    },

    methods: {
        openSetting(key)
        {
            for(let type in this.settings){
                this.settings[type].opened = false;
            }

            this.settings[ key ].opened = true;
        },

    },
}

</script>

<style scoped>
    .c-size{
        width: 461px;
        height: 583px;
    }

    .sub-size{
        width: 461px;
        height: 540px;
    }
</style>