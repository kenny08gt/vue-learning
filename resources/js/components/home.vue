<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12 p-0">
                <transition name="slide-fade" mode="out-in">
                    <component v-bind:is="component" v-on:users_tasks="showUserTasks($event)" :data-filter_user_id="filter_user_id"></component>
                </transition>
                <div class="mt-2">
                    <button v-on:click="component = 'list';filter_user_id=0;" class="btn btn-info">Tasks</button>
                    <button v-on:click="component = 'new-task'" class="btn btn-info">New task</button>
                    <button v-on:click="component = 'gantt'" id="ganttBtn" class="btn btn-info">Gantt chart</button>
                    <button v-on:click="component = 'kanban'" id="kanbanBtn" class="btn btn-info">Kanban chart</button>
                    <button v-on:click="component = 'users'" id="usersBtn" class="btn btn-info">Users</button>
                </div>
            </div>
            <notifications group="notification-template"/>
        </div>
    </div>
</template>

<script>
    import kanban from './kanban'
    import newTask from './new-task'
    import gantt from './gantt'
    import editTaskModal from './edit-task-modal'
    import users from './users'
    import list from './list'

    export default {
        data() {
            return {
                component: list,
                filter_user_id: 0
            }
        },
        name: 'home',
        mounted() {
            console.log('Component mounted. home')
        },
        methods: {
            showNotification() {
                Vue.notify({
                    group: 'notification-template',
                    title: 'Important message',
                    text: 'Hello user! This is a notification!'
                })
            },
            showUserTasks(user_id) {
                this.filter_user_id = user_id;
                this.component = 'list';

            }
        },
        components: {
            'kanban': kanban,
            'new-task': newTask,
            'gantt': gantt,
            'edit-task-modal': editTaskModal,
            'users': users,
            'list': list,
        }
    }
</script>
<style>
    /* Enter and leave animations can use different */
    /* durations and timing functions.              */
    .slide-fade-enter-active {
        transition: all .3s ease;
    }

    .slide-fade-leave-active {
        transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
    }

    .slide-fade-enter, .slide-fade-leave-to
        /* .slide-fade-leave-active below version 2.1.8 */
    {
        transform: translateX(10px);
        opacity: 0;
    }

    label.error {
        color: red;
        font-size: 0.8rem;
    }

    .list-unstyled {
        list-style: none;
    }
</style>
