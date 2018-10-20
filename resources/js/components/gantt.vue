<template>
   <div class="gantt-wrapper">
       <div class="list">
           <ul id="v-tasks-list" class="list-unstyled text-left">
               <li><strong>Task</strong></li>
               <li v-for="(value, key) in tasks">{{value.name}}</li>
           </ul>
       </div>
       <div class="segments">
           <ul id="days-list">
           </ul>
           <ul id="" class="list-unstyled">
               <li v-for="(value, key) in tasks" :data-start_date="value.start_date"  :data-due-date="value.due_date"
               class="task">{{value.name}}</li>
           </ul>
       </div>
   </div>
</template>

<script>

    export default {
        name: 'gantt',
        data() {
            return {
                tasks: [],
                days: []
            }
        },
        mounted() {
            console.log('Component mounted. gantt');
            axios.get(window.location.origin + '/api/tasks/gantt').then(({ data }) => {
                this.tasks = [];
                data.tasks.forEach(task => {
                    this.tasks.push(task);
                });
                this.days = [];
                let start_date = new Date(data.start_date);
                let due_date = new Date(data.due_date);
                console.log('Start date ', start_date);
                console.log('Due date ', due_date);
                let day_container = $("#days-list");
                for (let d = start_date; d <= due_date; d.setDate(d.getDate() + 1)) {
                    day_container.append('<li>'+d.getDate()+'/'+(d.getMonth() + 1)+'</li>');
                    console.log('dar '+d.getDate() + ', month '+d.getMonth());
                }
            });
        },
        methods: {
            newTask(){
                component = 'new-task'
            }
        }

    }
</script>
<style lang="scss">
    .gantt-wrapper {
        width: calc(100vw - 45px);
        height: 100vh;
        background-color: #afafaf;
        .list {
            width: 20%;
            height: 100%;
            background-color: #eeeeee;
            position: absolute;
            left: 0;
            top: 0;
            li {
                padding: 5px 10px;
                max-height: 30px;
                &:hover {
                    background-color: #E7E7E7;
                }
            }
        }
        .segments {
            background-color: #fff;
            width: 80%;
            height: 100%;
            position: absolute;
            top: 0;
            right: 0;
            li {
                &.task {
                    background-color: #C1EAFF;
                    color: #C1EAFF;
                    border-radius: 5px;
                    max-height: 28px;
                    padding: 5px 0;
                    margin: 2px 0;
                }
            }
            #days-list {
                list-style: none;
                display: flex;
                padding: 0;
                margin: 0;
                li {
                    width: 30px;
                }
            }
        }
    }
</style>
