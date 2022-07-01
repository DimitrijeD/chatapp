<template>
    <div 
        class="py-2 flex items-center cursor-pointer"
        :class="[getGap, getOveriddenClasses]"
    >
        <img
            :src="user.thumbnail"
            alt="no img :/"
            class="w-16 h-16 inline-block ml-0.5 object-cover border border-gray-100 rounded-full"
        > 

        <p class="inline-block truncate">
            {{ user.first_name }} {{ user.last_name }}
        </p>
    </div>
</template>

<script>
export default {
    props:[
        'user', 'img_name_gap', 'overrideClasses'
    ],

    computed: {
        getGap(){
            return this.img_name_gap 
                ? "space-x-" + this.img_name_gap 
                : this.defaultGap
        },

        getOveriddenClasses(){
            return this.overrideClasses 
                ? this.overrideClasses 
                : this.defaultOverridenClasses
        }
    },

    data() {
        return {
            defaultGap: "space-x-1",
            defaultOverridenClasses: "hover:bg-gray-50 text-blue-600"
        }
    },


}
</script>