<template>
    <div class="mx-auto p-3 component-wrapper">
        <div class="card card-default">
            <div class="card-header">Task</div>
            <div class="card-body text-left">
                <ul id="v-tasks-list" class="list-unstyled">
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
                tasks: [],
                filter_user_id: 0,
                last_update: false
            }
        },
        mounted() {
            console.log('Component mounted. list');
            let url = '';
            if (this.filter_user_id > 0)
                url = window.location.origin + '/api/tasks?user_id=' + this.filter_user_id;
            else
                url = window.location.origin + '/api/tasks';

            if(!this.last_update){
                this.getTasks();
                this.last_update = true;
            }
        },
        updated() {
            console.log('updated');
            if(this.last_update){
                this.last_update = false;
                return;
            }

            this.getTasks();
            this.last_update = true;
        },
        methods: {
            newTask() {
                component = 'new-task'
            },
            getTasks(){
                this.filter_user_id = $(".component-wrapper").data('filter_user_id');
                let url = '';
                if (this.filter_user_id > 0)
                    url = window.location.origin + '/api/tasks?user_id=' + this.filter_user_id;
                else
                    url = window.location.origin + '/api/tasks';

                axios.get(url).then(({data}) => {
                    this.tasks = [];
                    data.payload.forEach(task => {
                        this.tasks.push(task);
                    });
                });
            }
        }

    }
</script>
