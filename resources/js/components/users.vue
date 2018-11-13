<template>
    <div class="p-3">
        <div class="card card-default mx-auto" style="max-width: 300px">
            <div class="card-header">Users</div>
            <div class="card-body text-left">
                <ul class="list-unstyled">
                    <li v-for="(user) in users">
                        <div class="d-flex user-wrapper">
                            <div class="left">
                                <div class="profile-pic"
                                     :style="'background-image: url('+user.profile_picture+');'">
                                </div>
                            </div>
                            <div class="right">
                                <strong>{{user.name}}</strong><br>
                                <a href="#nogo" v-on:click="seeTasks" :data-id="user.id">Tasks assigned: {{user.tasks.length}}</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: 'users',
        data() {
            return {
                users: []
            }
        },
        mounted() {
            console.log('Component mounted. users');
            axios.get(window.location.origin + '/api/users').then(({data}) => {
                this.users = [];
                data.users.forEach(user => {
                    this.users.push(user);
                });
            });
        },
        methods: {
            seeTasks(event){
                let target = event.target;
                this.$emit('users_tasks', $(target).data('id'));
            }
        }

    }
</script>
<style lang="scss">
    .user-wrapper {
        margin: 15px;
        .left {
            margin-right: 5px;
            .profile-pic {
                width: 50px;
                height: 50px;
                margin-right: 5px;
                background-origin: center center;
                -webkit-background-size: cover;
                background-size: cover;
                -webkit-border-radius: 3px;
                -moz-border-radius: 3px;
                border-radius: 3px;
            }
        }
    }
</style>
