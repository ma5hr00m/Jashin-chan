<template>
    <ShadowCard cardW="1100" cardH="800" class="absolute flex left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2">
        <div class="relative w-850px h-800px">
            <div class="relative w-full h-80px content border-b-2 border-gray-700">
                <img class="absolute left-5 top-1/2 h-36px transform -translate-y-1/2 " src="../assets/images/logo.svg">
                <h1 class="absolute left-18 top-1/2 font-bold text-28px text-gray-200 transform -translate-y-1/2 ">Wirror</h1>
                <div class="absolute right-5 top-1/2 transform -translate-y-1/2 flex center h-8 w-8 rounded-md text-center bg-cover bg-center bg-bluel">
                        <img class="absolute left-1/2 top-1/2 h-4 transform -translate-x-1/2 -translate-y-1/2" src="../assets/images/return.svg" onclick="returnInput.click()">
                        <input id="returnInput" class="h-8 w-8 rounded-md bg-cover bg-center bg-blued" value=" " type="button" onclick="window.location.href='/login';">
                </div>
            </div>
            <div class="relative w-full h-640px p-5"></div>
            <div class="relative flex w-full h-80px content border-t-2 border-gray-700">
                <form class="relative top-20px flex mx-auto" >
                    <div class="relative left-[-20px] flex center h-10 w-10 rounded-md text-center bg-cover bg-center bg-blued">
                        <img class="absolute left-1/2 top-1/2 h-5 transform -translate-x-1/2 -translate-y-1/2" src="../assets/images/link.svg" onclick="fileInput.click()">
                        <input class="w-full h-full opacity-0" id="fileInput" type="file" name="file">
                    </div>
                    <input class="h-10 w-170 rounded-md p-2 focus:outline-none focus" type="text">
                    <div class="relative right-[-20px] flex center h-10 w-10 rounded-md text-center bg-cover bg-center bg-bluel">
                        <img class="absolute left-1/2 top-1/2 h-5 transform -translate-x-1/2 -translate-y-1/2" src="../assets/images/send.svg" onclick="sendInput.click()">
                        <input id="sendInput" class="h-10 w-10 rounded-md bg-cover bg-center bg-blued" value=" " type="submit">
                    </div>
                </form>
            </div>
        </div>
        <div class="relative w-250px h-800px content border-l-2 border-gray-700">
            <div class="relative w-full h-80px ">
                <img class="absolute left-70px top-1/2 h-7 transform -translate-x-1/2 -translate-y-1/2" src="../assets/images/users.svg">
                <h2 class="absolute top-1/2 transform -translate-y-1/2 left-100px text-gray-200 font-semibold text-16px">Online Users</h2>
            </div>
            <div id="usersList" class="relative w-full h-720px p-5 overflow-y-auto scrollbar-hide">
                <UserBox v-for="user in users" :key="user" :user="user" />
            </div>
        </div>
    </ShadowCard>
</template>

<script>
import ShadowCard from '../components/ShadowCard.vue';
import UserBox from '../components/UserBox.vue';

export default {
    data() {
        return {
            users: ['Cecilia','Alice', 'Tom'],
        }
    },
    components: {
        ShadowCard,
        UserBox
    }
}

let ws = new WebSocket('ws://localhost:8880');

const params = new URLSearchParams(window.location.search);
const username = params.get('username');

ws.onopen = function (event) {
    ws.send(JSON.stringify({
        type: 'login',
        name: username,
    }))
};

ws.onmessage = function (event) {
    let data = JSON.parse(event.data);

    switch (data.type) {
        case 'login':
            this.$set(this.users, this.users.length, data.users);
            break;
    };
};



</script>