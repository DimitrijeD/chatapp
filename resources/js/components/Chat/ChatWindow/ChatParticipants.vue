<template>
    <div class="w-full m-auto">
        <!-- Make overflow when there are many users in this chat -->
        <!-- make status dynamic -->

        <div class="flex justify-start ml-2">
            <!-- Do not show self in chat window heading because this.participants contains all users that belong to this chat-->
            <div
                v-for="user in participants"
                v-if="user.id != userSelf.id"
                class="flex justify-start mr-2"
            >
                <!-- Next div is just green circle as 'user is online' indicator; replace after this is done dynamically -->
                <div class="m-auto">
                    <div class="w-3 h-3 rounded-full bg-green-500 border-1 border-green-400" />
                </div>

                <div class="text-base text-white font-semibold ml-2 hover:border-white border-b-2 border-blue-500">
                    {{ user.firstName }} {{ user.lastName }}
                </div>

            </div>
        </div>

    </div>
</template>

<script>
export default {
    props:[
        'participants',
        'userSelf',
    ],

    data(){
        return{

        }
    },

    mounted() {

    },

    methods:{

    },
}
</script>

