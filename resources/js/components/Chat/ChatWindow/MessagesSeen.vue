<template>
    <div class="mr-2 mb-1 ">
        <span class="p-1.5 text-blue-400 text-sm">
            {{ firstName }} seen
        </span>
    </div>
</template>

<script>
export default {
    props:[
        'firstName'
    ],

}
</script>
