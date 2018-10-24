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
                <li v-for="(value, key) in tasks" :data-start_date="value.start_date" :data-due_date="value.due_date">
                    <span @mouseover="taskSegmentHover" @mouseout="taskSegmentHoverOut" @click="taskSegmentClick"
                          class="task">{{value.name}}</span>
                </li>
            </ul>
        </div>

        <div class="tooltip-wrapper">
            <span class="fa fa-pencil" @click="editTask"></span>
            <span class="fa fa-trash" @click="deleteTask"></span>
        </div>
        <div class="modal" tabindex="-1" role="dialog" id="editTaskModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Task</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid animi, aperiam corporis
                            earum eveniet explicabo fugiat illo in inventore, ipsum iure nostrum rerum similique
                            sint.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        name: 'gantt',
        data() {
            return {
                tasks: [],
                days: [],
                start_date: '',
                due_date: '',
                oneDay: 24 * 60 * 60 * 1000 // hours*minutes*seconds*milliseconds
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

                    $(item).find('.task').css('margin-left', left_margin + 'px');
                    $(item).find('.task').css('width', width + 'px');
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
            taskSegmentClick(event) {
                console.log('event', event);
                $("#segments-list li span.task .tooltip-wrapper").remove();
                let segment = $(event.target);

                let tooltip = $(".tooltip-wrapper").clone();
                tooltip.css('display', 'block');
                let width = segment.width();
                let element_width = 68;
                console.log('element width ' + element_width);
                console.log('width ' + width);
                // tooltip.css('top', '-35px');
                // tooltip.css('left', ((width / 2) - (element_width / 2)));
                tooltip.css('top', event.layerY - 50);
                tooltip.css('left', event.layerX - (element_width / 2));
                segment.prepend(tooltip);
            },
            editTask() {
                alert('Edit task');
                $("#editTaskModal").modal('show');
            },
            deleteTask() {
                alert('Delete task');
            }
        }
    }

    $('body').on('click', '.tooltip-wrapper .fa-pencil', function (e) {
        alert('edit pencil click');
    })

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
                    position: relative;
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
                    min-width: 45px;
                    border-right: 0.1px solid black;
                    height: 30px;
                }
            }
        }
        .tooltip-wrapper {
            display: none;
            background-color: #fbffb8;
            color: #000;
            position: absolute;
            padding: 10px;
            border-radius: 10px;
            width: 68px;
            &:after {
                content: "";
                position: absolute;
                right: 100%;
                top: 35px;
                left: 20px;
                border-top: 13px solid #fbffb8;
                border-right: 13px solid transparent;
                border-bottom: 13px solid transparent;
                border-left: 13px solid transparent;
            }
            span {
                margin: 0 5px;
            }
        }
    }
</style>
