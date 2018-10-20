<template>
    <div class="mx-auto p-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-default text-left">
                        <div class="card-header">New task</div>
                        <form action="" class="card-body" v-on:submit.prevent="createTaskForm">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" v-model="task_obj.name">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" class="form-control"
                                          v-model="task_obj.description"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="start_date">Start date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                       v-model="task_obj.start_date">
                            </div>
                            <div class="form-group">
                                <label for="due_date">Due date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control"
                                       v-model="task_obj.due_date">
                            </div>
                            <button>Submit</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    const now = new Date().toJSON().slice(0, 10).replace(/-/g, '/');
    let task_obj = {
        name: '',
        description: '',
        start_date: window.now,
        due_date: window.now
    };
    export default {
        name: 'newTask',
        mounted() {
            console.log('Component mounted. new-task');
        },
        data() {
            return {
                task_obj
            }
        },
        methods: {
            createTaskForm() {
                this.cleanErrorsForm();
                $.ajax({
                    url: window.location.origin + '/api/task',
                    type: 'post',
                    data: task_obj,
                    success: function (data) {
                        Vue.notify({
                            group: 'notification-template',
                            title: 'Success',
                            text: 'Task created!',
                            type: 'success',
                            duration: '3500'
                        });
                        $("#listBtn").trigger('click');
                    },
                    error: function (error) {
                        let errors = $.parseJSON(error.responseText);
                        Vue.notify({
                            group: 'notification-template',
                            title: 'Error',
                            text: errors.message,
                            type: 'error',
                            duration: '3500'
                        });
                        $.each(errors['errors'], function (key, value) {
                            console.log('key ' + key);
                            console.log('value ' + value);
                            $("#" + key).after('<label class="error">' + value + '</label>');

                        });
                    }
                })
            },
            cleanErrorsForm() {
                $("form label.error").each(function (index, item) {
                    item.remove();
                })
            }
        },
    }
</script>
