<template>
    <div class="py-2 flex hover:bg-gray-50 items-center cursor-pointer">
        <img
            :src="user.thumbnail"
            alt="no img :/"
            class="w-16 h-16 inline-block ml-0.5 object-cover border border-gray-100 rounded-full"
        >

        <p class="ml-1 text-blue-600 inline-block truncate">
            {{ user.first_name }} {{ user.last_name }}
        </p>
    </div>
</template>

<script>
export default {
    props:[
        'user'
    ],

    data() {
        return {

        }
    },


}
</script>