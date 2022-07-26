<template>
    <span class="header-text hover:border-white border-b-2 border-blue-500 cursor-pointer nowrap">{{ group_name }}</span>
</template>

<script>

export default {
    props: [
        'group_name',
    ],

    data(){
        return {

        }
    },
}
</script>