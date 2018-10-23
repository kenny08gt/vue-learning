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
            <ul id="segments-list" class="list-unstyled">
                <li v-for="(value, key) in tasks" :data-start_date="value.start_date" :data-due_date="value.due_date"
                    @mouseover="taskSegmentHover" @mouseout="taskSegmentHoverOut"><span
                    class="task" :id="'task_'+value.id">{{value.name}}</span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import Draggable from '@shopify/draggable/lib/draggable';

    export default {
        name: 'gantt',
        data() {
            return {
                tasks: [],
                days: [],
                start_date: '',
                due_date: '',
                oneDay: 24 * 60 * 60 * 1000, // hours*minutes*seconds*milliseconds
                current_task_x_position: -1,
                initial_task_x_position: -1
            }
        },
        mounted() {
            console.log('Component mounted. gantt');
            this.loadTasks();
        },
        methods: {
            loadTasks() {
                const vue_scope = this;
                axios.get(window.location.origin + '/api/tasks/gantt').then(({data}) => {
                    vue_scope.tasks = [];
                    data.tasks.forEach(task => {
                        vue_scope.tasks.push(task);
                    });
                    vue_scope.days = [];
                    vue_scope.start_date = new Date(data.start_date);
                    vue_scope.due_date = new Date(data.due_date);
                    let day_container = $("#days-list");
                    for (let d = new Date(vue_scope.start_date.valueOf()); d <= new Date(vue_scope.due_date.valueOf() + vue_scope.oneDay); d.setDate(d.getDate() + 1)) {
                        day_container.append('<li>' + d.getDate() + '/' + (d.getMonth() + 1) + '</li>');
                    }

                }).then(() => {
                    this.orderTasks();
                    this.enableDrag();
                });
            },
            orderTasks() {
                console.log('order tasks ');
                const vue_scope = this;
                $("#segments-list li").each(function (index, item) {
                    const task_start_date = new Date($(item).data('start_date'));
                    const task_due_date = new Date($(item).data('due_date'));
                    const left_margin = (vue_scope.datediff(vue_scope.start_date, task_start_date) + 1) * 45;
                    const width = (vue_scope.datediff(task_start_date, task_due_date) + 1) * 45;

                    let task = $(item).find('.task');
                    task.css('margin-left', left_margin + 'px');
                    task.css('width', width + 'px');
                });
            },
            datediff(firstDate, secondDate) {

                return Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (this.oneDay)));
            },
            taskSegmentHover(event) {
                event.target.classList.add('hovered');
            },
            taskSegmentHoverOut(event) {
                event.target.classList.remove('hovered');
            },
            enableDrag() {
                const draggable = new Draggable(document.querySelectorAll("#segments-list li"), {
                    draggable: 'span'
                });
                const vue_scope = this;
                draggable.on('drag:start', (e,) => {
                    console.log('start drag', e);
                });
                draggable.on('drag:move', (e) => {

                    let clientX = e.data.sensorEvent.data.clientX;
                    const original_task = $(e.data.originalSource);
                    original_task.css('margin-left', 'calc('+(clientX - 30) + 'px - 20%)');

                });
                draggable.on('drag:stop', (e) => {
                    const task = $(e.data.source);
                    const original_task = $(e.data.originalSource);

                });
            }
        }
    }

    $("body").on('hove', '#segments-list li span', function (event) {
        $(this).css('background', 'red');
    });
</script>
<style lang="scss">
    .gantt-wrapper {
        width: calc(100vw - 45px);
        height: 100vh;
        background-color: #afafaf;
        position: relative;
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
                border-bottom: 1px solid black;
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
                border-bottom: 1px solid black;
                .task {
                    background-color: #C1EAFF;
                    color: transparent;
                    border-radius: 5px;
                    max-height: 25px;
                    padding: 5px 0;
                    margin: 2px 0;
                    display: block;
                    cursor: pointer;
                    &.hovered {
                        background-color: red;
                    }
                }
            }
            #days-list {
                list-style: none;
                display: flex;
                padding: 0;
                margin: 0;
                li {
                    width: 45px;
                    border-right: 0.1px solid black;
                    height: 30px;
                }
            }
        }
    }
</style>
