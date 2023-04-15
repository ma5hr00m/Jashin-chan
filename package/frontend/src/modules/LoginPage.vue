<template>
    <ShadowCard :cardW="cardWidth" :cardH="cardHeight" class="absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 transition-all duration-500 ease-in-out">
        <div class="h-full">
            <h1 class="absolute left-6 top-[-60px] italic text-4xl font-extrabold text-bluel antialiased h-min">W<span class="text-yellowl">i</span>rror</h1>
                <div id="login-body" class="absolute flex h-full w-full">
                    <div class="relative w-100 h-full">
                        <img src="../assets/images/logo.svg" class="absolute left-1/2 top-16 h-50 transform -translate-x-1/2" />
                        <div class="absolute left-1/2 top-80 w-max transform -translate-x-1/2">
                            <span class="absolute left-1/2 w-max font-bold text-2xl text-gray-200 transform -translate-x-1/2">PHP Chatroom</span>
                            <br>
                            <a class="absolute left-1/2 top-10 w-max font-medium text-gray-400 transform -translate-x-1/2 hover:underline hover:text-gray-300" href="https://github.com/ma5hr00m/Wirror">
                                Click here to view the repo!
                                <img src="../assets/images/github.svg" class="relative left-1/2 top-2 h-8 transform -translate-x-1/2">
                            </a>
                        </div>
                    </div>
                    <div class="relative h-full w-[calc(100%-400px)] p-10">
                        <h2 class="mb-8 italic text-2xl font-bold text-center text-gray-200">Welcome to Wirror!</h2>
                        <form id="loginForm" @submit.prevent="login" class="relative flex flex-col">
                            <label for="username" class="text-base text-gray-300 mb-2">Username</label>
                            <input type="text" name="username" v-model="loginUsername" class=" box-border p-4 h-10 rounded-md text-sm font-medium bg-dark-200 text-gray-300 focus:outline-none focus">
                            <label for="password" class="text-base text-gray-300 mb-2 mt-4">Password</label>
                            <input type="password" name="password" v-model="loginPassword" class=" box-border p-4 h-10 rounded-md text-sm font-medium bg-dark-200 text-gray-300 focus:outline-none focus">
                            <a class="relative w-30 text-sm text-gray-400 mt-1 font-medium" href="javascript: alert('useless function 😋;')">Forget password?</a>
                            <input type="submit" value="Login" class="mt-12 h-10 rounded-md bg-blued text-light-300 font-bold tracking-wider hover:cursor-pointer">
                        </form>
                        <div class="absolute left-1/2 transform -translate-x-1/2 mt-7">
                            <input type="checkbox" name="condition" class=" mr-1">
                            <label for="condition" class="text-sm text-gray-400 font-medium">Authorizing the <a href="javascript: alert('Not support yet!');" class="text-blued">Service Agreement</a>.</label>
                        </div>
                    </div>
                </div>
                <div id="register-body" class="absolute hidden flex flex-col h-full w-full p-10">
                    <h2 class="mb-8 italic text-2xl font-bold text-center text-gray-200">Create an account</h2>
                    <form id="registerForm" @submit.prevent="register" class="relative flex flex-col">
                        <label for="username" class="text-base text-gray-300 mb-2">Username</label>
                        <input type="text" name="username" v-model="registerUsername" class="box-border p-4 h-10 rounded-md text-sm font-medium bg-dark-200 text-gray-300 focus:outline-none focus">
                        <label for="password" class="text-base text-gray-300 mb-2 mt-4">Password</label>
                        <input type="password" name="password" v-model="registerPassword" class="box-border p-4 h-10 rounded-md text-sm font-medium bg-dark-200 text-gray-300 focus:outline-none focus">
                        <input type="submit" value="Sign up" class="mt-12 h-10 rounded-md bg-blued text-light-300 font-bold tracking-wider hover:cursor-pointer">
                    </form>
                </div>
            <a class="absolute right-6 bottom-[-40px] italic text-base font-bold text-blued cursor-pointer" :onclick="isToLogin ? ToLogin : ToRegister">{{ transLink }}</a>
        </div>
    </ShadowCard>
</template>


<script>
import axios from "axios";
import ShadowCard from '../components/ShadowCard.vue';

export default {
    components: {
        ShadowCard,
    },
    data() {
        return {
            loginUsername: '',
            loginPassword: '',
            registerUsername: '',
            registerPassword: '',
            isToLogin: false,
            transLink: 'Sign up',
            cardWidth: 900,
            cardHeight: 480
        };
    },
    methods: {
        async login() {
            let formData = new FormData();
            formData.append('username', this.loginUsername);
            formData.append('password', this.loginPassword);

            axios.post('http://localhost:8888/api/login.php', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                if (response.data.status_code === 1) {
                    this.$router.push('/chatroom');
                } else {
                    console.log(response.data.status_message);
                }
            })
            .catch(error => {
                console.log(error.response.data);
            });
        },

        async register() {
            let formData = new FormData();
            formData.append('username', this.registerUsername);
            formData.append('password', this.registerPassword);

            axios.post('http://localhost:8888/api/register.php', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then( response => {
                if (response.data.status_code === 1) {
                    window.location.reload();
                } else {
                    console.log(response.data);
                }
            })
            .catch(error => {
                console.log(error.response.date);
            })
        },
        
        ToRegister() {
            this.transLink = '',
            this.isToLogin = true;
            
            setTimeout(() => {
                this.cardWidth = 440;
                this.cardHeight = 410;
                document.getElementById('login-body').style.display = 'none';
            });
            setTimeout(() => {
                    this.transLink = 'Login';
                    document.getElementById('register-body').style.display = 'flex';
                }, 600);
        },
        
        ToLogin() {
            this.transLink = '',
            this.isToLogin = false;

            setTimeout(() => {
                this.cardWidth = 900;
                this.cardHeight = 480;
                document.getElementById('register-body').style.display = 'none';
            });
            setTimeout(() => {
                    this.transLink = 'Sign up';
                    document.getElementById('login-body').style.display = 'flex';
                }, 600);
        }
    }
}
</script>