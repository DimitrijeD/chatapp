<template>
    <div class="mr-1.5 mb-0.5 ">
        <span class="p-1.5 text-blue-400 text-sm hover:text-blue-500">
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
