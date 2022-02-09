<template>
    <div v-if="user">
        <div class="bg-gray-50 shadow-xl py-3">

            <div class="p-2">
                <img class="mx-auto" :src="user.thumbnail" alt="no img">
            </div>

            <div class="p-2">
                <h3 class="text-center text-xl text-gray-900  text-uppercase">{{ user.firstName }} {{ user.lastName }} </h3>

                <div class="text-center text-gray-400 text-xs font-semibold p-1 mb-2">
                    <p>Description</p>
                </div>

                <table class="text-xs my-3">
                    <tr>
                        <td class="p-2 text-gray-500 font-semibold">Email</td>
                        <td class="p-2">{{ user.email }}</td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            user: null,
        };
    },

    mounted(){

        axios.get('/api/user').then((res)=>{
            this.user = res.data;
        });
    },

}
</script>
