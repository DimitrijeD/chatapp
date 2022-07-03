<template>
    <div :class="[static.layout, getLayoutCls]" v-if="user">
        <img
            :src="user.thumbnail"
            :class="[static.img, getImgCls]"
            alt="no img :/"
        > 

        <p :class="[static.userName, getUserNameCls]">
            {{ getUserNameDisplay }}
        </p>
    </div>
</template>

<script>
export default {
    props:[
        'user', 'layoutCls', 'imgCls', 'userNameCls', 'showOnly' 
    ],

    computed: {
        getLayoutCls(){ return this.layoutCls ? this.layoutCls : this.default.layout },

        getImgCls(){ return this.imgCls ? this.imgCls : this.default.img },

        getUserNameCls(){ return this.userNameCls ? this.userNameCls : this.default.userName },

        // expects "first_name", "last_name" or ""
        getUserNameDisplay(){ 
            if(!this.showOnly) return `${this.user.first_name} ${this.user.last_name}`

            return this.user[this.showOnly]
        }
    },

    data() {
        return {
            default:{ 
                layout: "py-2 space-x-1",
                img: "w-16 h-16",
                userName: "truncate",
            },

            static: {
                layout: "flex items-center cursor-pointer",
                img: "inline-block ml-0.5 object-cover border border-gray-100 rounded-full",
                userName: "inline-block font-semibold",
            },
        }
    },


}
</script>