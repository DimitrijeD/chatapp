<template>
    <div class="grid inline-grid grid-cols-3 gap-x-3 m-auto pr-3">
        <!-- Chat Window Config -->
        <font-awesome-icon 
            icon="gear" 
            class="cw-btn opacity-80 hover:text-white"
            @click="openConfig()"             
            v-bind:class="{
                'opacity-90': showConfig,
            }"
        />

        <!-- Minimize chat window -->
        <font-awesome-icon 
            icon="angles-down"
            @click="minimize()"
            class="cw-btn hover:text-white"
            v-bind:class="{
                'transform rotate-180': isMinimized,
            }"
        />


        <!-- Close chat window -->
        <svg
            @click="close()"
            xmlns="http://www.w3.org/2000/svg"
            class="cw-btn hover:text-white"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
</template>

<script>
import * as ns from '../../../../store/module_namespaces.js'

export default {
    props:[
        'group_id', 'showConfig', 'isMinimized'
    ],

    methods:{
        minimize() { this.$emit('minimizeWindow') },

        close() { this.$store.dispatch(ns.groupsManager() + '/closeGroup', this.group_id) },

        openConfig()  { this.$emit('openConfig') },
    },

}
</script>

<style>
.cw-btn{
    @apply h-5 w-5 text-blue-100 cursor-pointer;
}
</style>
        