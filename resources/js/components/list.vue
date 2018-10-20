<template>
    <div class="mx-auto p-3">
        <div class="card card-default">
            <div class="card-header">Task</div>
            <div class="card-body text-left">
                <ul id="v-tasks-list list-unstyled">
                    <li v-for="(value, key) in tasks">{{value.name}}</li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: 'list',
        data() {
            return {
                tasks: []
            }
        },
        mounted() {
            console.log('Component mounted. list');
            axios.get(window.location.origin + '/api/tasks').then(({data}) => {
                this.tasks = [];
                data.payload.forEach(task => {
                    this.tasks.push(task);
                });
            });
        },
        methods: {
            newTask() {
                component = 'new-task'
            }
        }

    }
</script>
